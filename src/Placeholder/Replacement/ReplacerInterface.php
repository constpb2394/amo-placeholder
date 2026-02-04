<?php

namespace Constpb\AmoPlaceholder\Placeholder\Replacement;

use Constpb\AmoPlaceholder\Entity\EntityInterface;

/**
 * An interface responsible for directly substituting values ​​into the specified placeholders.
 */
interface ReplacerInterface
{
    /**
     * @param array<string> $placeholders
     *
     * @return array<string, string>
     */
    public function getReplacements(EntityInterface $entity, array $placeholders): array;
}
