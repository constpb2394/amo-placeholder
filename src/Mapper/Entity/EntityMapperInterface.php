<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity;

use AmoCRM\Models\BaseApiModel;
use Constpb\AmoPlaceholder\Entity\EntityInterface;

/**
 * The interface responsible for converting the Amo entity into our internal representation.
 */
interface EntityMapperInterface
{
    public function map(BaseApiModel $model): EntityInterface;
}
