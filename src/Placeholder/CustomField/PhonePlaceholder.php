<?php

namespace Constpb\AmoPlaceholder\Placeholder\CustomField;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class PhonePlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return 'Телефон';
    }
}
