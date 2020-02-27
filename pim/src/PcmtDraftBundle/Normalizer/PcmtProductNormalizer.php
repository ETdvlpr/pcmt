<?php
/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Normalizer;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Doctrine\ORM\EntityManagerInterface;
use PcmtDraftBundle\Entity\AbstractDraft;
use PcmtDraftBundle\Service\Helper\UnexpectedAttributesFilter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PcmtProductNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $productNormalizer;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UnexpectedAttributesFilter */
    private $attributesFilter;

    public function __construct(
        NormalizerInterface $productNormalizer,
        EntityManagerInterface $entityManager,
        UnexpectedAttributesFilter $attributesFilter
    ) {
        $this->productNormalizer = $productNormalizer;
        $this->entityManager = $entityManager;
        $this->attributesFilter = $attributesFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($product, $format = null, array $context = [])
    {
        /** @var ProductInterface $product */
        $data = $this->productNormalizer->normalize($product, $format, $context);

        if ($context['include_draft_id'] ?? false) {
            $draft = $this->entityManager->getRepository(AbstractDraft::class)->findOneBy(
                [
                    'product' => $product,
                    'status'  => AbstractDraft::STATUS_NEW,
                ]
            );

            $data['draftId'] = $draft ? $draft->getId() : 0;
        }

        if ($this->hasToFilterUnexpectedValues($context, $product, $data)) {
            $data['values'] = $this->attributesFilter->filter($product, $data['values']);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->productNormalizer->supportsNormalization($data, $format);
    }

    private function hasToFilterUnexpectedValues(array $context, ProductInterface $product, array $data): bool
    {
        return ($context['import_via_drafts'] ?? false)
            && $product->isVariant()
            && isset($data['values']);
    }
}