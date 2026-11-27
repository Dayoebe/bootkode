<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;

class SvgPreviewSanitizer
{
    public function sanitize(string $svg): string
    {
        abort_if(strlen($svg) > 5 * 1024 * 1024 || stripos($svg, '<!DOCTYPE') !== false, 415, 'This SVG cannot be previewed safely.');

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $loaded = $document->loadXML($svg, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        abort_unless($loaded && $document->documentElement?->localName === 'svg', 415, 'This SVG cannot be previewed safely.');

        $xpath = new DOMXPath($document);
        foreach ($xpath->query('//*[local-name()="script" or local-name()="foreignObject" or local-name()="iframe" or local-name()="object" or local-name()="embed" or local-name()="style"]') ?: [] as $node) {
            $node->parentNode?->removeChild($node);
        }

        foreach ($xpath->query('//*') ?: [] as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            foreach (iterator_to_array($node->attributes ?? []) as $attribute) {
                $name = strtolower($attribute->nodeName);
                $value = trim($attribute->nodeValue);
                if (str_starts_with($name, 'on') || $name === 'style'
                    || (in_array($name, ['href', 'xlink:href', 'src'], true) && $value !== '' && !str_starts_with($value, '#'))) {
                    $node->removeAttributeNode($attribute);
                }
            }
        }

        return $document->saveXML($document->documentElement) ?: abort(415, 'This SVG cannot be previewed safely.');
    }
}
