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

namespace Vyfony\Bundle\PortationBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vyfony\Bundle\PortationBundle\DependencyInjection\CompilerPass\PortationTargetPass;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VyfonyPortationBundle extends Bundle
{
    public const TRANSLATION_DOMAIN = 'portation';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PortationTargetPass());
    }
}
