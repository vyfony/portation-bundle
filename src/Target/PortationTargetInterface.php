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

use Vyfony\Bundle\PortationBundle\Target\CellValueExtractorInterface as CellValuesExtractor;
use Vyfony\Bundle\PortationBundle\Target\CellValueHandlerInterface as CellValueHandler;
use Vyfony\Bundle\PortationBundle\Target\EntityFactoryInterface as EntityFactory;
use Vyfony\Bundle\PortationBundle\Target\EntitySourceInterface as EntitySource;
use Vyfony\Bundle\PortationBundle\Target\SchemaProviderInterface as Schema;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface PortationTargetInterface extends CellValueHandler, CellValuesExtractor, EntityFactory, EntitySource, Schema
{
}
