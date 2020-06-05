<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

namespace PcmtPermissionsBundle\Service;

use Akeneo\Tool\Component\Classification\Repository\CategoryRepositoryInterface;
use PcmtPermissionsBundle\Entity\CategoryWithAccessInterface;

class CategoryWithPermissionsRepository
{
    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    /** @var CategoryPermissionsChecker */
    private $categoryPermissionsChecker;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryPermissionsChecker $categoryPermissionsChecker
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryPermissionsChecker = $categoryPermissionsChecker;
    }

    public function getCategoryCodes(string $permissionLevel): array
    {
        $categories = $this->categoryRepository->findAll();
        $codes = [];
        foreach ($categories as $category) {
            /** @var CategoryWithAccessInterface $category */
            if ($this->categoryPermissionsChecker->isGranted($permissionLevel, $category)) {
                $codes[] = $category->getCode();
            }
        }

        return $codes;
    }
}