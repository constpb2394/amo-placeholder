<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity\Factory;

use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Factory\TypeFactoryInterface;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Mapper\Entity\CompanyMapper;
use Constpb\AmoPlaceholder\Mapper\Entity\ContactMapper;
use Constpb\AmoPlaceholder\Mapper\Entity\EntityMapperInterface;
use Constpb\AmoPlaceholder\Mapper\Entity\LeadMapper;
use Psr\Log\LoggerInterface;

class CommonEntityMapperFactory implements EntityMapperFactoryInterface
{
    public function __construct(
        private readonly TypeFactoryInterface $typeFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    public function create(EntityTypeEnum $entityType): EntityMapperInterface
    {
        $mapper = match ($entityType) {
            EntityTypeEnum::LEAD => new LeadMapper($this->typeFactory, $this->logger),
            EntityTypeEnum::CONTACT => new ContactMapper($this->typeFactory,$this->logger),
            default => throw new \InvalidArgumentException('Unsupported entity type for creating a mapper'),
        };

        return $mapper;
    }
}
