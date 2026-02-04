<?php

namespace Constpb\AmoPlaceholder\Placeholder\Entity\Contact;

use Constpb\AmoPlaceholder\Locale\LocaleService;
use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

class ContactEntityPlaceholder implements PlaceholderInterface
{
    public function getValue(?string $modificator = null): string
    {
        return LocaleService::trans('contact');
    }
}
