<?php

namespace Constpb\AmoPlaceholder\Placeholder\Entity\Contact;

use Constpb\AmoPlaceholder\Placeholder\Entity\EntityNamePlaceholder;

class ContactNamePlaceholder extends EntityNamePlaceholder
{
    public function getValue(?string $modificator = null): string
    {
        $naming = parent::getValue($modificator);

        return $naming . 'контакта';
    }
}
