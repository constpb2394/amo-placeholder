<?php

namespace Constpb\AmoPlaceholder\Placeholder\Entity\Lead;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class LeadEntityPlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return 'Сделка';
    }
}
