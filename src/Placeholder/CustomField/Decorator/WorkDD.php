<?php

namespace Constpb\AmoPlaceholder\Placeholder\CustomField\Decorator;

class WorkDD extends Work
{
    public function getValue(?string $modificator = null): string
    {
        $cfName = parent::getValue($modificator);

        return $cfName . ' прямой';
    }
}
