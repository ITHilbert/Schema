<?php

declare(strict_types=1);

namespace ITHilbert\Schema\Tests\Unit;

use ITHilbert\Schema\Tests\TestCase;
use ITHilbert\Schema\Types\AggregateRating;
use ITHilbert\Schema\Types\Breadcrumb;
use ITHilbert\Schema\Types\FAQ;
use ITHilbert\Schema\Types\LocalBusiness;
use ITHilbert\Schema\Types\Offers;
use ITHilbert\Schema\Types\Product;
use ITHilbert\Schema\Types\Review;

/**
 * Charakterisierung der einzelnen Schema.org-Typen (Ist-Stand).
 * Jeder Typ liefert ein Fragment "key": {...}, das in ein Objekt eingebettet wird.
 */
final class TypesTest extends TestCase
{
    /**
     * Fragment in ein Objekt packen und dekodieren; Komma am Ende entfernen.
     *
     * @return array<string, mixed>|null
     */
    private function dekodiere(string $fragment): ?array
    {
        return json_decode('{'.rtrim(trim($fragment), ',').'}', true);
    }

    // Charakterisierung
    public function test_breadcrumb_nummeriert_positionen_ab_eins(): void
    {
        $b = new Breadcrumb;
        $b->add('Start', 'https://example.test/');
        $b->add('Unterseite', 'https://example.test/u');

        $json = $this->dekodiere($b->getSchema());

        $this->assertSame('BreadcrumbList', $json['BreadcrumbList']['@type']);
        $liste = $json['BreadcrumbList']['itemListElement'];
        $this->assertCount(2, $liste);
        $this->assertSame(1, $liste[0]['position']);
        $this->assertSame('Unterseite', $liste[1]['name']);
        $this->assertSame('https://example.test/u', $liste[1]['item']);
    }

    // Charakterisierung
    public function test_faq_liefert_fragen_mit_akzeptierter_antwort(): void
    {
        $faq = new FAQ;
        $faq->add('Frage 1?', 'Antwort 1');
        $faq->add('Frage 2?', 'Antwort 2');

        $json = $this->dekodiere($faq->getSchema());

        $this->assertSame('FAQPage', $json['FAQPage']['@type']);
        $this->assertCount(2, $json['FAQPage']['mainEntity']);
        $this->assertSame('Question', $json['FAQPage']['mainEntity'][0]['@type']);
        $this->assertSame('Frage 2?', $json['FAQPage']['mainEntity'][1]['name']);
        $this->assertSame('Antwort 1', $json['FAQPage']['mainEntity'][0]['acceptedAnswer']['text']);
    }

    // Charakterisierung: Mit ratingCount 0 (ausdruecklich gesetzt) -> kein Ausgabefragment
    public function test_aggregate_rating_ohne_bewertungen_ist_leer(): void
    {
        config(['schemaOrg.aggregateRating.ratingCount' => 0]);

        $this->assertSame('', (new AggregateRating)->getSchema());
    }

    // Charakterisierung
    public function test_aggregate_rating_mit_werten(): void
    {
        $r = new AggregateRating;
        $r->setRatingValue(4.5);
        $r->setBestRating(5);
        $r->setRatingCount(12);

        $json = $this->dekodiere($r->getSchema());

        $this->assertSame('AggregateRating', $json['aggregateRating']['@type']);
        $this->assertSame('4.5', $json['aggregateRating']['ratingValue']);
        $this->assertSame('5', $json['aggregateRating']['bestRating']);
        $this->assertSame('12', $json['aggregateRating']['ratingCount']);
    }

    // Charakterisierung: Werte kommen aus der Config
    public function test_aggregate_rating_liest_defaults_aus_der_config(): void
    {
        config(['schemaOrg.aggregateRating' => ['ratingValue' => '4', 'bestRating' => '5', 'ratingCount' => '9']]);

        $json = $this->dekodiere((new AggregateRating)->getSchema());

        $this->assertSame('9', $json['aggregateRating']['ratingCount']);
        $this->assertSame('4', $json['aggregateRating']['ratingValue']);
    }

    // Charakterisierung
    public function test_review_mit_standardwerten_hat_bewertung_5_von_5(): void
    {
        config(['schemaOrg.review' => ['ratingValue' => '5', 'bestRating' => '5', 'namePerson' => '']]);

        $json = $this->dekodiere((new Review)->getSchema());

        $this->assertSame('Review', $json['review']['@type']);
        $this->assertSame('5', $json['review']['reviewRating']['ratingValue']);
        $this->assertSame('5', $json['review']['reviewRating']['bestRating']);
        $this->assertArrayNotHasKey('author', $json['review']);
        $this->assertArrayNotHasKey('itemReviewed', $json['review']);
    }

    // Charakterisierung
    public function test_review_mit_allen_feldern_ist_gueltiges_json(): void
    {
        $r = new Review;
        $r->ratingValue(4);
        $r->namePerson('Anna');
        $r->reviewBody('Sehr gut');
        $r->datePublished('2026-01-02');
        $r->itemReviewed('Firma X');
        $r->itemReviewedType('Organization');

        $json = $this->dekodiere($r->getSchema());

        $this->assertNotNull($json, 'Review-Fragment ist kein gueltiges JSON');
        $this->assertSame('Anna', $json['review']['author']['name']);
        $this->assertSame('Firma X', $json['review']['itemReviewed']['name']);
        $this->assertSame('Sehr gut', $json['review']['reviewBody']);
    }

    // Charakterisierung
    public function test_offers_standardwerte_ohne_versand_und_ruecksendung(): void
    {
        $o = new Offers;

        $json = $this->dekodiere($o->getSchema());

        $this->assertSame('Offer', $json['offers']['@type']);
        $this->assertSame('0', $json['offers']['price']);
        $this->assertSame('EUR', $json['offers']['priceCurrency']);
        $this->assertSame('InStock', $json['offers']['availability']);
        $this->assertSame('NewCondition', $json['offers']['itemCondition']);
        $this->assertSame(url()->current(), $json['offers']['url']);
        $this->assertArrayNotHasKey('shippingDetails', $json['offers']);
        $this->assertArrayNotHasKey('hasMerchantReturnPolicy', $json['offers']);
        $this->assertArrayNotHasKey('seller', $json['offers']);
    }

    // Charakterisierung
    public function test_offers_setter_und_verkaeufer(): void
    {
        $o = new Offers;
        $o->setPrice('19.90');
        $o->setPriceCurrency('USD');
        $o->setSeller('Muster GmbH');
        $o->setShowShipping(true);

        $json = $this->dekodiere($o->getSchema());

        $this->assertSame('19.90', $json['offers']['price']);
        $this->assertSame('USD', $json['offers']['priceCurrency']);
        $this->assertSame('Muster GmbH', $json['offers']['seller']['name']);
        $this->assertSame('DACH', $json['offers']['shippingDestination']);
        $this->assertSame('NextDay', $json['offers']['deliveryTime']);
    }

    // Charakterisierung: showShipping wird auch als String "true" aus der Config gelesen
    public function test_offers_show_shipping_aus_config_string(): void
    {
        config(['schemaOrg.offers.showShipping' => 'true']);

        $this->assertStringContainsString('"shippingDetails"', (new Offers)->getSchema());
    }

    // OFFEN-2 (roter Test, Defekt): Bei showReturnPolicy steht hinter "applicableCountry" ein
    // doppeltes Komma (',,'), das JSON ist ungueltig.
    public function test_offers_mit_ruecksendung_ist_gueltiges_json(): void
    {
        $o = new Offers;
        $o->setShowReturnPolicy(true);

        $json = $this->dekodiere($o->getSchema());

        $this->assertNotNull($json, 'Offers-Fragment mit Rueckgaberichtlinie ist kein gueltiges JSON');
        $this->assertSame('DACH', $json['offers']['applicableCountry']);
    }

    // Charakterisierung
    public function test_product_basis_ohne_marke_und_bild(): void
    {
        config(['schemaOrg.product.brand' => '']);
        $p = new Product('Tisch', 'Ein Tisch');

        $schema = $p->getSchema();

        $this->assertStringContainsString('"name": "Tisch"', $schema);
        $this->assertStringContainsString('"description": "Ein Tisch"', $schema);
        $this->assertStringContainsString('"@type": "Product"', $schema);
        $this->assertStringNotContainsString('"brand"', $schema);
        $this->assertStringNotContainsString('"image"', $schema);
    }

    // Charakterisierung: Marke aus Config, Bild ueber asset()
    public function test_product_marke_und_bild(): void
    {
        config(['schemaOrg.product.brand' => 'Acme']);
        $p = new Product('Tisch', 'Ein Tisch');
        $p->setImage('img/tisch.png');

        $schema = $p->getSchema();

        $this->assertStringContainsString('"name": "Acme"', $schema);
        $this->assertStringContainsString('"image": "'.asset('img/tisch.png').'"', $schema);
    }

    // OFFEN-3 (roter Test, Defekt): Der Schluessel des Produkts ist "Prodct" (Tippfehler im Quelltext).
    public function test_product_fragment_nutzt_schluessel_product(): void
    {
        $schema = (new Product('Tisch', 'Ein Tisch'))->getSchema();

        $this->assertStringContainsString('"Product": {', $schema);
        $this->assertStringNotContainsString('"Prodct"', $schema);
    }

    // Charakterisierung: Produkt-Review uebernimmt Namen und Typ
    public function test_product_use_review_setzt_besprochenes_objekt(): void
    {
        $p = new Product('Tisch', 'Ein Tisch');
        $p->useReview();

        $this->assertSame('Tisch', $p->review->itemReviewed);
        $this->assertSame('Product', $p->review->itemReviewedType);
    }

    // Charakterisierung
    public function test_local_business_liest_config_und_gibt_pflichtfelder_aus(): void
    {
        config(['schemaOrg.localBusiness' => [
            'url' => 'https://example.test', 'name' => 'Muster GmbH', 'addressLocality' => 'Berlin',
            'postalCode' => '10115', 'streetAddress' => 'Weg 1', 'openingHours' => 'Mo-Fr 9-17',
            'telephone' => '030 123', 'logo' => 'logo.png', 'image' => 'bild.png',
        ]]);

        $schema = (new LocalBusiness)->getSchema();

        $this->assertStringContainsString('"LocalBusiness": {', $schema);
        $this->assertStringContainsString('"name": "Muster GmbH"', $schema);
        $this->assertStringContainsString('"addressLocality": "Berlin"', $schema);
        $this->assertStringContainsString('"streetAddress": "Weg 1"', $schema);
        $this->assertStringContainsString('"openingHours": "Mo-Fr 9-17"', $schema);
        $this->assertStringContainsString('"telephone": "030 123"', $schema);
        $this->assertStringContainsString('"logo": "'.asset('logo.png').'"', $schema);
        $this->assertStringContainsString('"image": "'.asset('bild.png').'"', $schema);
    }

    // Charakterisierung: Leere optionale Felder werden weggelassen
    public function test_local_business_laesst_leere_optionale_felder_weg(): void
    {
        config(['schemaOrg.localBusiness' => ['openingHours' => '', 'telephone' => '', 'logo' => '', 'image' => '']]);

        $schema = (new LocalBusiness)->getSchema();

        $this->assertStringNotContainsString('"openingHours"', $schema);
        $this->assertStringNotContainsString('"telephone"', $schema);
        $this->assertStringNotContainsString('"logo"', $schema);
        $this->assertStringNotContainsString('"image"', $schema);
    }

    // Charakterisierung
    public function test_local_business_setter(): void
    {
        $l = new LocalBusiness;
        $l->setName('Neu');
        $l->setURL('https://neu.test');

        $schema = $l->getSchema();

        $this->assertStringContainsString('"name": "Neu"', $schema);
        $this->assertStringContainsString('"url": "https://neu.test"', $schema);
    }

    // Charakterisierung
    public function test_to_string_entspricht_get_schema(): void
    {
        $b = new Breadcrumb;
        $b->add('A', '/a');

        $this->assertSame($b->getSchema(), (string) $b);
    }
}
