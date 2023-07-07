<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class Offers{

    public $url;
    public $priceCurrency;
    public $price;
    public $priceValidUntil;
    public $availability;
    public $itemCondition;
    public $shippingDetails;
    public $shippingRate;
    public $deliveryTime;
    public $shippingDestination;
    public $hasMerchantReturnPolicy;
    public $applicableCountry;
    public $returnPolicyCategory;
    public $seller;

    public $showShipping;
    public $showReturnPolicy;

    public function __construct()
    {
        $this->url = Request::url();
        $this->price = config('schemaOrg.offers.price', 0);
        $this->priceCurrency = config('schemaOrg.offers.priceCurrency', "EUR");
        $this->itemCondition = config('schemaOrg.offers.itemCondition', "NewCondition");

        $this->priceValidUntil = config('schemaOrg.offers.priceValidUntil', date("Y-m-d", strtotime('+2 years')) );
        $this->availability = config('schemaOrg.offers.availability', "InStock");
        $this->shippingDetails = config('schemaOrg.offers.shippingDetails', "shippingSettings");
        $this->hasMerchantReturnPolicy = config('schemaOrg.offers.hasMerchantReturnPolicy', "MerchantReturnFreeShipping");
        $this->seller = config('schemaOrg.offers.seller');

        $this->shippingRate = config('schemaOrg.offers.shippingRate', 'kostenloser Versand');
        $this->deliveryTime = config('schemaOrg.offers.deliveryTime', 'NextDay');
        $this->shippingDestination = config('schemaOrg.offers.shippingDestination', 'DACH');

        $this->applicableCountry = config('schemaOrg.offers.applicableCountry', 'DACH');
        $this->returnPolicyCategory = config('schemaOrg.offers.returnPolicyCategory', 'https://schema.org/NonRefundable');

        //Anzeigen oder nicht?
        $this->showShipping = filter_var(config('schemaOrg.offers.showShipping', 'false'), FILTER_VALIDATE_BOOLEAN);
        $this->showReturnPolicy = filter_var(config('schemaOrg.offers.showReturnPolicy', 'false'), FILTER_VALIDATE_BOOLEAN);
    }

    public function getSchema(){
        $off = "\t\t".'"offers": {'."\n";
        $off .= "\t\t\t".'"@type": "Offer",'."\n";
        $off .= "\t\t\t".'"url": "'.  $this->url .'",'."\n";
        $off .= "\t\t\t".'"price": "'.$this->price.'",'."\n";
        $off .= "\t\t\t".'"priceCurrency": "'.$this->priceCurrency.'",'."\n";
        $off .= "\t\t\t".'"priceValidUntil": "'. $this->priceValidUntil .'",'."\n";
        $off .= "\t\t\t".'"itemCondition": "'.$this->itemCondition.'",'."\n";
        $off .= "\t\t\t".'"availability": "'.$this->availability.'",'."\n";

        if($this->showShipping){
            $off .= "\t\t\t".'"shippingDetails": "'.$this->shippingDetails.'",'."\n";
            $off .= "\t\t\t".'"deliveryTime": "'.$this->deliveryTime.'",'."\n";
            $off .= "\t\t\t".'"shippingDestination": "'.$this->shippingDestination.'",'."\n";
            $off .= "\t\t\t".'"shippingRate": "'.$this->shippingRate.'",'."\n";
        }

        if($this->showReturnPolicy){
            $off .= "\t\t\t".'"hasMerchantReturnPolicy": "'.$this->hasMerchantReturnPolicy.'",'."\n";
            $off .= "\t\t\t".'"returnPolicyCategory": "'.$this->returnPolicyCategory.'",'."\n";
            $off .= "\t\t\t".'"applicableCountry": "'.$this->applicableCountry.'",'.",\n";
        }
        if($this->seller != ''){
            $off .= "\t\t\t".'"seller": {'."\n";
            $off .= "\t\t\t\t".'"@type": "Organization",'."\n";
            $off .= "\t\t\t\t".'"name": "'.$this->seller.'"'."\n";
            $off .= "\t\t\t},\n";
        }
        //Letztes Komma entfernen
        $off = substr($off, 0, -2) . "\n";
        $off .= "\t\t},\n";

        return $off;
    }
}
