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

namespace Espin\ContaoTwigDebugMarkerBundle\Test\Twig;

use Espin\ContaoTwigDebugMarkerBundle\Twig\DebugMarkerEnvironment;
use PHPUnit\Framework\TestCase;
use Twig\Loader\ArrayLoader;

/**
 * @covers \Espin\ContaoTwigDebugMarkerBundle\Twig\DebugMarkerEnvironment
 */
final class DebugMarkerEnvironmentTest extends TestCase
{
    public function testWrapsAContaoNamespacedTemplateInDebugMode(): void
    {
        $environment = $this->createEnvironment(true);

        $output = $environment->render('@Contao/foo.html.twig');

        self::assertSame(
            "\n<!-- TWIG TEMPLATE START: @Contao/foo.html.twig -->\nFOO"
            . "\n<!-- TWIG TEMPLATE END: @Contao/foo.html.twig -->\n",
            $output
        );
    }

    public function testLeavesTheOutputUntouchedOutsideDebugMode(): void
    {
        $environment = $this->createEnvironment(false);

        self::assertSame('FOO', $environment->render('@Contao/foo.html.twig'));
    }

    public function testLeavesTemplatesOutsideTheContaoNamespaceUntouched(): void
    {
        $environment = $this->createEnvironment(true);

        self::assertSame('BAR', $environment->render('plain-name.html.twig'));
    }

    /**
     * A name with an extra dot in the leaf segment - MetaModels' own convention for its "text"
     * output format (search index, sorting, URL building, ...) is the concrete case this was found
     * with - does not necessarily produce page markup at all. Wrapping it broke MetaModels'
     * jump-to-item URL, which used the "text" rendering of an "alias" attribute as a raw path
     * segment; the injected comment made that an invalid URL parameter.
     */
    public function testLeavesANameWithAnExtraDotInTheLeafSegmentUntouched(): void
    {
        $environment = $this->createEnvironment(true);

        self::assertSame(
            'hihi-huhusss-6',
            $environment->render('@Contao/metamodels/attribute/alias.text.html.twig')
        );
    }

    public function testStillWrapsANestedPathWithoutAnExtraDotInTheLeaf(): void
    {
        $environment = $this->createEnvironment(true);

        self::assertSame(
            "\n<!-- TWIG TEMPLATE START: @Contao/metamodels/attribute/alias.html.twig -->\nSLUG"
            . "\n<!-- TWIG TEMPLATE END: @Contao/metamodels/attribute/alias.html.twig -->\n",
            $environment->render('@Contao/metamodels/attribute/alias.html.twig')
        );
    }

    private function createEnvironment(bool $debug): DebugMarkerEnvironment
    {
        $loader = new ArrayLoader([
            '@Contao/foo.html.twig'                                => 'FOO',
            'plain-name.html.twig'                                 => 'BAR',
            '@Contao/metamodels/attribute/alias.text.html.twig'    => 'hihi-huhusss-6',
            '@Contao/metamodels/attribute/alias.html.twig'         => 'SLUG',
        ]);

        return new DebugMarkerEnvironment($loader, ['debug' => $debug, 'cache' => false]);
    }
}
