<?php
namespace ITHilbert\Schema;

use ITHilbert\Schema\Types\Breadcrumb;
use ITHilbert\Schema\Types\FAQ;
use ITHilbert\Schema\Types\LocalBusiness;
use ITHilbert\Schema\Types\Product;
use ITHilbert\Schema\Types\AggregateRating;
use ITHilbert\Schema\Types\Offers;
use ITHilbert\Schema\Types\Review;

class Schema{

    public AggregateRating $aggregateRating;
    public Breadcrumb $breadcrumb;
    public FAQ $faq;
    public LocalBusiness $localBusiness;
    public Offers $offers;
    public Product $product;
    public Review $review;

    public function useAggregateRating(){
        $this->aggregateRating = new AggregateRating();
    }
    public function useBreadcrumb(){
        $this->breadcrumb = new Breadcrumb();
        $this->breadcrumb->add(config('schemaOrg.breadcrumb.rootName'), config('schemaOrg.breadcrumb.rootURL'));
    }
    public function useFAQ(){
        $this->faq = new FAQ();
    }
    public function useLocalBusiness(){
        $this->localBusiness = new LocalBusiness();
    }
    public function useOffers(){
        $this->offers = new Offers();
    }
    public function useProduct($productName, $description){
        $this->product = new Product($productName, $description);
    }
    public function useReview($itemReviewed, $itemReviewedType){
        $this->review = new Review;
        $this->review->itemReviewed = $itemReviewed;
        $this->review->itemReviewedType = $itemReviewedType;
    }

    public function getSchema(){
        $schema = $this->start();
        if(isset($this->breadcrumb)) $schema .= $this->breadcrumb->getSchema();
        if(isset($this->localBusiness)) $schema .= $this->localBusiness->getSchema();
        if(isset($this->product)) $schema .= $this->product->getSchema();
        if(isset($this->review)) $schema .= $this->review->getSchema();
        if(isset($this->aggregateRating)) $schema .= $this->aggregateRating->getSchema();
        if(isset($this->offers)) $schema .= $this->offers->getSchema();
        if(isset($this->faq)) $schema .= $this->faq->getSchema();

        $schema = $this->removeLastKomma($schema);
        $schema .= $this->ende();

        return $schema;
    }

    /**
     * Öffnet einen Schema.org Script
     *
     * @return string
     */
    private function start(){
        $schema = '<script type="application/ld+json">'. "\n";
        $schema .= "\t{\n";
        $schema .= "\t\t". '"@context": "https://schema.org/",'. "\n";

        return $schema;
    }

    /**
     * Schließt das Schema.org Script
     *
     * @return string
     */
    private function ende(){
        $schema = "\t}\n";
        $schema .= "\t</script>\n";

        return $schema;
    }

    private function removeLastKomma($schema){
        //Letzte Komma wieder entfernen
        return substr($schema, 0, -2) . "\n";
    }
}
