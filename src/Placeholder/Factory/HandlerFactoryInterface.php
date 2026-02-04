<?php

namespace Constpb\AmoPlaceholder\Placeholder\Factory;

use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;
use Constpb\AmoPlaceholder\Placeholder\FieldHandler\FieldHandlerInterface;

interface HandlerFactoryInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function createHandler(CustomFieldBase $field): FieldHandlerInterface;
}
