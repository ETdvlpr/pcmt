<?php

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Service\Draft;

use Akeneo\UserManagement\Component\Model\UserInterface;
use PcmtDraftBundle\Entity\AbstractDraft;
use PcmtDraftBundle\Entity\ExistingProductDraft;
use PcmtDraftBundle\Entity\NewProductDraft;

class ProductDraftCreator implements DraftCreatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(
        $baseEntity,
        array $productData,
        ?UserInterface $author = null
    ): AbstractDraft {
        if ($baseEntity && $baseEntity->getId()) {
            if (!empty($productData['categories'])) {
                $key = array_search(DraftCreatorInterface::CATEGORY_FOR_BASE_PRODUCTS, $productData['categories']);
                if (false !== $key) {
                    unset($productData['categories'][$key]);
                }
            }

            return new ExistingProductDraft(
                $baseEntity,
                $productData,
                new \DateTime(),
                $author
            );
        }

        return new NewProductDraft(
            $productData,
            new \DateTime(),
            $author
        );
    }
}