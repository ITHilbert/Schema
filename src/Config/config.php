<?php
use Illuminate\Support\Facades\Config;

$site = include('site.php');

return [
    /*
    |--------------------------------------------------------------------------
    | Schema.org
    |--------------------------------------------------------------------------
    |
    | Informationen für die Schema.org Profile
    |
    */
    'breadcrumb' => [
        'rootURL' => env('APP_URL'),
        'rootName' => env('APP_NAME')
    ],

    'review' => [
        'reviewBody' => '',
        'ratingValue' => "5",
        'bestRating' => "5",
        'namePerson' => 'Max',
        'datePublished' => '',
    ],

    'aggregateRating' => [
        'ratingValue' => "5",
        "bestRating" => "5",
        "ratingCount" => "10"
    ],

    'product' => [
        'brand' => 'brand'
    ],


    'localBusiness' => [
        //URL
        'url' => $site['domain1'],
        //Name des LocalBusiness
        'name' => $site['firma'],
        //Ort
        'addressLocality' => $site['ort'],
        //PLZ
        'postalCode' => $site['plz'],
        //Straße
        'streetAddress' => $site['strasse'],
        //Öffnungszeiten
        'openingHours' => "Mo-Fr 10:00-16:00",
        //Telefonnummer
        'telephone' => $site['telefon2'],
        //Logo
        'logo' => $site['logo'],
        //Image
        'image' => '/img/image.png',
    ],

    'offers' => [
        "price" => "0",                                 //Preis
        "priceCurrency" => "EUR",                       //Währung "EUR", "USD" usw.
        "priceValidUntil" => "2025-02-09 18:11:42",     //Bis Wann ist das Angebot gültig
        /* availability: Verfügbarkeit
            "Discontinued": Das Produkt ist nicht mehr verfügbar, da es eingestellt wurde.
            "InStock": Das Produkt ist auf Lager und sofort verfügbar.
            "InStoreOnly": Das Produkt ist nur im Ladengeschäft erhältlich und nicht online verfügbar.
            "LimitedAvailability": Das Produkt ist in begrenzter Stückzahl verfügbar.
            "OnlineOnly": Das Produkt ist nur online erhältlich und nicht im Ladengeschäft.
            "OutOfStock": Das Produkt ist derzeit nicht auf Lager.
            "PreOrder": Das Produkt kann vorbestellt werden und wird zu einem späteren Zeitpunkt verfügbar sein.
            "PreSale": Das Produkt kann im Voraus gekauft werden, bevor es offiziell verfügbar ist.
            "SoldOut": Das Produkt ist ausverkauft und derzeit nicht verfügbar. */
        "availability" => "InStock",
        /* itemCondition: Produkt Zustand
            "NewCondition": Das Produkt ist neu und unbenutzt.
            "UsedCondition": Das Produkt wurde zuvor verwendet, befindet sich jedoch in gutem Zustand.
            "RefurbishedCondition": Das Produkt wurde überarbeitet, repariert oder aufgearbeitet, um wieder funktionsfähig zu sein.
            "DamagedCondition": Das Produkt hat Schäden oder Mängel. */
        'itemCondition' => "NewCondition",
        'showShipping' => false,
        /* shippingDetails: Versand
            "shippingDestination" (Der Versand ist auf bestimmte Ziele beschränkt)
            "shippingLabel" (Ein bestimmtes Versandetikett wird verwendet)
            "shippingRate" (Ein spezifischer Versandpreis wird angegeben)
            "shippingSettings" (Allgemeine Versandeinstellungen oder -optionen) */
        'shippingDetails' => 'shippingSettings',
        'shippingRate' => 'kostenloser Versand',            //Textfeld zu den Versandkosten
        /* deliveryTime: Lieferzeit
            "SameDay": Die Lieferung erfolgt am selben Tag, wenn die Bestellung vor einer bestimmten Uhrzeit aufgegeben wird.
            "NextDay": Die Lieferung erfolgt am nächsten Tag nach der Bestellung.
            "TwoDay": Die Lieferung erfolgt innerhalb von zwei Werktagen.
            "ThreeDay": Die Lieferung erfolgt innerhalb von drei Werktagen.
            "FourDay": Die Lieferung erfolgt innerhalb von vier Werktagen.
            "FiveDay": Die Lieferung erfolgt innerhalb von fünf Werktagen.
            "SixDay": Die Lieferung erfolgt innerhalb von sechs Werktagen.
            "SevenDay": Die Lieferung erfolgt innerhalb von sieben Werktagen.
            "1-2 Weeks": Die Lieferung erfolgt innerhalb von ein bis zwei Wochen.
            "2-3 Weeks": Die Lieferung erfolgt innerhalb von zwei bis drei Wochen.
            "3-4 Weeks": Die Lieferung erfolgt innerhalb von drei bis vier Wochen.
            "4-6 Weeks": Die Lieferung erfolgt innerhalb von vier bis sechs Wochen.
            "6-8 Weeks": Die Lieferung erfolgt innerhalb von sechs bis acht Wochen.
            "8-12 Weeks": Die Lieferung erfolgt innerhalb von acht bis zwölf Wochen. */
        'deliveryTime' => 'NextDay',
        /* shippingDestination: Versandregion
            "DACH" - Deutschland, Österreich, Schweiz (allgemeine Bezeichnung für den deutschsprachigen Raum)
            "DE" - Deutschland
            "AT" - Österreich
            "CH" - Schweiz
            "EU" - Europäische Union
            "US" - Vereinigte Staaten
            "CA" - Kanada
            "GB" - Vereinigtes Königreich
            "Weltweit" - Weltweiter Versand
            "Nordamerika" - Nordamerika (USA und Kanada) */
        'shippingDestination' => 'DACH',
        'showReturnPolicy' => false,
        /* hasMerchantReturnPolicy: Rücksendung
            "MerchantReturnFiniteReturnWindow" (Der Händler hat eine begrenzte Rückgabefrist)
            "MerchantReturnUnspecified" (Der Händler hat keine spezifische Rückgabepolitik angegeben)
            "MerchantReturnNotPermitted" (Der Händler erlaubt keine Rücksendungen)
            "MerchantReturnFreeShipping" (Der Händler bietet kostenlosen Versand für Rücksendungen an) */
        'hasMerchantReturnPolicy' => 'MerchantReturnFreeShipping',
        /* applicableCountry: für welche Länder das Angebot gilt
            "DACH" - Deutschland, Österreich, Schweiz (allgemeine Bezeichnung für den deutschsprachigen Raum)
            "US" für die Vereinigten Staaten
            "CA" für Kanada
            "DE" für Deutschland
            "GB" für das Vereinigte Königreich
            "FR" für Frankreich
            "JP" für Japan
            "AU" für Australien
            "IN" für Indien */
        'applicableCountry' => 'DACH',
        /* returnPolicyCategory: Kategorie der Rückgaberichtlinie für ein Produkt
            "https://schema.org/Refundable": Die Rückgaberichtlinie erlaubt Rückerstattungen für das Produkt.
            "https://schema.org/NonRefundable": Das Produkt ist nicht rückerstattungsfähig.
            "https://schema.org/ExchangeRefund": Die Rückgaberichtlinie ermöglicht den Umtausch oder die Rückerstattung des Produkts.
            "https://schema.org/StoreCreditRefund": Die Rückgaberichtlinie ermöglicht eine Rückerstattung in Form von Ladenkredit.
            "https://schema.org/FullRefund": Die Rückgaberichtlinie gewährt eine volle Rückerstattung für das Produkt. */
        'returnPolicyCategory' => 'https://schema.org/NonRefundable'
    ]

];
