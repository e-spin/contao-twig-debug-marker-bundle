<?php

/**
 * This file is part of e-spin/contao-twig-debug-marker-bundle.
 *
 * Copyright (c) 2026 e-spin
 *
 * @package   e-spin/contao-twig-debug-marker-bundle
 * @author    Ingolf Steinhardt <info@e-spin.de>
 * @copyright 2026 e-spin
 * @license   LGPL-3.0-or-later
 */

declare(strict_types=1);

namespace Espin\ContaoTwigDebugMarkerBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Espin\ContaoTwigDebugMarkerBundle\EspinContaoTwigDebugMarkerBundle;

/**
 * @internal
 */
final class Plugin implements BundlePluginInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) - the parser comes with the interface.
     */
    #[\Override]
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(EspinContaoTwigDebugMarkerBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
