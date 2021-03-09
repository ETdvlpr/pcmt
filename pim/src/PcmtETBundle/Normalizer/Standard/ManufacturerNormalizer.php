<?php

namespace PcmtETBundle\Normalizer\Standard;

use PcmtETBundle\Entity\ReferenceData\Manufacturer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Manufacturer normalizer
 */
class ManufacturerNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /** @var EtCountryCodeNormalizer */
    protected $EtCountryCodeNormalizer;

    /**
     * @param EtCountryCodeNormalizer $EtCountryCodeNormalizer
     */
    public function __construct(EtCountryCodeNormalizer $EtCountryCodeNormalizer)
    {
        $this->EtCountryCodeNormalizer = $EtCountryCodeNormalizer;
    }

    /**
     * @param Manufacturer $entity
     * @param null  $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        $normalizedManufacturer = [
            'id'    => $entity->getId(),
            'code'  => $entity->getCode(),
            'name'  => $entity->getName(),
            'address'  => $entity->getAddress(),
            'sn'   => $entity->getSN(),
            'gln'   => $entity->getGLN(),
        ];
        $countryoforigin = $entity->getCountryOfOrigin();
        if (null !== $countryoforigin) {            
            $normalizedManufacturer['countryoforigin'] = $this->EtCountryCodeNormalizer->normalize($countryoforigin, 'standard');
        }

        return $normalizedManufacturer;
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Manufacturer && in_array($format, $this->supportedFormats);
    }
}