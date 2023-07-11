<?php
namespace ITHilbert\Schema\Types;

use Illuminate\Support\Facades\Request;
use ITHilbert\Schema\Helper\SchemaOrg;

class Offers extends SchemaOrg{

    private $url;
    private $priceCurrency;
    private $price;
    private $priceValidUntil;
    private $availability;
    private $itemCondition;
    private $shippingDetails;
    private $shippingRate;
    private $deliveryTime;
    private $shippingDestination;
    private $hasMerchantReturnPolicy;
    private $applicableCountry;
    private $returnPolicyCategory;
    private $seller;
    private $showShipping;
    private $showReturnPolicy;

    public function setURL($url){
        $this->url = $url;
    }

    public function setPriceCurrency($priceCurrency){
        $this->priceCurrency = $priceCurrency;
    }

    public function setPrice($price){
        $this->price = $price;
    }

    public function setPriceValidUntil($priceValidUntil){
        $this->priceValidUntil = $priceValidUntil;
    }

    public function setAvailability($availability){
        $this->availability = $availability;
    }

    public function setItemCondition($itemCondition){
        $this->itemCondition = $itemCondition;
    }

    public function setShippingDetails($shippingDetails){
        $this->shippingDetails = $shippingDetails;
    }

    public function setShippingRate($shippingRate){
        $this->shippingRate = $shippingRate;
    }

    public function setDeliveryTime($deliveryTime){
        $this->deliveryTime = $deliveryTime;
    }

    public function setShippingDestination($shippingDestination){
        $this->shippingDestination = $shippingDestination;
    }

    public function setHasMerchantReturnPolicy($hasMerchantReturnPolicy){
        $this->hasMerchantReturnPolicy = $hasMerchantReturnPolicy;
    }

    public function setApplicableCountry($applicableCountry){
        $this->applicableCountry = $applicableCountry;
    }

    public function setReturnPolicyCategory($returnPolicyCategory){
        $this->returnPolicyCategory = $returnPolicyCategory;
    }

    public function setSeller($seller){
        $this->seller = $seller;
    }

    public function setShowShipping($showShipping){
        $this->showShipping = $showShipping;
    }

    public function setShowReturnPolicy($showReturnPolicy){
        $this->showReturnPolicy = $showReturnPolicy;
    }

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
