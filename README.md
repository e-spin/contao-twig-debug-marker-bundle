# contao-twig-debug-marker-bundle

*[English below](#english)*

Contaos klassische `.html5`-Vorlagen tragen im Debug-Modus einen HTML-Kommentar
`<!-- TEMPLATE START: ... -->` um ihre Ausgabe, so dass im Quelltext einer Seite sofort zu sehen
ist, welche Vorlage welches Stück HTML erzeugt hat. Übernimmt ein gleichnamiges Twig-Template den
Vorrang - Contaos eigener Mechanismus dafür -, fehlt dieser Kommentar: Der Zweig, der die
Twig-Ausgabe zurückgibt, liegt in `Contao\TemplateInheritance::inherit()` **vor** der
Debug-Markierung, nicht danach. Dieses Bundle schließt genau diese Lücke, ohne an Contao selbst
etwas zu ändern.

## Installation

```
composer require e-spin/contao-twig-debug-marker-bundle
```

Keine Konfiguration, keine Datenbank-Änderung - installieren genügt.

## Verhalten

Nur wirksam, wenn `kernel.debug` an ist (Symfony-`dev`-Modus bzw. Contaos Debug-Umschalter). Jeder
**oberste** Aufruf von `Twig\Environment::render()` für ein Template im verwalteten
`@`-Namespace - also alles, was Contao selbst unter einem Namen lädt: Content-Elemente, Module,
jedes Legacy-zu-Twig-Surrogat - bekommt denselben Rahmen wie die `.html5`-Vorlagen:

```html
<!-- TWIG TEMPLATE START: @Contao/content_element/text.html.twig -->
...
<!-- TWIG TEMPLATE END: @Contao/content_element/text.html.twig -->
```

Ein `{% include %}` oder `{% embed %}` **innerhalb** eines Templates läuft nicht über
`render()`, sondern direkt auf dem bereits geladenen Template - solche verschachtelten Aufrufe
bekommen also **keinen** eigenen Kommentar und können daher auch keinen Kommentar an einer Stelle
platzieren, wo er nicht hingehört (z. B. innerhalb eines Attributwerts).

Außerhalb des Debug-Modus tut das Bundle nichts, die Ausgabe ist byteidentisch zu ohne das Bundle.

## Wie es arbeitet

Ein einziger Compiler-Pass tauscht die Klasse des `twig`-Dienstes gegen eine Unterklasse von
`Twig\Environment`, die nur `render()` überschreibt - Ladeprogramm und Optionen, die Symfonys
TwigBundle bereits zusammengestellt hat, bleiben unverändert, es ändert sich nur, welche Klasse
damit erzeugt wird.

## Lizenz

LGPL-3.0-or-later

---

<a id="english"></a>

# contao-twig-debug-marker-bundle (English)

Contao's classic `.html5` templates wrap their output in an HTML comment
`<!-- TEMPLATE START: ... -->` in debug mode, so a page's source instantly shows which template
produced which piece of markup. When a same-named Twig template takes precedence - Contao's own
mechanism for that - the comment is missing: the branch that returns the Twig output, in
`Contao\TemplateInheritance::inherit()`, sits **before** the debug marker, not after. This bundle
closes exactly that gap without touching Contao itself.

## Installation

```
composer require e-spin/contao-twig-debug-marker-bundle
```

No configuration, no database change - installing is enough.

## Behaviour

Only active when `kernel.debug` is on (Symfony's `dev` mode, or Contao's own debug toggle). Every
**top-level** call to `Twig\Environment::render()` for a template in the managed `@` namespace -
i.e. anything Contao itself loads by name: content elements, modules, every legacy-to-Twig
surrogate - gets the same wrapper the `.html5` templates already have:

```html
<!-- TWIG TEMPLATE START: @Contao/content_element/text.html.twig -->
...
<!-- TWIG TEMPLATE END: @Contao/content_element/text.html.twig -->
```

An `{% include %}` or `{% embed %}` **inside** a template does not go through `render()` - it
calls the already-loaded template directly - so such nested calls get **no** comment of their own
and can never place one somewhere it does not belong (inside an attribute value, for instance).

Outside debug mode the bundle does nothing; output is byte-identical to not having it installed.

## How it works

A single compiler pass swaps the `twig` service's class for a `Twig\Environment` subclass that
overrides only `render()` - the loader and options Symfony's TwigBundle already assembled stay
untouched, only the class instantiated with them changes.

## License

LGPL-3.0-or-later
