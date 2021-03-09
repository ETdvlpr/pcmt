<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     akeneo
 * @copyright   Copyright (c) Diglin (http://www.diglin.com)
 */

declare(strict_types=1);

namespace PcmtETBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use PcmtETBundle\Entity\ReferenceData\EtCountryCode;

/**
 * Class ReferenceDataController
 * @package ApiRefDataBundle\Bundle\ReferenceDataBundle\Controller
 */
class ReferenceDataController
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var string
     */
    private $classname = null;

    /**
     * ReferenceDataController constructor.
     *
     * @param NormalizerInterface $normalizer
     * @param EntityManagerInterface $entityManager
     * @param Registry $registry
     */
    public function __construct(
        NormalizerInterface $normalizer,
        EntityManagerInterface $entityManager,
        Registry $registry
    ) {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    /**
     * @param $referenceName
     */
    protected function init($referenceName)
    {
        $this->getClassName($referenceName);
    }

    /**
     * Get the data a reference data
     *
     * @param string $referenceName
     *
     * @AclAncestor("api_ref_data_reference_data_get")
     *
     * @param string $code
     *
     * @return Response
     */
    public function getAction(string $referenceName, string $code): Response
    {
        $this->init($referenceName);

        $referenceData = $this->findOrCreateObject([$code]);

        if (null === $referenceData) {
            throw new NotFoundHttpException(sprintf('Reference data "%s" does not exist.', $code));
        }

        $normalizedAsset = $this->normalizer->normalize($referenceData, 'external_api');

        return new JsonResponse($normalizedAsset);
    }

    /**
     * Get the list of all items of a reference data
     *
     * @AclAncestor("api_ref_data_reference_data_get")
     *
     * @param string $referenceName
     *
     * @return JsonResponse
     */
    public function listAction(string $referenceName): Response
    {
        $this->init($referenceName);

        $items = $this->getRepository()->findAll();

        $response = [];
        foreach ($items as $item) {
            $response[] = $this->normalizer->normalize($item, 'external_api');
        }

        return new JsonResponse($response);
    }

    /**
     * Finds or create reference data entity
     *
     * @param array $item
     *
     * @return null|object
     */
    protected function findOrCreateObject(array $item)
    {
        $entity = $this->findObject($item);
        if (null === $entity) {
            $className = $this->getClassName();
            $entity = new $className();
        }

        return $entity;
    }

    /**
     * Finds reference data entity
     *
     * @param array $item
     *
     * @return null|object
     */
    protected function findObject($code)
    {
        return $this->getRepository()->findOneByIdentifier($code);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository($this->getClassName());
    }

    /**
     * Gets class name from the conf registry
     *
     * @param $referenceDataName
     *
     * @return string
     */
    protected function getClassName($referenceDataName = null): ?string
    {
        if (null === $this->classname && null !== $referenceDataName) {
            $this->classname = $this->registry->get($referenceDataName)->getEntityClass();
        }

        return $this->classname;
    }

    public function getEtCountryCodes(): Response
    {
        $repository = $this->entityManager->getRepository(EtCountryCode::class);

        $referenceData = $repository->findAll();

        return new JsonResponse($this->serializer->normalize($referenceData));
    }
}