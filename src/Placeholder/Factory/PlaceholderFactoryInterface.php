<?php

namespace Constpb\AmoPlaceholder\Placeholder\Factory;

use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Placeholder\Entity\EntityNamePlaceholder;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

interface PlaceholderFactoryInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function createEntityPlaceholder(EntityTypeEnum $entityType): PlaceholderInterface;

    /**
     * @throws \InvalidArgumentException
     */
    public function createNamePlaceholder(EntityTypeEnum $entityType): EntityNamePlaceholder;
}
