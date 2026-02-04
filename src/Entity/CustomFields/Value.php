<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields;

use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

/**
 * Decorator for a custom field value that contains the custom field value itself.
 */
class Value extends TypeDecorator
{
    private ?string $enumCode;
    private ?string $value;

    public function __construct(CustomFieldBase $field, ?string $value, ?string $enumCode = null)
    {
        parent::__construct($field, $field->getId(), $field->getName());

        $this->enumCode = $enumCode;
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getEnumCode(): ?string
    {
        return $this->enumCode;
    }

    public function getType(): FieldTypeEnum
    {
        return $this->field->getType();
    }
}
