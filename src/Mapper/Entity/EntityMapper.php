<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity;

use AmoCRM\Models\BaseApiModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\Type\Factory\TypeFactoryInterface;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Entity\EntityInterface;
use Constpb\AmoPlaceholder\Extractor\ExtractorFactory;
use Constpb\AmoPlaceholder\Extractor\Trait\ExtractorTrait;
use Psr\Log\LoggerInterface;

abstract class EntityMapper implements EntityMapperInterface
{
    use ExtractorTrait;

    public function __construct(
        private readonly TypeFactoryInterface $typeFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function map(BaseApiModel $model): EntityInterface
    {
        $this->checkApiModel($model);

        $entity = $this->createEntity();
        $entity->setName($model->getName());

        $cfValues = $model->getCustomFieldsValues();
        if (!$cfValues) {
            $this->logger->warning('Не найдены кастомные поля в сущности {entity} c id={id}', [
                'entity' => get_class($entity),
                'id' => $model->getId(),
            ]);

            return $entity;
        }



        foreach ($cfValues as $cfValue) {
            $fieldCode = $cfValue->getFieldCode();
            $fieldType = $cfValue->getFieldType();

            $fieldId = $cfValue->getFieldId();
            $fieldName = $cfValue->getFieldName();

            $field = $this->typeFactory->create($fieldType, $fieldCode);
            if (!$field) {
                $this->logger->warning('Не найден тип поля {field_type} для поля {field_code}', [
                    'field_type' => $fieldType,
                    'field_code' => $fieldCode,
                ]);

                continue;
            }

            $field->setId($fieldId);
            $field->setName($fieldName);

            $extractor = ExtractorFactory::create($field);
            $customFields = $extractor->extractData($cfValue, $field);

            $entity->addCustomFields($customFields);
        }

        $this->logger->debug('Создана сущность', [
            'entity_class' => get_class($entity),
            'entity_name' => $entity->getName(),
            'custom_fields' => array_map(function (Value $customField) {
                return [
                    'id' => $customField->getId(),
                    'name' => $customField->getName(),
                    'type' => $customField->getType()->value,
                    'enum_code' => $customField->getEnumCode(),
                    'value' => $customField->getValue(),
                ];
            }, $entity->getCustomFields()),
        ]);

        return $entity;
    }

    abstract protected function createEntity(): EntityInterface;

    /**
     * @throws \InvalidArgumentException
     */
    abstract protected function checkApiModel(BaseApiModel $model): void;
}
