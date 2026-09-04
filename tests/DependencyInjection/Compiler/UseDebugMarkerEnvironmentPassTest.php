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

namespace Espin\ContaoTwigDebugMarkerBundle\Test\DependencyInjection\Compiler;

use Espin\ContaoTwigDebugMarkerBundle\DependencyInjection\Compiler\UseDebugMarkerEnvironmentPass;
use Espin\ContaoTwigDebugMarkerBundle\Twig\DebugMarkerEnvironment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Twig\Environment;

/**
 * @covers \Espin\ContaoTwigDebugMarkerBundle\DependencyInjection\Compiler\UseDebugMarkerEnvironmentPass
 */
final class UseDebugMarkerEnvironmentPassTest extends TestCase
{
    public function testSwapsTheTwigServiceClass(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('twig', new Definition(Environment::class));

        (new UseDebugMarkerEnvironmentPass())->process($container);

        self::assertSame(DebugMarkerEnvironment::class, $container->getDefinition('twig')->getClass());
    }

    public function testDoesNothingWhenTheTwigServiceIsMissing(): void
    {
        $container = new ContainerBuilder();

        (new UseDebugMarkerEnvironmentPass())->process($container);

        self::assertFalse($container->hasDefinition('twig'));
    }
}
