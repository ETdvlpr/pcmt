<?php

namespace PcmtETBundle\Normalizer\Standard;

use PcmtETBundle\Entity\ReferenceData\EtCountryCode;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * EtCountryCode normalizer
 */
class EtCountryCodeNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /**
     * @param EtCountryCode $entity
     * @param null  $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        $normalizedEtCountryCode = [
            'id'    => $entity->getId(),
            'code'  => $entity->getCode(),
            'name'  => $entity->getName(),
            'description'   => $entity->getDescription(),
            'labels' => [
                'en_US' => $entity->getName(),
            ],
        ];
        return $normalizedEtCountryCode;
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EtCountryCode && in_array($format, $this->supportedFormats);
    }
}