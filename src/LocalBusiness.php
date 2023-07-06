<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class LocalBusiness{
    public static function XXXLocalBusiness(){
        $lb = '"@type": "LocalBusiness",';
        $lb .= '"address": {';
        $lb .= '"@type": "PostalAddress",';
        $lb .= '"addressLocality": "'.config('site.ort').'",';
        $lb .= '"postalCode": "'. config('site.plz').' ",';
        $lb .= '"streetAddress": "'. config('site.strasse').'"';
        $lb .= '},';

        $lb .= '"aggregateRating": {';
        $lb .= '"@type": "AggregateRating",';
        $lb .= '"image": "'. asset('images/schema/5-stars.png') .'",';
        $lb .= '"name": "Access Programmierung",';
        $lb .= '"ratingValue": "5",';
        $lb .= '"bestRating": "5",';
        $lb .= '"ratingCount": "4"';
        $lb .= '},';

        $lb .= '"name": "IT-Hilbert GmbH",';
        $lb .= '"openingHours": "Mo-Fr 10:00-16:00",';
        $lb .= '"telephone": " +49 4344 - 3899378",';
        $lb .= '"url": "'. config('site.domain1') .'",';
        $lb .= '"logo": "'.asset('images/schema/logo-og-hilbert.jpg').'",';
        $lb .= '"image": "'.asset('images/schema/foto-hilbert.jpg').'",';
        $lb .= '"titel": "Ihr Access Programmierer",';
        $lb .= '"price": "0",';
        $lb .= '"priceRange": "0",';

        $lb .= '"review": {';
        $lb .= '"@type": "Review",';
        $lb .= '"reviewRating": {';
        $lb .= '"@type": "Rating",';
        $lb .= '"ratingValue": "5",';
        $lb .= '"bestRating": "5"';
        $lb .= '},';
        $lb .= '"author": {';
        $lb .= '"@type": "Person",';
        $lb .= '"name": "Max"';
        $lb .= '}';
        $lb .= '},';

        $lb .= '"offers": {';
        $lb .= '"@type": "Offer",';
        $lb .= '"url": "{{  Request::url() }}",';
        $lb .= '"priceCurrency": "EUR",';
        $lb .= '"price": "0",';
        $lb .= '"priceValidUntil": "2023-02-09 18:11:42",';
        $lb .= '"itemCondition": "https://schema.org/UsedCondition",';
        $lb .= '"availability": "https://schema.org/InStock",';
        $lb .= '"seller": {';
        $lb .= '"@type": "Organization",';
        $lb .= '"name": "it-hilbert.com"';
        $lb .= '}';
        $lb .= '}';

        return $lb;
    }
}
