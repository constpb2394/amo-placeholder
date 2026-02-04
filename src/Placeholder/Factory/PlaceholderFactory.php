<?php

namespace Constpb\AmoPlaceholder\Placeholder\Factory;

use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Placeholder\Entity\Contact\ContactEntityPlaceholder;
use Constpb\AmoPlaceholder\Placeholder\Entity\Contact\ContactNamePlaceholder;
use Constpb\AmoPlaceholder\Placeholder\Entity\EntityNamePlaceholder;
use Constpb\AmoPlaceholder\Placeholder\Entity\Lead\LeadEntityPlaceholder;
use Constpb\AmoPlaceholder\Placeholder\Entity\Lead\LeadNamePlaceholder;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class PlaceholderFactory implements PlaceholderFactoryInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function createEntityPlaceholder(EntityTypeEnum $entityType): PlaceholderInterface
    {
        $placeholder = match ($entityType) {
            EntityTypeEnum::LEAD => new LeadEntityPlaceholder(),
            EntityTypeEnum::CONTACT => new ContactEntityPlaceholder(),
            default => throw new \InvalidArgumentException('Unsupported entity type.' . $entityType->value),
        };

        return $placeholder;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function createNamePlaceholder(EntityTypeEnum $entityType): EntityNamePlaceholder
    {
        $placeholder = match ($entityType) {
            EntityTypeEnum::LEAD => new LeadNamePlaceholder(),
            EntityTypeEnum::CONTACT => new ContactNamePlaceholder(),
            default => throw new \InvalidArgumentException('Unsupported entity type.' . $entityType->value),
        };

        return $placeholder;
    }
}
