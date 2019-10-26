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
interface EntitySourceInterface
{
    /**
     * @return object[]
     */
    public function getEntities(): array;

    /**
     * @param object $entity
     *
     * @return object[]
     */
    public function getNestedEntities(object $entity): array;
}
