<?php

namespace Constpb\AmoPlaceholder\Placeholder\Builder;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Placeholder\Factory\HandlerFactoryInterface;
use Constpb\AmoPlaceholder\Placeholder\Factory\PlaceholderFactoryInterface;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;
use Psr\Log\LoggerInterface;

class CommonPlaceholderBuilder implements PlaceholderBuilderInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HandlerFactoryInterface $handlerFactory,
        private readonly PlaceholderFactoryInterface $placeholderFactory,
    ) {
    }

    public function buildPlaceholderList(EntityTypeEnum $entityType, array $customFields): array
    {
        $this->logger->debug('Начато построение списка плейсхолдеров для сущности {entity}', [
            'entity' => $entityType->value,
            'custom_fields' => array_map(static function (CustomFieldBase $customField) {
                return [
                    'id' => $customField->getId(),
                    'name' => $customField->getName(),
                    'type' => $customField->getType()->value,
                ];
            }, $customFields),
        ]);

        $placeholders = [];

        $entityPlaceholder = $this->placeholderFactory->createEntityPlaceholder($entityType)->getValue();
        $namePlaceholder = $this->placeholderFactory->createNamePlaceholder($entityType)->getValue();

        $entityNamePlaceholder = sprintf(PlaceholderInterface::PLACEHOLDER_TEMPLATE, $entityPlaceholder, $namePlaceholder);

        $placeholders[] = $entityNamePlaceholder;

        foreach ($customFields as $field) {
            try {
                $handler = $this->handlerFactory->createHandler($field);

                $rawPlaceholders = $handler->handleCustomField($field);

                foreach ($rawPlaceholders as $rawPlaceholder) {
                    $placeholder = sprintf(
                        PlaceholderInterface::PLACEHOLDER_TEMPLATE,
                        $entityPlaceholder,
                        $rawPlaceholder->getValue($field->getName() ?? '')
                    );

                    $placeholders[] = trim($placeholder);
                }
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning($e->getMessage());

                continue;
            }
        }

        $this->logger->debug('Завершено построение списка плейсхолдеров для сущности {entity}', [
            'entity' => $entityType->value,
            'placeholders' => $placeholders,
        ]);

        return array_unique($placeholders);
    }
}
