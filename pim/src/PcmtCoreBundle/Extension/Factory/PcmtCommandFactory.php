<?php

declare(strict_types=1);

namespace PcmtCoreBundle\Extension\Factory;

class PcmtCommandFactory
{
    public function command(string $attributeClass): object
    {
        switch ($attributeClass) {
            default:
                throw new \InvalidArgumentException('Attribute type not recognized');
        }
    }
}