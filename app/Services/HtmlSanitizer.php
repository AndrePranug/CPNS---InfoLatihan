<?php

namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonyHtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class HtmlSanitizer
{
    private SymfonyHtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = new HtmlSanitizerConfig;

        foreach (['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'sup', 'sub', 'ul', 'ol', 'li'] as $element) {
            $config = $config->allowElement($element);
        }

        $this->sanitizer = new SymfonyHtmlSanitizer($config->withMaxInputLength(20000));
    }

    /**
     * Remove every tag/attribute outside the question allowlist; return '' for visually empty markup.
     */
    public function clean(?string $html): string
    {
        $clean = trim($this->sanitizer->sanitize((string) $html));
        $text = str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags($clean), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return trim($text) === '' ? '' : $clean;
    }
}
