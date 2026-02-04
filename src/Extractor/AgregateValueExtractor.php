<?php

namespace Constpb\AmoPlaceholder\Extractor;

use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Extractor\Trait\ExtractorTrait;

/**
 * Слепляет все значения в одно.
 */
class AgregateValueExtractor extends AbstractFieldValueExtractor
{
    use ExtractorTrait;

    public function extractData(
        BaseCustomFieldValuesModel $fieldValues,
        CustomFieldBase $field,
    ): array {
        $value = $this->extractCustomFieldValue($fieldValues);

        $cf = new Value($field, $value);

        return [$cf];
    }
}
