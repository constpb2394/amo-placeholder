<?php

namespace Constpb\AmoPlaceholder\Entity;

use Constpb\AmoPlaceholder\Entity\CustomFields\Value;

abstract class BaseEntity implements EntityInterface
{
    private ?string $name;

    /**
     * @var Value[]
     */
    private array $customFields = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): EntityInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getCustomFields(): array
    {
        return $this->customFields;
    }

    public function setCustomFields(array $customFields): EntityInterface
    {
        $this->customFields = $customFields;

        return $this;
    }

    public function addCustomField(Value $customField): EntityInterface
    {
        $this->customFields[] = $customField;

        return $this;
    }

    public function addCustomFields(array $customFields): EntityInterface
    {
        $this->customFields = [...$this->customFields, ...$customFields];

        return $this;
    }
}
