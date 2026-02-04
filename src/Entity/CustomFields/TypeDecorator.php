<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields;

/**
 * Decorator pattern for representing an internal type system for custom fields.
 * Similar to Amo, which has FieldModel and FieldValueModel.
 */
abstract class TypeDecorator extends CustomFieldBase
{
    protected CustomFieldBase $field;

    public function __construct(CustomFieldBase $field, ?int $id, ?string $name)
    {
        parent::__construct($id, $name);

        $this->field = $field;
    }
}
