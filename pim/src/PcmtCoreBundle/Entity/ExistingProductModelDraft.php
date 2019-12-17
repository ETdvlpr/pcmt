<?php
/**
 * Copyright (c) 2019, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtCoreBundle\Entity;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\UserManagement\Component\Model\UserInterface;

class ExistingProductModelDraft extends AbstractProductModelDraft
{
    public const TYPE = 'existing product model draft';

    public function __construct(
        ProductModelInterface $productModel,
        array $productData,
        UserInterface $author,
        \DateTime $created,
        int $status
    ) {
        $this->productModel = $productModel;
        $this->productData = $productData;
        parent::__construct($author, $created, $status);
    }
}