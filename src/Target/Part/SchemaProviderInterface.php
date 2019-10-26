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

namespace Vyfony\Bundle\PortationBundle\Target\Part;

use Vyfony\Bundle\PortationBundle\RowType\RowTypeInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface SchemaProviderInterface
{
    /**
     * @return RowTypeInterface
     */
    public function getRootRowType(): RowTypeInterface;

    /**
     * @return string[]
     */
    public function getSchema(): array;
}
