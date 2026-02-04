<?php

namespace Constpb\AmoPlaceholder\Entity;

use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;

class Lead extends BaseEntity
{
    public function getType(): EntityTypeEnum
    {
        return EntityTypeEnum::LEAD;
    }
}
