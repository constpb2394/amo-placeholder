<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields\Type;

namespace Constpb\AmoPlaceholder\Entity\CustomFields\Type;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

class Phone extends CustomFieldBase
{
    public function getType(): FieldTypeEnum
    {
        return FieldTypeEnum::PHONE;
    }
}
