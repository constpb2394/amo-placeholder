<?php

namespace Constpb\AmoPlaceholder\Placeholder\CustomField;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class EmailPlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return 'Email';
    }
}
