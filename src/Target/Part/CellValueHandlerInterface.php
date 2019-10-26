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

namespace Vyfony\Bundle\PortationBundle\Target;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface CellValueHandlerInterface
{
    /**
     * @param string $newRowKey
     *
     * @return callable[]
     */
    public function getCellValueHandlers(string $newRowKey): array;
}