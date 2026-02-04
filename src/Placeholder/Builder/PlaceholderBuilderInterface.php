<?php

namespace Constpb\AmoPlaceholder\Placeholder\Builder;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;

/**
 * The interface responsible for compiling a list of placeholders.
 */
interface PlaceholderBuilderInterface
{
    /**
     * @param CustomFieldBase[] $customFields
     *
     * @return array<string>
     */
    public function buildPlaceholderList(EntityTypeEnum $entityType, array $customFields): array;
}
