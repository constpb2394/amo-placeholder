<?php

namespace Constpb\AmoPlaceholder\Extractor;

use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseCustomFieldValueModel;
use Constpb\AmoPlaceholder\Extractor\Trait\ExtractorTrait;

abstract class AbstractFieldValueExtractor implements FieldValueExtractorInterface
{
    use ExtractorTrait;

    protected function extractEnum(BaseCustomFieldValueModel $fieldValue): ?string
    {
        return $this->extractEnumCode($fieldValue);
    }
}
