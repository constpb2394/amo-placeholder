<?php

namespace Constpb\AmoPlaceholder\Entity;

use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;

class Contact extends BaseEntity
{
    public function getType(): EntityTypeEnum
    {
        return EntityTypeEnum::CONTACT;
    }
}
