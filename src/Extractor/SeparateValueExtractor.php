<?php

namespace Constpb\AmoPlaceholder\Extractor;

use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Extractor\Trait\ExtractorTrait;

/**
 * Вытягивает каждое значение отдельно.
 */
class SeparateValueExtractor extends AbstractFieldValueExtractor
{
    use ExtractorTrait;

    public function extractData(
        BaseCustomFieldValuesModel $fieldValues,
        CustomFieldBase $field,
    ): array {
        $result = [];

        $values = $fieldValues->getValues()?->all();
        if (!$values) {
            return $result;
        }

        foreach ($values as $value) {
            $enumCode = $this->extractEnum($value);
            $value = $this->extractSingleValue($value);

            $cf = new Value($field, $value, $enumCode);

            $result[] = $cf;
        }

        return $result;
    }
}
