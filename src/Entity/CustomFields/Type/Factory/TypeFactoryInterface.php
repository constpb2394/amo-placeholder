<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields\Type\Factory;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;

interface TypeFactoryInterface
{
    public function create(?string $fieldType, ?string $fieldCode): ?CustomFieldBase;
}
