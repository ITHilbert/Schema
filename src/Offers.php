<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

class Offers{

    public $url;
    public $priceCurrency;
    public $price;
    public $priceValidUntil;
    public $availability;
    public $shippingDetails;
    public $hasMerchantReturnPolicy;
    public $seller;

    public function __construct()
    {
        $this->url = Request::url();
        $this->price = config('schemaOrg.offers.price', 0);
        $this->priceCurrency = config('schemaOrg.offers.priceCurrency', "EUR");
        $this->priceValidUntil = config('schemaOrg.offers.priceValidUntil', date("Y-m-d", strtotime('+2 years')) );
        $this->availability = config('schemaOrg.offers.availability', "InStock");
        $this->shippingDetails = config('schemaOrg.offers.shippingDetails', "shippingSettings");
        $this->hasMerchantReturnPolicy = config('schemaOrg.offers.hasMerchantReturnPolicy', "MerchantReturnFreeShipping");
        $this->seller = config('schemaOrg.offers.seller');
    }

    public function getSchema(){
        $off = "\t\t".'"offers": {'."\n";
        $off .= "\t\t\t".'"@type": "Offer",'."\n";
        $off .= "\t\t\t".'"url": "'.  $this->url .'",'."\n";
        $off .= "\t\t\t".'"price": "'.$this->price.'",'."\n";
        $off .= "\t\t\t".'"priceCurrency": "'.$this->priceCurrency.'",'."\n";
        $off .= "\t\t\t".'"priceValidUntil": "'. config('schemaOrg.offers.priceValidUntil') .'",'."\n";
        $off .= "\t\t\t".'"itemCondition": "https://schema.org/UsedCondition",'."\n";
        $off .= "\t\t\t".'"availability": "'.$this->availability.'",'."\n";
        $off .= "\t\t\t".'"shippingDetails": "'.$this->shippingDetails.'",'."\n";
        $off .= "\t\t\t".'"hasMerchantReturnPolicy": "'.$this->hasMerchantReturnPolicy.'"';
        if($this->seller != ''){
            $off .= ",\n";
            $off .= "\t\t\t".'"seller": {'."\n";
            $off .= "\t\t\t\t".'"@type": "Organization",'."\n";
            $off .= "\t\t\t\t".'"name": "it-hilbert.com"'."\n";
            $off .= "\t\t\t}\n";
        }else{
            $off .= "\n";
        }
        $off .= "\t\t},\n";

        return $off;
    }
}
