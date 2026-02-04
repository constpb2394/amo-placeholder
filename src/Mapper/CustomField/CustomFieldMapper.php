<?php

namespace Constpb\AmoPlaceholder\Mapper\CustomField;

use AmoCRM\Models\CustomFields\CustomFieldModel;
use AmoCRM\Models\CustomFields\EnumModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\Model;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Custom;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Email;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Phone;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Psr\Log\LoggerInterface;

class CustomFieldMapper implements CustomFieldMapperInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function map(CustomFieldModel $customField): Model
    {
        $this->logger->debug('Начат мапинг пользовательского поля {field_name}', [
            'field_name' => $customField->getName(),
            'field_type' => $customField->getType(),
            'field_code' => $customField->getCode(),
        ]);

        $cfId = $customField->getId();
        $cfName = $customField->getName();

        $cfCode = (string) $customField->getCode();
        $cfType = FieldTypeEnum::tryFrom($cfCode);

        if (!$cfType) {
            $this->logger->warning('Пользовательское поле {field_name} не является полем специального типа', [
                'field_name' => $cfName,
                'special_types' => array_map(fn (FieldTypeEnum $type) => $type->value, FieldTypeEnum::cases()),
            ]);

            $cfType = new Custom($cfId, $cfName);
        } else {
            $cfType = match ($cfType) {
                FieldTypeEnum::EMAIL => new Email($cfId, $cfName),
                FieldTypeEnum::PHONE => new Phone($cfId, $cfName),
                default => throw new \InvalidArgumentException(
                    sprintf('Неизвестный тип пользовательского поля: name=%s, type=%s', $cfName, $cfType->value)
                ),
            };
        }

        $enumCodes = [];
        if (method_exists($customField, 'getEnums')) {
            $cfEnums = $customField->getEnums();

            $this->logger->debug('Пользовательское поле {field_name} имеет перечисляемые значения', [
                'field_name' => $cfName,
                'enum_codes' => array_map(static function (EnumModel $enum) {
                    return $enum->getValue();
                }, $cfEnums->all()),
            ]);

            /** @var EnumModel $cfEnum */
            foreach ($cfEnums as $cfEnum) {
                $enumCode = $cfEnum->getValue();
                if (!$enumCode) {
                    continue;
                }

                $enumCodes[] = $enumCode;
            }
        }

        $cf = new Model($cfType, $enumCodes);

        $this->logger->debug('Завршён маппинг пользовательского поля {field_name}', [
            'field_name' => $cfName,
            'field' => [
                'id' => $cf->getId(),
                'type' => $cf->getType()->value,
                'type_enum_code' => $cf->getEnumCodes(),
                'name' => $cf->getName(),
            ],
        ]);

        return $cf;
    }
}
