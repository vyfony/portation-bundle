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
     * @var RowTypeInterface|null
     */
    private $nestedRowType;

    /**
     * @param string                $newRowKey
     * @param RowTypeInterface|null $nestedRowType
     */
    public function __construct(string $newRowKey, ?RowTypeInterface $nestedRowType)
    {
        $this->newRowKey = $newRowKey;
        $this->nestedRowType = $nestedRowType;
    }

    /**
     * @return string
     */
    public function getNewRowKey(): string
    {
        return $this->newRowKey;
    }

    /**
     * @return RowTypeInterface|null
     */
    public function getNestedRowType(): ?RowTypeInterface
    {
        return $this->nestedRowType;
    }
}
