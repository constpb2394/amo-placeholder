<?php

namespace Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator;

use Constpb\AmoPlaceholder\Placeholder\CustomField\CustomFieldsPlaceholderDecorator;

class Work extends CustomFieldsPlaceholderDecorator
{
    public function getValue(?string $modificator = null): string
    {
        $cfName = parent::getValue($modificator);

        return $cfName . self::DELIMITER . 'Рабочий';
    }
}
