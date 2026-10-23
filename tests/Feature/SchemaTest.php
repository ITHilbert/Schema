<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests\Feature;

use ITHilbert\Schema\Schema;
use ITHilbert\Schema\Tests\TestCase;

/**
 * Charakterisierung der Hauptklasse Schema (Ist-Stand): setzt aus den Typen
 * ein <script type="application/ld+json">-Dokument zusammen.
 */
final class SchemaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['schemaOrg.breadcrumb' => ['rootName' => 'Start', 'rootURL' => 'https://example.test']]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function jsonAus(string $ausgabe): ?array
    {
        $this->assertSame(1, preg_match('#<script type="application/ld\+json">(.*)</script>#s', $ausgabe, $m));

        return json_decode($m[1], true);
    }

    // Charakterisierung
    public function test_leeres_schema_enthaelt_nur_den_context(): void
    {
        $json = $this->jsonAus((new Schema)->getSchema());

        $this->assertSame(['@context' => 'https://schema.org/'], $json);
    }

    // Charakterisierung
    public function test_ausgabe_ist_ein_ld_json_script_block(): void
    {
        $ausgabe = (string) new Schema;

        $this->assertStringStartsWith('<script type="application/ld+json">', $ausgabe);
        $this->assertStringContainsString('"@context": "https://schema.org/"', $ausgabe);
        $this->assertStringEndsWith("</script>\n", $ausgabe);
    }

    // Charakterisierung
    public function test_breadcrumb_startet_mit_wurzel_aus_der_config(): void
    {
        $schema = new Schema;
        $schema->useBreadcrumb();
        $schema->breadcrumb->add('Unterseite', 'https://example.test/u');

        $json = $this->jsonAus($schema->getSchema());

        $liste = $json['BreadcrumbList']['itemListElement'];
        $this->assertSame('Start', $liste[0]['name']);
        $this->assertSame('https://example.test', $liste[0]['item']);
        $this->assertSame(2, $liste[1]['position']);
    }

    // Charakterisierung
    public function test_use_product_legt_produkt_an(): void
    {
        $schema = new Schema;
        $schema->useProduct('Tisch', 'Ein Tisch');

        $this->assertStringContainsString('"name": "Tisch"', $schema->getSchema());
    }

    // Charakterisierung
    public function test_use_review_setzt_besprochenes_objekt(): void
    {
        $schema = new Schema;
        $schema->useReview('Firma', 'Organization');

        $this->assertSame('Firma', $schema->review->itemReviewed);
        $this->assertSame('Organization', $schema->review->itemReviewedType);
    }

    // Charakterisierung: Reihenfolge Breadcrumb, LocalBusiness, Product, Review, AggregateRating, Offers, FAQ
    public function test_reihenfolge_der_bausteine(): void
    {
        $schema = new Schema;
        $schema->useFAQ();
        $schema->faq->add('F?', 'A');
        $schema->useOffers();
        $schema->useAggregateRating();
        $schema->aggregateRating->setRatingCount(3);
        $schema->useReview('X', 'Organization');
        $schema->useProduct('P', 'D');
        $schema->useLocalBusiness();
        $schema->useBreadcrumb();

        $ausgabe = $schema->getSchema();
        $reihenfolge = ['"BreadcrumbList"', '"LocalBusiness"', '"@type": "Product"', '"review"', '"aggregateRating"', '"offers"', '"FAQPage"'];

        $positionen = array_map(static fn (string $s): int|false => strpos($ausgabe, $s), $reihenfolge);
        $this->assertNotContains(false, $positionen);
        $sortiert = $positionen;
        sort($sortiert);
        $this->assertSame($sortiert, $positionen);
    }

    // Charakterisierung: Breadcrumb allein ergibt gueltiges JSON
    public function test_breadcrumb_allein_ist_gueltiges_json(): void
    {
        $schema = new Schema;
        $schema->useBreadcrumb();

        $this->assertNotNull($this->jsonAus($schema->getSchema()));
    }

    // Charakterisierung: FAQ allein ist gueltiges JSON
    public function test_faq_allein_ist_gueltiges_json(): void
    {
        $schema = new Schema;
        $schema->useFAQ();
        $schema->faq->add('F?', 'A');

        $json = $this->jsonAus($schema->getSchema());

        $this->assertNotNull($json);
        $this->assertSame('FAQPage', $json['FAQPage']['@type']);
    }

    // Charakterisierung: Mehrere Bausteine ohne Sonderfelder ergeben gueltiges JSON
    public function test_breadcrumb_und_faq_und_offers_sind_gueltiges_json(): void
    {
        $schema = new Schema;
        $schema->useBreadcrumb();
        $schema->useOffers();
        $schema->useFAQ();
        $schema->faq->add('F?', 'A');

        $json = $this->jsonAus($schema->getSchema());

        $this->assertNotNull($json);
        $this->assertArrayHasKey('offers', $json);
        $this->assertArrayHasKey('BreadcrumbList', $json);
    }

    // Charakterisierung: Alle Bausteine zusammen ergeben (mit Standardwerten) gueltiges JSON
    public function test_vollstaendiges_schema_ist_gueltiges_json(): void
    {
        config(['schemaOrg.localBusiness.name' => 'Muster GmbH']);
        $schema = new Schema;
        $schema->useBreadcrumb();
        $schema->useLocalBusiness();
        $schema->useProduct('Tisch', 'Ein Tisch');
        $schema->useReview('Muster GmbH', 'Organization');
        $schema->review->namePerson('Anna');
        $schema->useAggregateRating();
        $schema->aggregateRating->setRatingCount(3);
        $schema->useOffers();
        $schema->useFAQ();
        $schema->faq->add('F?', 'A');

        $this->assertNotNull($this->jsonAus($schema->getSchema()), 'Gesamtausgabe ist kein gueltiges JSON');
    }

    // OFFEN-4 (roter Test, Defekt): Texte mit Anfuehrungszeichen werden nicht JSON-maskiert,
    // die Ausgabe wird ungueltig (und ist ein Injektionsweg in den Script-Block).
    public function test_anfuehrungszeichen_in_texten_bleiben_gueltiges_json(): void
    {
        $schema = new Schema;
        $schema->useFAQ();
        $schema->faq->add('Was heisst "Hallo"?', 'Sagt man so');

        $json = $this->jsonAus($schema->getSchema());

        $this->assertNotNull($json, 'Anfuehrungszeichen brechen das JSON');
    }

    // Charakterisierung: Ohne veroeffentlichte Config bleibt der Wurzel-Breadcrumb leer
    public function test_breadcrumb_ohne_config_hat_leere_wurzel(): void
    {
        config(['schemaOrg' => null]);
        $schema = new Schema;
        $schema->useBreadcrumb();

        $this->assertStringContainsString('"name": ""', $schema->getSchema());
    }
}
