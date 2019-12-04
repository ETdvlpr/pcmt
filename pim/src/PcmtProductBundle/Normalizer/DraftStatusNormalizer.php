<?php

declare(strict_types=1);

namespace PcmtProductBundle\Normalizer;

use PcmtProductBundle\Entity\AbstractDraft;
use PcmtProductBundle\Entity\DraftStatus;
use PcmtProductBundle\Service\Draft\DraftStatusTranslatorService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DraftStatusNormalizer implements NormalizerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DraftStatusTranslatorService
     */
    private $draftStatusService;

    public function __construct(LoggerInterface $logger, DraftStatusTranslatorService $draftStatusService)
    {
        $this->logger = $logger;
        $this->draftStatusService = $draftStatusService;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($draftStatus, $format = null, array $context = []): array
    {
        $statusId = $draftStatus->getId();

        try {
            $name = $this->draftStatusService->getNameTranslated($statusId);
        } catch (\Throwable $e) {
            $name = 'Unknown';
            $this->logger->error($e->getMessage());
        }

        return [
            'id'    => $statusId,
            'name'  => $name,
            'class' => $this->getCssClass($statusId),
        ];
    }

    private function getCssClass(int $draftStatusId): string
    {
        switch ($draftStatusId) {
            case AbstractDraft::STATUS_NEW:
                return 'AknBadge--warning';
            case AbstractDraft::STATUS_APPROVED:
                return 'AknBadge--success';
            case AbstractDraft::STATUS_REJECTED:
                return 'AknBadge--important';
            default:
                return 'AknBadge--grey';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof DraftStatus;
    }
}