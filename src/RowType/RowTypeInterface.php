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
interface RowTypeInterface
{
    public function getNewRowKey(): string;

    public function getNestedRowType(): ?self;

    public function getParentRowType(): ?self;

    public function setParentRowType(self $parentRowType): void;
}
