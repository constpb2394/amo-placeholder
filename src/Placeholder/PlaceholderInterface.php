<?php

namespace Constpb\AmoPlaceholder\Placeholder;

interface PlaceholderInterface
{
    public const PLACEHOLDER_TEMPLATE = '{{%s / %s}}';

    public function getValue(?string $modificator = null): string;
}
