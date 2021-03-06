<?php
/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Tests\Connector\Job\Tasklet;

use Akeneo\Tool\Component\Batch\Job\JobParameters;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use PcmtDraftBundle\Connector\Job\Provider\DraftsProvider;
use PcmtDraftBundle\Connector\Job\Tasklet\DraftsBulkRejectTasklet;
use PcmtDraftBundle\Exception\DraftViolationException;
use PcmtDraftBundle\Service\Draft\DraftFacade;
use PcmtDraftBundle\Tests\TestDataBuilder\ExistingProductDraftBuilder;
use PcmtDraftBundle\Tests\TestDataBuilder\NewProductDraftBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class DraftsBulkRejectTaskletTest extends TestCase
{
    /** @var DraftsBulkRejectTasklet */
    private $draftBulkRejectTasklet;

    /** @var DraftFacade|MockObject */
    private $draftFacadeMock;

    /** @var NormalizerInterface|MockObject */
    private $normalizerMock;

    /** @var StepExecution|MockObject */
    private $stepExecutionMock;

    /** @var JobParameters|MockObject */
    private $jobInstanceMock;

    /** @var DraftsProvider|MockObject */
    private $draftsProviderMock;

    protected function setUp(): void
    {
        $this->draftFacadeMock = $this->createMock(DraftFacade::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->stepExecutionMock = $this->createMock(StepExecution::class);
        $this->jobInstanceMock = $this->createMock(JobParameters::class);
        $this->draftsProviderMock = $this->createMock(DraftsProvider::class);

        $this->draftBulkRejectTasklet = new DraftsBulkRejectTasklet(
            $this->draftFacadeMock,
            $this->normalizerMock,
            $this->draftsProviderMock
        );
    }

    public function testExecuteWhenAllSelectedAndNoOneExcluded(): void
    {
        $this->draftBulkRejectTasklet->setStepExecution($this->stepExecutionMock);

        $this->stepExecutionMock
            ->method('getJobParameters')
            ->willReturn($this->jobInstanceMock);

        $this->jobInstanceMock
            ->method('get')
            ->withConsecutive(['allSelected'], ['excluded'], ['selected'])
            ->willReturnOnConsecutiveCalls(true, [], []);

        $draftOfANewProduct = (new NewProductDraftBuilder())->build();
        $draftOfAnExistingProduct = (new ExistingProductDraftBuilder())->build();

        $drafts = [
            $draftOfANewProduct,
            $draftOfAnExistingProduct,
        ];

        $this->draftsProviderMock
            ->method('prepare')
            ->willReturn($drafts);

        $this->draftFacadeMock
            ->expects($this->exactly(2))
            ->method('rejectDraft')
            ->withConsecutive([$draftOfANewProduct], [$draftOfAnExistingProduct]);

        $this->draftBulkRejectTasklet->execute();
    }

    public function testExecuteWhenAllSelectedAndOneDraftIsExcluded(): void
    {
        $this->draftBulkRejectTasklet->setStepExecution($this->stepExecutionMock);

        $this->stepExecutionMock
            ->method('getJobParameters')
            ->willReturn($this->jobInstanceMock);

        $this->jobInstanceMock
            ->method('get')
            ->withConsecutive(['allSelected'], ['excluded'], ['selected'])
            ->willReturnOnConsecutiveCalls(true, [31], []);

        $draftOfANewProduct = (new NewProductDraftBuilder())
            ->withId(30)
            ->build();

        $draftOfAnExistingProduct = (new ExistingProductDraftBuilder())
            ->withId(31)
            ->build();

        $drafts = [
            $draftOfANewProduct,
        ];

        $this->draftsProviderMock
            ->method('prepare')
            ->willReturn($drafts);

        $this->draftFacadeMock
            ->expects($this->exactly(1))
            ->method('rejectDraft')
            ->withConsecutive([$draftOfANewProduct], [$draftOfAnExistingProduct]);

        $this->draftBulkRejectTasklet->execute();
    }

    public function testExecuteWhenSelectedSomeDrafts(): void
    {
        $this->draftBulkRejectTasklet->setStepExecution($this->stepExecutionMock);

        $this->stepExecutionMock
            ->method('getJobParameters')
            ->willReturn($this->jobInstanceMock);

        $this->jobInstanceMock
            ->method('get')
            ->withConsecutive(['allSelected'], ['excluded'], ['selected'])
            ->willReturnOnConsecutiveCalls(false, [], [
                30,
                31,
            ]);

        $draftOfANewProduct = (new NewProductDraftBuilder())
            ->withId(30)
            ->build();

        $draftOfAnExistingProduct = (new ExistingProductDraftBuilder())
            ->withId(31)
            ->build();

        $drafts = [
            $draftOfANewProduct,
            $draftOfAnExistingProduct,
        ];

        $this->draftsProviderMock
            ->method('prepare')
            ->willReturn($drafts);

        $this->draftFacadeMock
            ->expects($this->exactly(2))
            ->method('rejectDraft')
            ->withConsecutive([$draftOfANewProduct], [$draftOfAnExistingProduct]);

        $this->draftBulkRejectTasklet->execute();
    }

    public function testExecuteWhenThereIsNoStepExecution(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->draftBulkRejectTasklet->execute();
    }

    public function testExecuteWhenDraftApprovingFailed(): void
    {
        $this->draftBulkRejectTasklet->setStepExecution($this->stepExecutionMock);

        $this->stepExecutionMock
            ->method('getJobParameters')
            ->willReturn($this->jobInstanceMock);

        $this->jobInstanceMock
            ->method('get')
            ->withConsecutive(['allSelected'], ['excluded'], ['selected'])
            ->willReturnOnConsecutiveCalls(false, [], [
                30,
                31,
            ]);

        $draftOfANewProduct = (new NewProductDraftBuilder())
            ->withId(30)
            ->build();

        $draftOfAnExistingProduct = (new ExistingProductDraftBuilder())
            ->withId(31)
            ->build();

        $drafts = [
            $draftOfANewProduct,
            $draftOfAnExistingProduct,
        ];

        $this->draftsProviderMock
            ->method('prepare')
            ->willReturn($drafts);

        $exception = $this->createMock(DraftViolationException::class);
        $violation = new ConstraintViolation(
            'No corresponding object found.',
            'No corresponding object found.',
            [],
            $draftOfANewProduct,
            'draft_rejection',
            'no'
        );
        $violations = new ConstraintViolationList();
        $violations->add($violation);
        $exception->method('getViolations')->willReturn($violations);

        $this->draftFacadeMock
            ->expects($this->exactly(2))
            ->method('rejectDraft')
            ->willThrowException($exception);

        $this->stepExecutionMock
            ->expects($this->exactly(2))
            ->method('addWarning');

        $this->draftBulkRejectTasklet->execute();
    }
}