<?php
/**
 * Copyright (c) 2019, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtETBundle\Entity\ReferenceData;

use Akeneo\Pim\Structure\Component\AttributeTypes;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

class EtCountryCode extends AbstractCustomEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $codeName
     */
    public function setName(?string $codeName): void
    {
        $this->name = $codeName;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $Description
     */
    public function setDescription(?string $Description): void
    {
        $this->description = $codeDescription;
    }

    public function getReferenceDataEntityType(): string
    {
        return AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT;
    }

    protected static function getClass(): string
    {
        return 'EtCountryCode';
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'name';
    }
}