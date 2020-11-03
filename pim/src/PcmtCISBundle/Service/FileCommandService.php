<?php

declare(strict_types=1);

/*
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

namespace PcmtCISBundle\Service;

use PcmtCISBundle\Entity\Subscription;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class FileCommandService
{
    /** @var Filesystem */
    private $filesystem;

    /** @var FileContentService */
    private $fileContentService;

    /** @var FileNameService */
    private $fileNameService;

    /** @var DirectoryService */
    private $directoryService;

    public function __construct(
        Filesystem $filesystem,
        FileContentService $fileContentService,
        FileNameService $fileNameService,
        DirectoryService $directoryService
    ) {
        $this->filesystem = $filesystem;
        $this->fileContentService = $fileContentService;
        $this->fileNameService = $fileNameService;
        $this->directoryService = $directoryService;
    }

    public function createFile(Subscription $subscription, string $documentCommandType): void
    {
        $this->directoryService->prepare();

        $filepath = $this->directoryService->getWorkDirectory() . $this->fileNameService->get();

        $this->filesystem->touch($filepath);

        if (!$this->filesystem->exists($filepath)) {
            throw new FileNotFoundException('File has not been created!');
        }

        $this->filesystem->appendToFile($filepath, $this->fileContentService->getHeader() . PHP_EOL);
        $this->filesystem->appendToFile(
            $filepath,
            $this->fileContentService->getSubscriptionContent($subscription, $documentCommandType)
        );
    }
}