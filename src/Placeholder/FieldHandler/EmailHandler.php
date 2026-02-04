<?php

namespace Constpb\AmoPlaceholder\Placeholder\FieldHandler;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\EmailFieldEnum;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Other;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Priv;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Work;
use Constpb\AmoPlaceholder\Placeholder\CustomField\EmailPlaceholder;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class EmailHandler extends AbstractFieldHandler
{
    protected function getEnumPlaceholder(string $enumCode, PlaceholderInterface $basePlaceholder): ?PlaceholderInterface
    {
        $code = EmailFieldEnum::tryFrom($enumCode);
        if (!$code) {
            $errorMessage = sprintf(
                self::ERROR_MESSAGE_TEMPLATE,
                'почты',
                $enumCode,
                static::class,
            );

            $this->logger->warning($errorMessage);

            return null;
        }

        $placeholder = match ($code) {
            EmailFieldEnum::AMOCRM_EMAIL_PRIV => new Priv($basePlaceholder),
            EmailFieldEnum::AMOCRM_EMAIL_WORK => new Work($basePlaceholder),
            EmailFieldEnum::AMOCRM_EMAIL_OTHER => new Other($basePlaceholder),
        };

        return $placeholder;
    }

    protected function validateType(CustomFieldBase $field): bool
    {
        return FieldTypeEnum::EMAIL === $field->getType();
    }

    protected function getTypePlaceholder(): PlaceholderInterface
    {
        return new EmailPlaceholder();
    }
}
