<?php

namespace Constpb\AmoPlaceholder\Placeholder\Entity\Contact;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class ContactEntityPlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return 'Контакт';
    }
}
