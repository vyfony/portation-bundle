<?php

declare(strict_types=1);

/*
 * This file is part of VyfonyPortationBundle project.
 *
 * (c) Anton Dyshkant <vyshkant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vyfony\Bundle\PortationBundle\RowType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class EntityRow implements RowTypeInterface
{
    /**
     * @var string
     */
    private $newRowKey;

    /**
     * @var RowTypeInterface[]
     */
    private $nestedRowTypes;

    /**
     * @var RowTypeInterface|null
     */
    private $parentRowType;

    /**
     * @param RowTypeInterface[] $nestedRowTypes
     */
    public function __construct(string $newRowKey, array $nestedRowTypes)
    {
        $this->newRowKey = $newRowKey;
        $this->nestedRowTypes = $nestedRowTypes;

        foreach ($this->nestedRowTypes as $nestedRowType) {
            $nestedRowType->setParentRowType($this);
        }
    }

    public function getNewRowKey(): string
    {
        return $this->newRowKey;
    }

    public function getNestedRowType(int $nestingLevel): ?RowTypeInterface
    {
        if (\array_key_exists($nestingLevel, $this->nestedRowTypes)) {
            return $this->nestedRowTypes[$nestingLevel];
        }

        return null;
    }

    public function getParentRowType(): ?RowTypeInterface
    {
        return $this->parentRowType;
    }

    public function setParentRowType(RowTypeInterface $parentRowType): void
    {
        $this->parentRowType = $parentRowType;
    }
}
