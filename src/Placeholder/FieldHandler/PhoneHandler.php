<?php

namespace Constpb\AmoPlaceholder\Placeholder\FieldHandler;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;
use Constpb\AmoPlaceholder\Entity\Enum\PhoneFieldEnum;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Home;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Mobile;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Other;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\Work;
use Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator\WorkDD;
use Constpb\AmoPlaceholder\Placeholder\CustomField\PhonePlaceholder;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class PhoneHandler extends AbstractFieldHandler
{
    protected function getEnumPlaceholder(string $enumCode, PlaceholderInterface $basePlaceholder): ?PlaceholderInterface
    {
        $code = PhoneFieldEnum::tryFrom($enumCode);
        if (!$code) {
            $errorMessage = sprintf(
                self::ERROR_MESSAGE_TEMPLATE,
                'of phone',
                $enumCode,
                static::class,
            );

            $this->logger->warning($errorMessage);

            return null;
        }

        $placeholder = match ($code) {
            PhoneFieldEnum::AMOCRM_PHONE_WORK => new Work($basePlaceholder),
            PhoneFieldEnum::AMOCRM_PHONE_WORKDD => new WorkDD($basePlaceholder),
            PhoneFieldEnum::AMOCRM_PHONE_MOBILE => new Mobile($basePlaceholder),
            PhoneFieldEnum::AMOCRM_PHONE_HOME => new Home($basePlaceholder),
            PhoneFieldEnum::AMOCRM_PHONE_OTHER => new Other($basePlaceholder),
        };

        return $placeholder;
    }

    protected function validateType(CustomFieldBase $field): bool
    {
        return FieldTypeEnum::PHONE === $field->getType();
    }

    protected function getTypePlaceholder(): PlaceholderInterface
    {
        return new PhonePlaceholder();
    }
}
