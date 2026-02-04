<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity;

use AmoCRM\Models\BaseApiModel;
use Constpb\AmoPlaceholder\Entity\EntityInterface;

/**
 * Интерфейс отвечающий за преобразование сущности Амо в наше внутренне представление.
 */
interface EntityMapperInterface
{
    public function map(BaseApiModel $model): EntityInterface;
}
