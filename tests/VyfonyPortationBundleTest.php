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

namespace Vyfony\Bundle\PortationBundle\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VyfonyPortationBundleTest extends TestCase
{
    public function testBundleClassExists(): void
    {
        $this->assertTrue(class_exists(\Vyfony\Bundle\PortationBundle\VyfonyPortationBundle::class));
    }
}
