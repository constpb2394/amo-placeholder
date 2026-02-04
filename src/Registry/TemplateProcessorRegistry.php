<?php

namespace Constpb\AmoPlaceholder\Registry;

use Constpb\AmoPlaceholder\Entity\Contact;
use Constpb\AmoPlaceholder\Entity\EntityInterface;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Entity\Lead;
use Constpb\AmoPlaceholder\Mapper\Entity\ContactMapper;
use Constpb\AmoPlaceholder\Mapper\Entity\EntityMapperInterface;
use Constpb\AmoPlaceholder\Mapper\Entity\LeadMapper;

class TemplateProcessorRegistry
{
    /**
     * @return array<string, class-string<EntityInterface>>
     */
    public static function getAvailableEntities(): array
    {
        return [
            EntityTypeEnum::LEAD->value => Lead::class,
            EntityTypeEnum::CONTACT->value => Contact::class,
        ];
    }

    /**
     * @return array<string, class-string<EntityMapperInterface>>
     */
    public static function getAvailableMappers(): array
    {
        return [
            EntityTypeEnum::LEAD->value => LeadMapper::class,
            EntityTypeEnum::CONTACT->value => ContactMapper::class,
        ];
    }

    public static function getMapper(EntityTypeEnum $entityType): EntityMapperInterface
    {
        $mappers = self::getAvailableMappers();

        if (!isset($mappers[$entityType->value])) {
            throw new \InvalidArgumentException(sprintf('Mаппер для сущности {%s} не найден!', $entityType->value));
        }

        $class = $mappers[$entityType->value];

        $mapper = new $class();

        return $mapper;
    }

    // /**
    //  * @return CustomField[]
    //  */
    // public static function getAvailableFields(): array
    // {
    //
    // }
}
