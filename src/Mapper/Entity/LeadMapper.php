<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity;

use AmoCRM\Models\BaseApiModel;
use AmoCRM\Models\LeadModel;
use Constpb\AmoPlaceholder\Entity\EntityInterface;
use Constpb\AmoPlaceholder\Entity\Lead;

class LeadMapper extends EntityMapper
{
    protected function createEntity(): EntityInterface
    {
        return new Lead();
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function checkApiModel(BaseApiModel $model): void
    {
        if (!($model instanceof LeadModel)) {
            throw new \InvalidArgumentException(sprintf('Не поддерживаемая модель амо %s {class=%s}', get_class($model), static::class));
        }
    }
}
