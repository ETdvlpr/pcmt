<?php

namespace PcmtETBundle\Normalizer\Standard;

use PcmtETBundle\Entity\ReferenceData\EtBrand;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * EtBrand normalizer
 */
class EtBrandNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /**
     * @param EtBrand $entity
     * @param null  $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        $normalizedEtBrand = [
            'id'    => $entity->getId(),
            'code'  => $entity->getCode(),
            'name'  => $entity->getName(),
            'sn'   => $entity->getSN(),
        ];
        return $normalizedEtBrand;
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EtBrand && in_array($format, $this->supportedFormats);
    }
}