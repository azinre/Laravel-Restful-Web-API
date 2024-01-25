<?php
class Rating {
    public int $minRating;
    public int $maxRating;
    public int $currentRating;
    public function __construct() {
        $this->minRating = 0;
        $this->maxRating = 5;
    }
}
class Cost  {
    public int $minCost;
    public int $maxCost;
    public int $currentCost;
    
    public function __construct() {
        $this->minCost = 0;
        $this->maxCost = 5;
    }
}
class Address {
    public string $street;
    public string $city;
    public string $provstate;
    public string $postalzipcode;
  
}

class Restaurant{
    public int $id;
    public string $name;
    public Address $address;
    public string $foodType;
    public string $summary;
    public Cost $cost;
    public Rating $rating;
    public function __construct() 
    {
        $this->address = new Address;
        $this->cost = new Cost;
        $this->rating = new Rating;  
    }
} 


