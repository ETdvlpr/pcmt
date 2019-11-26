<?php

declare(strict_types=1);

namespace Pcmt\PcmtProductBundle\Service;

use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Pcmt\PcmtProductBundle\Entity\DraftInterface;
use Pcmt\PcmtProductBundle\Exception\DraftViolationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductModelDraftApprover extends DraftApprover
{
    /** @var ProductModelFromDraftCreator */
    protected $creator;

    /** @var SaverInterface */
    private $saver;

    /** @var ValidatorInterface */
    private $validator;

    public function setCreator(ProductModelFromDraftCreator $creator): void
    {
        $this->creator = $creator;
    }

    public function setSaver(SaverInterface $saver): void
    {
        $this->saver = $saver;
    }

    public function setValidator(ValidatorInterface $productModelValidator): void
    {
        $this->validator = $productModelValidator;
    }

    public function approve(DraftInterface $draft): void
    {
        $productModel = $this->creator->getProductModelToSave($draft);

        $violations = $this->validator->validate($productModel);
        if (0 === $violations->count()) {
            $this->saver->save($productModel);
        } else {
            throw new DraftViolationException($violations, $productModel);
        }

        $this->updateDraftEntity($draft);
    }
}