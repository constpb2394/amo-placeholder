<?php

namespace Constpb\AmoPlaceholder\Entity;

use Constpb\AmoPlaceholder\Entity\CustomFields\Value;
use Constpb\AmoPlaceholder\Entity\Enum\EntityTypeEnum;

/**
 * As with custom field types, for convenience, we use the internal entity type system.
 */
interface EntityInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getType(): EntityTypeEnum;

    /**
     * @return Value[]
     */
    public function getCustomFields(): array;

    /**
     * @param Value[] $customFields
     */
    public function setCustomFields(array $customFields): self;

    public function addCustomField(Value $customField): self;

    /**
     * @param Value[] $customFields
     */
    public function addCustomFields(array $customFields): EntityInterface;
}
