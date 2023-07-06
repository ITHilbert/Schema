<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Schema.org
    |--------------------------------------------------------------------------
    |
    | Informationen für die Schema.org Profile
    |
    */

    'aggregateRating' => [
        'ratingValue' => "5",
        "bestRating" => "5",
        "ratingCount" => "10"
    ],


    'LocalBusiness' => [
        'openingHours' => "Mo-Fr 10:00-16:00"
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
        /* shippingDetails: Versand
            "shippingDestination" (Der Versand ist auf bestimmte Ziele beschränkt)
            "shippingLabel" (Ein bestimmtes Versandetikett wird verwendet)
            "shippingRate" (Ein spezifischer Versandpreis wird angegeben)
            "shippingSettings" (Allgemeine Versandeinstellungen oder -optionen) */
        'shippingDetails' => 'shippingSettings',
        /* hasMerchantReturnPolicy: Rücksendung
            "MerchantReturnFiniteReturnWindow" (Der Händler hat eine begrenzte Rückgabefrist)
            "MerchantReturnUnspecified" (Der Händler hat keine spezifische Rückgabepolitik angegeben)
            "MerchantReturnNotPermitted" (Der Händler erlaubt keine Rücksendungen)
            "MerchantReturnFreeShipping" (Der Händler bietet kostenlosen Versand für Rücksendungen an) */
        'hasMerchantReturnPolicy' => 'MerchantReturnFreeShipping'
    ]

];
