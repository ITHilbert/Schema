<?php
namespace ITHilbert\Schema\Types;

use Illuminate\Support\Facades\Request;

use ITHilbert\Schema\Types\AggregateRating;
use ITHilbert\Schema\Types\FAQ;
use ITHilbert\Schema\Types\Offers;
use ITHilbert\Schema\Types\Review;

class Product{

    private $name;
    private $description;
    private $image;
    private $url;
    private $brand;

    public Review $review;
    public AggregateRating $aggregateRating;
    public Offers $offers;
    public FAQ $faq;

    public function setName($name){
        $this->name = $name;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function setImage($image){
        $this->image = $image;
    }

    public function setURL($url){
        $this->url = $url;
    }

    public function setBrand($brand){
        $this->brand = $brand;
    }

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->url = Request::url();
        $this->brand = config('schemaOrg.product.brand', '');
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
        $this->review->itemReviewedType = "Product";
    }



    /**
     * Liefert ein Schema für ein Produkt
     *
     * @param string $produkt
     * @return string
     */
    public function getSchema(){
        $schema = $this->start();
        $schema .= "\t\t".'"@type": "Product",'."\n";
        $schema .= "\t\t".'"name": "'.$this->name.'",'."\n";
        $schema .= "\t\t".'"description": "'.$this->description.'",'."\n";
        $schema .= "\t\t".'"url": "'.$this->url.'",'."\n";
        if($this->image != '') $schema .= "\t\t".'"image": "'. asset($this->image).'",'."\n";

        //Brand
        if( $this->brand != ''){
            $schema .= "\t\t".'"brand": {'."\n";
            $schema .= "\t\t\t".'"@type": "Brand",'."\n";
            $schema .= "\t\t\t".'"name": "'.$this->brand.'"'."\n";
            $schema .= "\t\t".'},'."\n";
        }

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
        $schema = $this->ende();

        return $schema;
    }

    private function start(){
        return "\t\t".'"Prodct": {' . "\n";
    }

    private function ende(){
        return "\t\t},\n";
    }

}
