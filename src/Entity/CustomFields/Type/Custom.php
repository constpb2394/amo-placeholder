<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields\Type;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

/**
 * As of February 4, 2025, for the placeholder module, everything that is not a Phone or Email is custom.
 */
class Custom extends CustomFieldBase
{
    public function getType(): FieldTypeEnum
    {
        return FieldTypeEnum::CUSTOM;
    }
}
