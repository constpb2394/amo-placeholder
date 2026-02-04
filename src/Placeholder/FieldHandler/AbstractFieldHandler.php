<?php

namespace Constpb\AmoPlaceholder\Placeholder\FieldHandler;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\CustomFields\Model;
use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractFieldHandler implements FieldHandlerInterface
{
    protected const ERROR_MESSAGE_TEMPLATE = 'Unsupported field enum_code in the {%s} handler: field - {%s}, class - {%s}';

    protected readonly LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function handleCustomField(CustomFieldBase $field): array
    {
        return $this->handle($field);
    }

    /**
     * @return array<PlaceholderInterface>
     *
     * @throws \InvalidArgumentException
     */
    private function handle(CustomFieldBase $field): array
    {
        if (!$this->validateType($field)) {
            $errorMessage = sprintf(
                self::ERROR_MESSAGE_TEMPLATE,
                $field->getType()->value,
                $field->getName(),
                static::class,
            );

            throw new \InvalidArgumentException($errorMessage);
        }

        $placeholder = $this->getTypePlaceholder();

        if ($field instanceof Model) {
            return $this->handleTypeField($field, $placeholder);
        } elseif ($field instanceof Value) {
            return $this->handleValueField($field, $placeholder);
        } else {
            throw new \InvalidArgumentException('Invalid custom field class: ' . get_class($field));
        }
    }

    /**
     * @return array<PlaceholderInterface>
     */
    private function handleTypeField(Model $field, PlaceholderInterface $placeholder): array
    {
        $fieldCodes = $field->getEnumCodes();
        if (empty($fieldCodes)) {
            return [$placeholder];
        }

        $placeholders = [];

        $placeholders[] = $placeholder;

        foreach ($fieldCodes as $fieldCode) {
            $plhdr = $this->getEnumPlaceholder($fieldCode, $placeholder);

            if (!$plhdr) {
                continue;
            }

            $placeholders[] = $plhdr;
        }

        return $placeholders;
    }

    /**
     * @return PlaceholderInterface[]
     */
    private function handleValueField(Value $field, PlaceholderInterface $placeholder): array
    {
        $fieldCode = $field->getEnumCode();
        if (!$fieldCode) {
            return [$placeholder];
        }

        $enumPlaceholder = $this->getEnumPlaceholder($fieldCode, $placeholder);

        return $enumPlaceholder ? [$placeholder, $enumPlaceholder] : [$placeholder];
    }

    /**
     * @throws \InvalidArgumentException
     */
    abstract protected function validateType(CustomFieldBase $field): bool;

    abstract protected function getTypePlaceholder(): PlaceholderInterface;

    abstract protected function getEnumPlaceholder(string $enumCode, PlaceholderInterface $basePlaceholder): ?PlaceholderInterface;
}
