<?php

namespace Constpb\AmoPlaceholder\Extractor;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

class ExtractorFactory
{
    public static function create(CustomFieldBase $field): FieldValueExtractorInterface
    {
        $extractor = match ($field->getType()) {
            FieldTypeEnum::PHONE, FieldTypeEnum::EMAIL => new SeparateValueExtractor(),
            FieldTypeEnum::CUSTOM => new AgregateValueExtractor(),
            default => throw new \InvalidArgumentException('Не поддерживаемый тип поля ' . $field->getType()->value), // @phpstan-ignore-line
        };

        return $extractor;
    }
}
