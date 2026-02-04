<?php

namespace Constpb\AmoPlaceholder\Mapper\Entity;

use AmoCRM\Models\BaseApiModel;
use AmoCRM\Models\ContactModel;
use Constpb\AmoPlaceholder\Entity\Contact;
use Constpb\AmoPlaceholder\Entity\EntityInterface;

class ContactMapper extends EntityMapper
{
    protected function createEntity(): EntityInterface
    {
        return new Contact();
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function checkApiModel(BaseApiModel $model): void
    {
        if (!($model instanceof ContactModel)) {
            throw new \InvalidArgumentException(sprintf('Не поддерживаемая модель амо %s {class=%s}', get_class($model), static::class));
        }
    }
}
