<?php

namespace Constpb\AmoPlaceholder\Placeholder\FieldHandler;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Constpb\AmoPlaceholder\Placeholder\CustomField\CustomPlaceholder;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class CustomHandler extends AbstractFieldHandler
{
    protected function getEnumPlaceholder(
        string $enumCode,
        PlaceholderInterface $basePlaceholder,
    ): ?PlaceholderInterface {
        return null;
    }

    protected function validateType(CustomFieldBase $field): bool
    {
        return FieldTypeEnum::CUSTOM === $field->getType();
    }

    protected function getTypePlaceholder(): PlaceholderInterface
    {
        return new CustomPlaceholder();
    }
}
