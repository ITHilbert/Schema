<?php
namespace ITHilbert\Schema\Types;

use ITHilbert\Schema\Helper\SchemaOrg;
use ITHilbert\Schema\Types\AggregateRating;
use ITHilbert\Schema\Types\FAQ;
use ITHilbert\Schema\Types\Offers;
use ITHilbert\Schema\Types\Review;

class LocalBusiness extends SchemaOrg{

    private $url;                //URL
    private $name;               //Name des LocalBusiness
    private $addressLocality;    //Ort
    private $postalCode;         //PLZ
    private $streetAddress;      //Straße
    private $openingHours;       //Öffnungszeiten
    private $telephone;          //Telefonnummer
    private $logo;               //Logo Image
    private $image;              //image

    public Review $review;
    public AggregateRating $aggregateRating;
    public Offers $offers;
    public FAQ $faq;

    public function setURL($url){
        $this->url = $url;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setAddressLocality($addressLocality){
        $this->addressLocality = $addressLocality;
    }

    public function setPostalCode($postalCode){
        $this->postalCode = $postalCode;
    }

    public function setStreetAddress($streetAddress){
        $this->streetAddress = $streetAddress;
    }

    public function setOpeningHours($openingHours){
        $this->openingHours = $openingHours;
    }

    public function setTelephone($telephone){
        $this->telephone = $telephone;
    }

    public function setLogo($logo){
        $this->logo = $logo;
    }

    public function setImage($image){
        $this->image = $image;
    }


    public function __construct()
    {
        $this->url = config('schemaOrg.localBusiness.url','');
        $this->name = config('schemaOrg.localBusiness.name','');
        $this->addressLocality = config('schemaOrg.localBusiness.addressLocality','');
        $this->postalCode = config('schemaOrg.localBusiness.postalCode','');
        $this->streetAddress = config('schemaOrg.localBusiness.streetAddress','');
        $this->openingHours = config('schemaOrg.localBusiness.openingHours','');
        $this->telephone = config('schemaOrg.localBusiness.telephone','');
        $this->logo = config('schemaOrg.localBusiness.logo','');
        $this->image = config('schemaOrg.localBusiness.image','');
    }

    public function useAggregateRating(){
        $this->aggregateRating = new AggregateRating();
    }
    public function useFAQ(){
        $this->faq = new FAQ();
    }
    public function useOffers(){
        $this->offers = new Offers();
    }
    public function useReview(){
        $this->review = new Review;
        $this->review->itemReviewed = $this->name;
        $this->review->itemReviewedType = "Organization";
    }

    public function getSchema(){
        $schema = $this->start();
        $schema .= "\t\t\t".'"@type": "LocalBusiness",'."\n";
        $schema .= "\t\t\t".'"name": "'.$this->name.'",'."\n";
        $schema .= "\t\t\t".'"address": {'."\n";
        $schema .= "\t\t\t\t".'"@type": "PostalAddress",'."\n";
        $schema .= "\t\t\t\t".'"addressLocality": "'.$this->addressLocality.'",'."\n";
        $schema .= "\t\t\t\t".'"postalCode": "'. $this->postalCode.' ",'."\n";
        $schema .= "\t\t\t\t".'"streetAddress": "'. $this->streetAddress.'"'."\n";
        $schema .= "\t\t\t".'},'."\n";
        if($this->openingHours != '') $schema .= "\t\t\t".'"openingHours": "'.$this->openingHours.'",'."\n";
        if($this->telephone != '') $schema .= "\t\t\t".'"telephone": "'.$this->telephone.'",'."\n";
        $schema .= "\t\t\t".'"url": "'. $this->url .'",'."\n";
        if($this->logo != '') $schema .= "\t\t\t".'"logo": "'.asset($this->logo).'",'."\n";
        if($this->image != '') $schema .= "\t\t\t".'"image": "'.asset($this->image).'",'."\n";

        //Review
        if(isset($this->review)) $schema .= $this->review->getSchema();
        //AggregateRating
        if(isset($this->aggregateRating)) $schema .= $this->aggregateRating->getSchema();
        //offers
        if(isset($this->offers)) $schema .= $this->offers->getSchema();
        //faq
        if(isset($this->faq)) $schema .= $this->faq->getSchema();

        //Letzte Komma wieder entfernen
        $schema = substr($schema, 0, -2) . "\n";
        $schema .= $this->ende();

        return $schema;
    }

    private function start(){
        return "\t\t".'"LocalBusiness": {' . "\n";
    }

    private function ende(){
        return "\t\t},\n";
    }

}
