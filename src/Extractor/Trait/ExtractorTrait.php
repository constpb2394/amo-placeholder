<?php

namespace Constpb\AmoPlaceholder\Extractor\Trait;

use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseCustomFieldValueModel;
use Constpb\AmoPlaceholder\Extractor\FieldValueExtractorInterface;

trait ExtractorTrait
{
    private function extractSingleValue(BaseCustomFieldValueModel $valueModel): ?string
    {
        $value = $valueModel->getValue();

        if (is_array($value)) {
            if (isset($value['value'])) {
                $value = $value['value'];
            }

            if (is_array($value)) {
                if (isset($value['text'])) {
                    return (string) $value['text'];
                }
                if (isset($value['value'])) {
                    return (string) $value['value'];
                }

                return implode(', ', array_filter(array_map(function ($item) {
                    return is_string($item) ? $item : ($item['text'] ?? $item['value'] ?? null);
                }, $value)));
            }

            return (string) $value;
        }

        return null !== $value ? (string) $value : null;
    }

    private function extractEnumCode(BaseCustomFieldValueModel $fieldValue): ?string
    {
        if (method_exists($fieldValue, 'getEnum')) {
            // @phpstan-ignore-next-line
            return $fieldValue->getEnum();
        }

        return null;
    }

    private function extractCustomFieldValue(BaseCustomFieldValuesModel $field): ?string
    {
        $values = $field->getValues();

        if (empty($values)) {
            return null;
        }

        $result = [];
        foreach ($values as $valueModel) {
            $value = $this->extractSingleValue($valueModel);

            if (null === $value) {
                continue;
            }

            if (is_string($value)) {
                if (preg_match(FieldValueExtractorInterface::DATE_PATTERN, $value)) {
                    try {
                        $dateTime = new \DateTime($value);
                        $dateTime = $dateTime->modify('+6 hours');

                        $value = $dateTime->format('d.m.Y');
                    } catch (\Throwable $e) {
                        if (isset($this->logger)) {
                            $this->logger->warning('Не получилось в переменной преобразовать дату', [
                                'value' => $value,
                                'exception' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }

            $result[] = $value;
        }

        return !empty($result) ? implode(', ', $result) : null;
    }
}
