<?php

namespace Constpb\AmoPlaceholder\Mapper\CustomField;

use AmoCRM\Models\CustomFields\CustomFieldModel;
use Constpb\AmoPlaceholder\Entity\CustomFields\CustomFieldBase;

/**
 * The interface responsible for converting Amo's FieldModel into our internal representation.
 */
interface CustomFieldMapperInterface
{
    public function map(CustomFieldModel $customField): CustomFieldBase;
}
