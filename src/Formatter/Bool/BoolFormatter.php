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

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function format(?bool $bool): ?string
    {
        if (null === $bool) {
            return null;
        }

        return $bool ? $this->formatTrue() : $this->formatFalse();
    }

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
                $message = sprintf(
                    'Cannot parse "%s" as bool: the value is neither true="%s", nor false="%s"',
                    $formattedBool,
                    $formattedTrue,
                    $formattedFalse
                );

                throw new InvalidArgumentException($message);
        }
    }

    private function formatTrue(): string
    {
        return $this->translate('true');
    }

    private function formatFalse(): string
    {
        return $this->translate('false');
    }

    private function translate(string $key): string
    {
        return $this->translator->trans(
            sprintf('portation.formatter.bool.%s', $key),
            [],
            VyfonyPortationBundle::TRANSLATION_DOMAIN
        );
    }
}
