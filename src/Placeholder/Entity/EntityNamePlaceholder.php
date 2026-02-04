<?php

namespace Constpb\AmoPlaceholder\Placeholder\Entity;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

abstract class EntityNamePlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return 'Наименование ';
    }
}
