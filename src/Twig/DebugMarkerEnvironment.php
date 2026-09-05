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

namespace Espin\ContaoTwigDebugMarkerBundle\Twig;

use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Wraps every top-level Twig render call for a Contao-managed template with HTML comments in debug
 * mode - the same "TEMPLATE START/END" markers Contao's legacy ".html5" templates have carried for
 * years (see Contao\TemplateInheritance::inherit()), but which never applied to a Twig template
 * that takes precedence over one: Contao\TemplateInheritance::renderTwigSurrogateIfExists() returns
 * the rendered Twig markup directly, before the debug-marker logic further down in inherit() is
 * ever reached. The same short-circuit exists in every other "legacy name, Twig takes precedence"
 * surrogate built the same way.
 *
 * render() - not display() - is the right place to hook: a "{% include %}" or "{% embed %}" inside
 * a template compiles to a direct call on the already-loaded template instance, never back through
 * Environment::render(). Only genuine top-level calls - the surrogate check above, a content
 * element's or module's own controller, Contao's FragmentTemplate, ... - go through here, so nested
 * templates are never double-wrapped or commented from the inside of an attribute value.
 *
 * Limited to the "@"-prefixed Contao managed namespace on purpose: everything Contao itself loads a
 * template by name for - content elements, modules, and every legacy-to-Twig surrogate alike - lives
 * there. Templates rendered under a different name (Symfony's own error pages, the web debug
 * toolbar, ...) stay untouched.
 *
 * Further limited to names shaped exactly like Contao's own surrogate builds them:
 * "@Contao/<leaf>.html.twig", with no further dot in the leaf segment - the one and only shape
 * Contao\TemplateInheritance::renderTwigSurrogateIfExists() ever produces
 * ("@Contao/$this->strTemplate.html.twig"). A name with an extra dot there follows some other,
 * unknown convention layered on top of the same render() call, and there is no generic way to tell
 * whether its result is page markup at all - it might just as well be a plain value a caller goes on
 * to use programmatically (build a URL from, store, compare, ...), which a comment stuck in the
 * middle of would corrupt rather than annotate. Skipping anything with that extra structure keeps
 * this to the one case it is actually known to be safe for.
 */
final class DebugMarkerEnvironment extends Environment
{
    /**
     * @param string|TemplateWrapper  $name
     * @param array<array-key, mixed> $context
     */
    #[\Override]
    public function render($name, array $context = []): string
    {
        $output = parent::render($name, $context);

        if (!$this->isDebug()) {
            return $output;
        }

        $label = $name instanceof TemplateWrapper ? $name->getTemplateName() : $name;
        if (!$this->looksLikeAContaoSurrogateName($label)) {
            return $output;
        }

        return "\n<!-- TWIG TEMPLATE START: {$label} -->\n{$output}\n<!-- TWIG TEMPLATE END: {$label} -->\n";
    }

    /**
     * Recognises exactly the "@Contao/<leaf>.html.twig" shape, requiring the leaf (the last path
     * segment, with the ".html.twig" suffix removed) to be free of further dots.
     */
    private function looksLikeAContaoSurrogateName(string $name): bool
    {
        if (!\str_starts_with($name, '@') || !\str_ends_with($name, '.html.twig')) {
            return false;
        }

        $stem = \substr($name, 0, -\strlen('.html.twig'));
        $leaf = \substr($stem, ((int) \strrpos($stem, '/')) + 1);

        return !\str_contains($leaf, '.');
    }
}
