<?php

namespace Constpb\AmoPlaceholder\Extractor;

use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;

/**
 * The interface responsible for extracting data from custom fields of the Amo model.
 */
interface FieldValueExtractorInterface
{
    public const DATE_PATTERN = '/^\d{4}-\d{2}-\d{2}(?:[T\s]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:?\d{2})?)?$/';

    /**
     * @return array<Value>
     */
    public function extractData(BaseCustomFieldValuesModel $fieldValues, CustomFieldBase $field): array;
}
