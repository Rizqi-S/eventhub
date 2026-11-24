<?php

namespace App\Services;

class RichTextSanitizer
{
    private const ALLOWED_TAGS = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'h2', 'h3', 'blockquote', 'a'];

    public function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') return null;
        if (! class_exists(\DOMDocument::class)) {
            // Without DOM, keep safe formatting but drop anchors entirely to avoid unsafe href values.
            return strip_tags($html, '<p><br><strong><b><em><i><u><s><ul><ol><li><h2><h3><blockquote>');
        }

        $previous = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadHTML('<?xml encoding="utf-8" ?><div id="rich-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementsByTagName('div')->item(0);
        if (! $root) return e(strip_tags($html));
        $this->sanitizeChildren($root);

        $result = '';
        foreach ($root->childNodes as $child) $result .= $document->saveHTML($child);
        return $result;
    }

    private function sanitizeChildren(\DOMNode $parent): void
    {
        for ($node = $parent->firstChild; $node;) {
            $next = $node->nextSibling;
            if ($node instanceof \DOMElement) {
                $tag = strtolower($node->tagName);
                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    $this->sanitizeChildren($node);
                    while ($node->firstChild) $parent->insertBefore($node->firstChild, $node);
                    $parent->removeChild($node);
                } else {
                    $href = $tag === 'a' ? trim($node->getAttribute('href')) : '';
                    while ($node->attributes->length) $node->removeAttributeNode($node->attributes->item(0));
                    if ($tag === 'a' && preg_match('/^(https?:\/\/|mailto:)/i', $href)) {
                        $node->setAttribute('href', $href);
                        $node->setAttribute('target', '_blank');
                        $node->setAttribute('rel', 'noopener noreferrer');
                    }
                    $this->sanitizeChildren($node);
                }
            }
            $node = $next;
        }
    }
}
