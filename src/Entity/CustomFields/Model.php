<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields;

use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

/**
 * Decorator for a custom field model containing a list of enumerations, if any.
 */
class Model extends TypeDecorator
{
    /**
     * @var string[]|null
     */
    private ?array $enumCodes;

    /**
     * @param array<string>|null $enumCodes
     */
    public function __construct(CustomFieldBase $field, ?array $enumCodes = null)
    {
        parent::__construct($field, $field->getId(), $field->getName());

        $this->enumCodes = $enumCodes;
    }

    /**
     * @return string[]|null
     */
    public function getEnumCodes(): ?array
    {
        return $this->enumCodes;
    }

    public function getType(): FieldTypeEnum
    {
        return $this->field->getType();
    }
}
