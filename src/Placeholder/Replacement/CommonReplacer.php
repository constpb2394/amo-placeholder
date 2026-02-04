<?php

namespace Constpb\AmoPlaceholder\Placeholder\Replacement;

use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Entity\EntityInterface;
use Constpb\AmoPlaceholder\Placeholder\Factory\HandlerFactoryInterface;
use Constpb\AmoPlaceholder\Placeholder\Factory\PlaceholderFactoryInterface;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;
use Psr\Log\LoggerInterface;

class CommonReplacer implements ReplacerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HandlerFactoryInterface $handlerFactory,
        private readonly PlaceholderFactoryInterface $placeholderFactory,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getReplacements(EntityInterface $entity, array $placeholders): array
    {
        $this->logger->debug('Начата процедура получения значений для плейсхолдеров', [
            'entity' => $entity->getType()->value,
            'custom_fields' => array_map(static function (Value $customField) {
                return [
                    'id' => $customField->getId(),
                    'name' => $customField->getName(),
                    'value' => $customField->getValue(),
                    'type' => $customField->getType()->value,
                    'enum_code' => $customField->getEnumCode(),
                ];
            }, $entity->getCustomFields()),
            'placeholders' => $placeholders,
        ]);

        $replacements = array_fill_keys($placeholders, '');

        $entityPlaceholder = $this->placeholderFactory->createEntityPlaceholder($entity->getType())->getValue();
        $namePlaceholder = $this->placeholderFactory->createNamePlaceholder($entity->getType())->getValue();

        $entityNamePlaceholder = sprintf(PlaceholderInterface::PLACEHOLDER_TEMPLATE, $entityPlaceholder, $namePlaceholder);

        if (isset($replacements[$entityNamePlaceholder])) {
            $replacements[$entityNamePlaceholder] = $entity->getName() ?? '';
        }

        $fields = $entity->getCustomFields();

        foreach ($fields as $field) {
            try {
                $handler = $this->handlerFactory->createHandler($field);

                $rawPlaceholders = $handler->handleCustomField($field);

                foreach ($rawPlaceholders as $rawPlaceholder) {
                    $placeholder = sprintf(
                        PlaceholderInterface::PLACEHOLDER_TEMPLATE,
                        $entityPlaceholder,
                        $rawPlaceholder->getValue($field->getName() ?? '')
                    );

                    $placeholder = trim($placeholder);

                    if (isset($replacements[$placeholder]) && '' === $replacements[$placeholder]) {
                        $replacements[$placeholder] = $field->getValue() ?? '';
                    }
                }
            } catch (\InvalidArgumentException $e) {
                $this->logger->warning($e->getMessage());

                continue;
            }
        }

        $this->logger->debug('Завершена процедура получения значений для плейсхолдеров', [
            'entity' => $entity->getType()->value,
            'replacements' => $replacements,
        ]);

        return $replacements;
    }
}
