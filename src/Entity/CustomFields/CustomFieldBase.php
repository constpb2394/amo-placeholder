<?php

namespace Constpb\AmoPlaceholder\Entity\CustomFields;

use Constpb\AmoPlaceholder\Entity\Enum\FieldTypeEnum;

/**
 * To make the module easier to use, we use our own system of field types.
 */
abstract class CustomFieldBase
{
    private ?int $id;
    private ?string $name;

    public function __construct(?int $id = null, ?string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    abstract public function getType(): FieldTypeEnum;
}
