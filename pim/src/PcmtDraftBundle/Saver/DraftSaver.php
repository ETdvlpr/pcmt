<?php
/**
 * Copyright (c) 2019, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Saver;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Doctrine\ORM\EntityManagerInterface;
use PcmtDraftBundle\Entity\AbstractDraft;
use PcmtDraftBundle\Entity\DraftInterface;
use PcmtDraftBundle\Entity\ExistingObjectDraftInterface;
use PcmtDraftBundle\Exception\DraftSavingFailedException;
use PcmtDraftBundle\Exception\DraftViolationException;
use PcmtDraftBundle\Service\Draft\DraftExistenceChecker;
use PcmtDraftBundle\Service\Draft\GeneralObjectFromDraftCreator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DraftSaver implements SaverInterface
{
    public const OPTION_NO_VALIDATION = 'no_validation';

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var DraftExistenceChecker */
    private $draftExistenceChecker;

    /** @var ValidatorInterface */
    private $productValidator;

    /** @var ValidatorInterface */
    private $productModelValidator;

    /** @var GeneralObjectFromDraftCreator */
    private $creator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        DraftExistenceChecker $draftExistenceChecker,
        ValidatorInterface $productValidator,
        ValidatorInterface $productModelValidator,
        GeneralObjectFromDraftCreator $creator
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->draftExistenceChecker = $draftExistenceChecker;
        $this->productValidator = $productValidator;
        $this->productModelValidator = $productModelValidator;
        $this->creator = $creator;
    }

    /**
     * {@inheritdoc}
     */
    public function save($draft, array $options = []): void
    {
        if (empty($options[self::OPTION_NO_VALIDATION])) {
            $this->validateDraft($draft);
        }
        $this->eventDispatcher->dispatch(StorageEvents::PRE_SAVE, new GenericEvent($draft, $options));
        $this->entityManager->persist($draft);
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(StorageEvents::POST_SAVE, new GenericEvent($draft, $options));
    }

    protected function validateDraft(object $draft): void
    {
        $this->checkIfDraftIsInstanceOfDraftInterface($draft);
        $this->checkIfDraftCouldBeSaved($draft);
        $this->validateObjectToSave($draft);
        $this->checkIfThereIsOtherDraftForThisObject($draft);
    }

    private function checkIfDraftIsInstanceOfDraftInterface(object $draft): void
    {
        if (!$draft instanceof DraftInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects a %s, "%s" provided',
                    DraftInterface::class,
                    get_class($draft)
                )
            );
        }
    }

    private function checkIfDraftCouldBeSaved(DraftInterface $draft): void
    {
        if (AbstractDraft::STATUS_APPROVED === $draft->getStatus()) {
            throw DraftSavingFailedException::draftAlreadyApproved();
        }

        if (AbstractDraft::STATUS_REJECTED === $draft->getStatus()) {
            throw DraftSavingFailedException::draftAlreadyRejected();
        }
    }

    private function validateObjectToSave(DraftInterface $draft): void
    {
        $objectToSave = $this->creator->getObjectToSave($draft);
        if (!$objectToSave) {
            throw DraftSavingFailedException::noCorrespondingObject();
        }

        if ($objectToSave instanceof ProductInterface) {
            $violations = $this->productValidator->validate($objectToSave, null, [
                'Default',
                'creation',
            ]);
        } elseif ($objectToSave instanceof ProductModelInterface) {
            $violations = $this->productModelValidator->validate($objectToSave, null, [
                'Default',
                'creation',
            ]);
        }

        if (0 !== $violations->count()) {
            throw new DraftViolationException($violations, $objectToSave);
        }
    }

    private function checkIfThereIsOtherDraftForThisObject(DraftInterface $draft): void
    {
        if (!$draft->getId() && $draft instanceof ExistingObjectDraftInterface) {
            if ($this->draftExistenceChecker->checkIfDraftForObjectAlreadyExists($draft)) {
                throw new \InvalidArgumentException(
                    'There is already a draft for this object'
                );
            }
        }
    }
}