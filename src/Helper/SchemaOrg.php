<?php

namespace ITHilbert\Schema\Helper;

abstract class SchemaOrg
{
    abstract public function getSchema();

    public function __toString()
    {
        return $this->getSchema();
    }

    /**
     * Kodiert einen Wert für die Verwendung innerhalb eines JSON-Strings im
     * <script type="application/ld+json">-Block (ohne umschließende Anführungszeichen).
     * Maskiert Anführungszeichen, Backslash, Steuerzeichen sowie < > & ' (HEX-Flags),
     * damit weder das JSON noch der Script-Block durchbrochen werden kann.
     */
    protected function e(mixed $value): string
    {
        $json = json_encode(
            (string) $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );

        return substr($json, 1, -1);
    }

    protected function removeLastKomma($schema)
    {
        // Letzte Komma wieder entfernen
        return substr($schema, 0, -2)."\n";
    }
}
