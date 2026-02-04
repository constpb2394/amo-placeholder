<?php

namespace Constpb\AmoPlaceholder\Placeholder\Entity\Lead;

use Constpb\AmoPlaceholder\Placeholder\Entity\EntityNamePlaceholder;

class LeadNamePlaceholder extends EntityNamePlaceholder
{
    public function getValue(?string $modificator = null): string
    {
        $naming = parent::getValue($modificator);

        return $naming . 'сделки';
    }
}
