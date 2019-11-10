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

use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vyfony\Bundle\PortationBundle\VyfonyPortationBundle;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BoolFormatter implements BoolFormatterInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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

        return $bool ? $this->formatTrue() : $this->formatFalse();
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

        $formattedTrue = $this->formatTrue();
        $formattedFalse = $this->formatFalse();

        switch ($formattedBool) {
            case $formattedTrue:
                return true;
            case $formattedFalse:
                return false;
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Cannot parse "%s" as bool: the value is neither true="%s", nor false="%s"',
                        $formattedBool,
                        $formattedTrue,
                        $formattedFalse
                    )
                );
        }
    }

    /**
     * @return string
     */
    private function formatTrue(): string
    {
        return $this->translate('true');
    }

    /**
     * @return string
     */
    private function formatFalse(): string
    {
        return $this->translate('false');
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function translate(string $key): string
    {
        return $this->translator->trans(
            sprintf('portation.formatter.bool.%s', $key),
            [],
            VyfonyPortationBundle::TRANSLATION_DOMAIN
        );
    }
}
