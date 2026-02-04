<?php

namespace Constpb\AmoPlaceholder\Placeholder\CustomField;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class CustomPlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return $modificator ?? '';
    }
}
