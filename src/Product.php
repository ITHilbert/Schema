<?php
namespace ITHilbert\Schema;

use Illuminate\Support\Facades\Request;

use ITHilbert\Schema\AggregateRating;
use ITHilbert\Schema\FAQ;
use ITHilbert\Schema\Offers;
use ITHilbert\Schema\Review;

class Product{

    public $product;
    public $description;
    public $image;
    public $url;
    public $brand;

    public Review $review;
    public AggregateRating $aggregateRating;
    public Offers $offers;
    public FAQ $faq;

    public function __construct($productName, $description)
    {
        $this->product = $productName;
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
        $this->review->itemReviewed = $this->product;
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
        $schema .= "\t\t".'"name": "'.$this->product.'",'."\n";
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
