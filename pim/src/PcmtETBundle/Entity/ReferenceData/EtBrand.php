<?php
// /src/PcmtETBundle/Entity/ReferenceData/EtBrand.php

namespace PcmtETBundle\Entity\ReferenceData;

use Akeneo\Pim\Structure\Component\AttributeTypes;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * EtBrand entity
 */
class EtBrand extends AbstractCustomEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $sn;


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
    public function getSN(): ?string
    {
        return $this->sn;
    }

    /**
     * @param string $sn
     */
    public function setSN(?string $sn): void
    {
        $this->sn = $sn;
    }

    protected static function getClass(): string
    {
        return 'EtBrand';
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'name';
    }

    public function getReferenceDataEntityType(): string
    {
        return AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT;
    }
}
