<?php

namespace Constpb\AmoPlaceholder\Placeholder\Factory;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\CustomHandler;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\EmailHandler;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\FieldHandlerInterface;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\PhoneHandler;
use Psr\Log\LoggerInterface;

class HandlerFactory implements HandlerFactoryInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function createHandler(CustomFieldBase $field): FieldHandlerInterface
    {
        $handler = match ($field->getType()) {
            FieldTypeEnum::PHONE => new PhoneHandler($this->logger),
            FieldTypeEnum::EMAIL => new EmailHandler($this->logger),
            FieldTypeEnum::CUSTOM => new CustomHandler($this->logger),
            default => throw new \InvalidArgumentException('Не найден обработчик для поля типа ' . $field->getType()->value), // @phpstan-ignore-line
        };

        return $handler;
    }
}
