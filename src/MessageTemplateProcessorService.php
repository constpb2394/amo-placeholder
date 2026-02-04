<?php

namespace Constpb\AmoPlaceholder;

use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Models\BaseApiModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFields\CustomFieldModel;
use AmoCRM\Models\LeadModel;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;
use Constpb\AmoPlaceholder\Mapper\CustomField\CustomFieldMapperInterface;
use Constpb\AmoPlaceholder\Mapper\Entity\Factory\EntityMapperFactoryInterface;
use Constpb\AmoPlaceholder\Placeholder\Builder\PlaceholderBuilderInterface;
use Constpb\AmoPlaceholder\Placeholder\Replacement\ReplacerInterface;
use Psr\Log\LoggerInterface;

class MessageTemplateProcessorService
{
    private const PLACEHOLDER_PATTERN = '/\{\{[^}]+\}\}/u';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PlaceholderBuilderInterface $placeholderBuilder,
        private readonly CustomFieldMapperInterface $customFieldMapper,
        private readonly EntityMapperFactoryInterface $mapperFactory,
        private readonly ReplacerInterface $replacement,
    ) {
    }

    /**
     * @return array<array{title:string, value: string}>
     */
    public function getVariables(EntityTypeEnum $entityType, CustomFieldsCollection $customFields): array
    {
        $result = [];

        $cfs = [];
        /** @var CustomFieldModel $customField */
        foreach ($customFields as $customField) {
            $isSystem = $customField->getIsApiOnly();
            if ($isSystem) {
                continue;
            }

            $cfs[] = $this->customFieldMapper->map($customField);
        }

        $placeholders = $this->placeholderBuilder->buildPlaceholderList($entityType, $cfs);
        foreach ($placeholders as $placeholder) {
            $cleanTitle = trim($placeholder, '{} ');

            $result[] = [
                'title' => $cleanTitle,
                'value' => $placeholder,
            ];
        }

        return array_values($result);
    }

    public function replaceVariables(
        string $template,
        BaseApiModel $amocrmEntity,
    ): string {
        $entityType = match (true) {
            $amocrmEntity instanceof LeadModel => EntityTypeEnum::LEAD,
            $amocrmEntity instanceof ContactModel => EntityTypeEnum::CONTACT,
            default => null,
        };

        if (!$entityType) {
            $this->logger->debug('Не поддерживаемый тип сущности , подставляем пустые значения', [
                'entity_id' => $amocrmEntity->getId(),
                'class_name' => get_class($amocrmEntity),
            ]);

            return $this->replaceWithEmptyValues($template);
        }

        $this->logger->debug('Заменяем переменные в сообщении из amoCRM', [
            'entity_id' => $amocrmEntity->getId(),
            'entity_type' => $entityType->value,
            'template' => $template,
            'class_name' => get_class($amocrmEntity),
        ]);

        $mapper = $this->mapperFactory->create($entityType);

        $entity = $mapper->map($amocrmEntity);

        $placeholders = $this->placeholderBuilder->buildPlaceholderList($entityType, $entity->getCustomFields());
        $replacements = $this->replacement->getReplacements($entity, $placeholders);

        preg_match_all(self::PLACEHOLDER_PATTERN, $template, $matches);
        $allPlaceholders = array_unique($matches[0]);

        $this->logger->debug('Нашли данные для замены переменных', [
            'entity_id' => $amocrmEntity->getId(),
            'template' => $template,
            'replacements' => $replacements,
            'all_placeholders' => $allPlaceholders,
        ]);

        $result = $template;

        usort($allPlaceholders, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($allPlaceholders as $placeholder) {
            if (isset($replacements[$placeholder])) {
                $value = $replacements[$placeholder];

                $pattern = '/' . preg_quote($placeholder, '/') . '/iu';
                // @phpstan-ignore-next-line
                $result = preg_replace($pattern, $value, (string) $result);
            }
        }

        $result = $result ?? '';

        $this->logger->info('Закончили заменять переменные', [
            'entity_id' => $amocrmEntity->getId(),
            'entity_type' => $entityType->value,
            'template' => $template,
            'class_name' => get_class($amocrmEntity),
            'result' => $result,
        ]);

        return $result;
    }

    public function replaceWithEmptyValues(string $template): string
    {
        preg_match_all(self::PLACEHOLDER_PATTERN, $template, $matches);
        $allPlaceholders = array_unique($matches[0]);

        $result = $template;

        usort($allPlaceholders, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($allPlaceholders as $placeholder) {
            $pattern = '/' . preg_quote($placeholder, '/') . '/iu';
            // @phpstan-ignore-next-line
            $result = preg_replace($pattern, '', $result);
        }

        return $result ?? '';
    }
}
