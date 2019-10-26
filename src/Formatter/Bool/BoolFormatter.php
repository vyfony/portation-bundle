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

namespace Vyfony\Bundle\PortationBundle\Formatter\Bool;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BoolFormatter implements BoolFormatterInterface
{
    public const BOOL_TRUE = 'да';

    public const BOOL_FALSE = 'нет';

    /**
     * @param bool|null $bool
     *
     * @return string|null
     */
    public function format(?bool $bool): ?string
    {
        if (null === $bool) {
            return null;
        }

        return $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
    }

    /**
     * @param string|null $formattedBool
     *
     * @return bool|null
     */
    public function parse(?string $formattedBool): ?bool
    {
        if (null === $formattedBool) {
            return null;
        }

        return self::BOOL_TRUE === $formattedBool;
    }
}
