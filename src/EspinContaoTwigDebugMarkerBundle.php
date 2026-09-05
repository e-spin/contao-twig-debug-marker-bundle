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

namespace Espin\ContaoTwigDebugMarkerBundle;

use Espin\ContaoTwigDebugMarkerBundle\DependencyInjection\Compiler\UseDebugMarkerEnvironmentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This is the bundle class.
 *
 * No configuration to load, so a plain DependencyInjection\Extension would add nothing - the single
 * compiler pass is registered directly here instead.
 *
 * @psalm-suppress DeprecatedInterface - Symfony 8 deprecated BundleInterface in favour of
 * AbstractBundle, but that swap is not a safe drop-in in general (it auto-discovers a classic
 * Extension by class-name convention only, breaking bundles that register one directly - seen
 * elsewhere in this project, e.g. discordier/justtextwidgets). Staying on plain Bundle here for
 * consistency across the project's bundles, suppressing the deprecation notice instead.
 */
final class EspinContaoTwigDebugMarkerBundle extends Bundle
{
    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new UseDebugMarkerEnvironmentPass());
    }
}
