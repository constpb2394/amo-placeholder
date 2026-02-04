<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity\Factory;

use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Mapper\Entity\EntityMapperInterface;

interface EntityMapperFactoryInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function create(EntityTypeEnum $entityType): EntityMapperInterface;
}
