<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields\Type\Factory;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Custom;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Email;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Phone;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

class CommonTypeFactory implements TypeFactoryInterface
{
    public function create(?string $fieldType, ?string $fieldCode): ?CustomFieldBase
    {
        $field = null;

        $fieldCode = FieldTypeEnum::tryFrom($fieldCode ?? '');
        if ($fieldCode) {
            $field = match ($fieldCode) {
                FieldTypeEnum::PHONE => new Phone(),
                FieldTypeEnum::EMAIL => new Email(),
                default => null,
            };
        }

        if (!$field) {
            $field = new Custom();
        }

        return $field;
    }
}
