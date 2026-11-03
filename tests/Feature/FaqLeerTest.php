<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests\Feature;

use ITHilbert\Schema\Schema;
use ITHilbert\Schema\Tests\TestCase;

/**
 * Regression: FAQ ohne Einträge.
 *
 * Befund: `$schema->useFAQ(); echo $schema->getSchema();` ohne `faq->add()` gab
 * `"mainEntity": ` gefolgt von `]` aus, weil `Types/FAQ::getSchema()` bei leerer Liste
 * mit `substr($schema, 0, -2)` das `[` samt Zeilenumbruch abschnitt. Der ld+json-Block
 * war ungültiges JSON. Erwartet: ohne Einträge kein FAQPage-Block, Block bleibt gültig.
 *
 * AK2 aus schema-plz-config: „bleibt der ld+json-Block gültiges JSON“.
 */
final class FaqLeerTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function jsonAus(string $ausgabe): array
    {
        $this->assertSame(1, preg_match('#<script type="application/ld\+json">(.*)</script>#s', $ausgabe, $m), 'Kein einzelner ld+json-Block');
        $daten = json_decode($m[1], true);
        $this->assertNotNull($daten, 'ld+json-Block ist kein gültiges JSON: '.json_last_error_msg());

        return $daten;
    }

    // AK2
    public function test_faq_ohne_eintraege_allein_ergibt_gueltiges_json(): void
    {
        $schema = new Schema;
        $schema->useFAQ();

        $daten = $this->jsonAus($schema->getSchema());

        $this->assertSame('https://schema.org/', $daten['@context']);
    }

    // AK2
    public function test_faq_ohne_eintraege_gibt_keinen_faqpage_block_aus(): void
    {
        $schema = new Schema;
        $schema->useFAQ();

        $ausgabe = $schema->getSchema();

        $this->assertStringNotContainsString('FAQPage', $ausgabe);
        $this->assertStringNotContainsString('mainEntity', $ausgabe);
    }

    // AK2
    public function test_faq_ohne_eintraege_mit_local_business_ergibt_gueltiges_json(): void
    {
        $schema = new Schema;
        $schema->useLocalBusiness();
        $schema->useFAQ();

        $daten = $this->jsonAus($schema->getSchema());

        $this->assertArrayHasKey('LocalBusiness', $daten, 'Aufbau: LocalBusiness-Block muss vorhanden sein');
    }

    // AK2
    public function test_faq_ohne_eintraege_mit_local_business_laesst_faqpage_weg(): void
    {
        $schema = new Schema;
        $schema->useLocalBusiness();
        $schema->useFAQ();

        $ausgabe = $schema->getSchema();

        $this->assertStringContainsString('LocalBusiness', $ausgabe, 'Aufbau: LocalBusiness muss ausgegeben werden');
        $this->assertStringNotContainsString('FAQPage', $ausgabe);
    }

    // AK2
    public function test_faq_mit_einem_eintrag_ergibt_gueltiges_json_mit_einer_frage(): void
    {
        $schema = new Schema;
        $schema->useFAQ();
        $schema->faq->add('Frage 1?', 'Antwort 1');

        $daten = $this->jsonAus($schema->getSchema());

        $this->assertCount(1, $daten['FAQPage']['mainEntity']);
        $this->assertSame('Frage 1?', $daten['FAQPage']['mainEntity'][0]['name']);
    }

    // AK2
    public function test_faq_mit_zwei_eintraegen_ergibt_gueltiges_json_mit_zwei_fragen(): void
    {
        $schema = new Schema;
        $schema->useFAQ();
        $schema->faq->add('Frage 1?', 'Antwort 1');
        $schema->faq->add('Frage 2?', 'Antwort 2');

        $daten = $this->jsonAus($schema->getSchema());

        $this->assertCount(2, $daten['FAQPage']['mainEntity']);
        $this->assertSame('Frage 2?', $daten['FAQPage']['mainEntity'][1]['name']);
    }
}
