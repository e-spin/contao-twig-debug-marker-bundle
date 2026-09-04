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

namespace Espin\ContaoTwigDebugMarkerBundle\DependencyInjection\Compiler;

use Espin\ContaoTwigDebugMarkerBundle\Twig\DebugMarkerEnvironment;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Swaps the "twig" service's class for a subclass that adds debug markers - see
 * DebugMarkerEnvironment. Twig\Environment's constructor is untouched, so the arguments Symfony's
 * TwigBundle already wired up for the service (loader, options) keep applying unchanged; only the
 * class that gets instantiated with them changes.
 */
final class UseDebugMarkerEnvironmentPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $container->getDefinition('twig')->setClass(DebugMarkerEnvironment::class);
    }
}
