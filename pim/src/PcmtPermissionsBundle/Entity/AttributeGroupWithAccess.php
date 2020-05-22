<?php
/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtPermissionsBundle\Entity;

use Akeneo\Pim\Structure\Component\Model\AttributeGroup;
use Akeneo\UserManagement\Component\Model\Group;
use Doctrine\Common\Collections\ArrayCollection;

class AttributeGroupWithAccess extends AttributeGroup
{
    /** @var ArrayCollection */
    private $accesses;

    /**
     * {@inheritdoc}
     */
    public function addAccess(AttributeGroupAccess $access): void
    {
        $this->accesses->add($access);
    }

    public function checkIfAccessExists(Group $userGroup, string $level): bool
    {
        foreach ($this->accesses as $access) {
            /** @var AttributeGroupAccess $access */
            if ($access->getUserGroup()->getId() === $userGroup->getId() && $level === $access->getLevel()) {
                return true;
            }
        }

        return false;
    }

    public function getAccesses(): ArrayCollection
    {
        return $this->accesses;
    }

    public function clearAccesses(): void
    {
        $this->accesses = new ArrayCollection();
    }
}
