<?php
// /src/PcmtETBundle/Entity/ReferenceData/Manufacturer.php

namespace PcmtETBundle\Entity\ReferenceData;

use Akeneo\Pim\Structure\Component\AttributeTypes;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * Manufacturer entity
 */
class Manufacturer extends AbstractCustomEntity
{
    /** @var string */
    protected $name;

    /** @var EtCountryCode */
    protected $countryoforigin;

    /** @var string */
    protected $address;

    /** @var string */
    protected $gln;

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
     * @return EtCountryCode
     */
    public function getCountryOfOrigin(): ?EtCountryCode
    {
        return $this->countryoforigin;
    }

    /**
     * @param EtCountryCode $countryoforigin
     */
    public function setCountryOfOrigin(?EtCountryCode $countryoforigin): void
    {
        $this->countryoforigin = $countryoforigin;
    }


    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }


    /**
     * @return string
     */
    public function getGLN(): ?string
    {
        return $this->gln;
    }

    /**
     * @param string $gln
     */
    public function setGLN(?string $gln): void
    {
        $this->gln = $gln;
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
        return 'Manufacturer';
    }

    public function getReferenceDataEntityType(): string
    {
        return AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT;
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'name';
    }
}
