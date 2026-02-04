<?php

namespace Constpb\AmoPlaceholder\Placeholder\CustomField;

use Constpb\AmoPlaceholder\Placeholder\PlaceholderInterface;

/**
 * Template Decorator for generating placeholders from enumerations of a custom Amo field.
 */
abstract class CustomFieldsPlaceholderDecorator implements PlaceholderInterface
{
    protected const DELIMITER = ' - ';

    private PlaceholderInterface $placeholder;

    public function __construct(PlaceholderInterface $placeholder)
    {
        $this->placeholder = $placeholder;
    }

    public function getValue(?string $modificator = null): string
    {
        return $this->placeholder->getValue();
    }
}
