<?php

namespace Constpb\AmoPlaceholder\Placeholder\FieldHandler;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

/**
 * An interface responsible for converting custom fields into placeholders.
 */
interface FieldHandlerInterface
{
    /**
     * @return array<PlaceholderInterface>
     *
     * @throws \InvalidArgumentException
     */
    public function handleCustomField(CustomFieldBase $field): array;
}
