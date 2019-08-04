<?php

namespace DivineOmega\WikitextParser;

class Utils
{
    public static function stripTagsMaintainWhitespace(string $html)
    {
        $plaintext = $html;
        $plaintext = str_replace('<', ' <', $plaintext);
        $plaintext = strip_tags($plaintext);
        $plaintext = str_replace('  ', ' ', $plaintext);

        return $plaintext;
    }
}