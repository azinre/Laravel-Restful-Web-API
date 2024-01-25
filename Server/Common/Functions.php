<?php
function GetRestaurantNames()
{
    $appConfigs = parse_ini_file("Lab7.ini");
    $xmlFilePath = $appConfigs["xmlFilePath"];
    $restaurants = simplexml_load_file($xmlFilePath);
    $names = array();
    foreach($restaurants->restaurant as $rest)
    {
        $names[] = (string)$rest->name;
    }
    return $names;
}
function GetAllRestaurantReviews()
{
    $appConfigs = parse_ini_file("Lab7.ini");
    $xmlFilePath = $appConfigs["xmlFilePath"];
    $restaurants = simplexml_load_file($xmlFilePath);
    
    $reviews = array();
    for($i = 0; $i < count($restaurants->restaurant); $i++)
    {
        $rest = $restaurants->restaurant[$i];
        
        $restData = new Restaurant;
        $restData->id = $i;
        $restData->name = (string)$rest->name;
        $restData->summary = (string)$rest->summary;
        $restData->foodType = (string)$rest->food_type;
        $restData->rating = new Rating;
        $restData->rating->currentRating = (int)$rest->rating;
        $restData->rating->minRating = (int)$rest->rating["min"];
        $restData->rating->maxRating = (int)$rest->rating["max"];
        $restData->cost->currentCost = (int)$rest->cost;
        $restData->cost->minCost = (int)$rest->cost["min"];
        $restData->cost->maxCost = (int)$rest->cost["max"];
        $restData->address->street = (string)$rest->address->street_address;
        $restData->address->city = (string)$rest->address->city;
        $restData->address->provstate = (string)$rest->address->state_province;
        $restData->address->postalzipcode = (string)$rest->address->zip_postal_code;
        
        $reviews[] = $restData;
    }
    return $reviews;
}

function GetRestaurantReviewById(int $id)
{
    $appConfigs = parse_ini_file("Lab7.ini");
    $xmlFilePath = $appConfigs["xmlFilePath"];
    $restaurants = simplexml_load_file($xmlFilePath);
 
    if ($id >= 0 && $id < count($restaurants->restaurant))
    {
        $rest = $restaurants->restaurant[$id];
        
        $restData = new Restaurant;
        $restData->id = $id;
        $restData->name = (string)$rest->name;
        $restData->summary = (string)$rest->summary;
        $restData->foodType = (string)$rest->food_type;
        $restData->rating = new Rating;
        $restData->rating->currentRating = (int)$rest->rating;
        $restData->rating->minRating = (int)$rest->rating["min"];
        $restData->rating->maxRating = (int)$rest->rating["max"];
        $restData->cost->currentCost = (int)$rest->cost;
        $restData->cost->minCost = (int)$rest->cost["min"];
        $restData->cost->maxCost = (int)$rest->cost["max"];
        $restData->address->street = (string)$rest->address->street_address;
        $restData->address->city = (string)$rest->address->city;
        $restData->address->provstate = (string)$rest->address->state_province;
        $restData->address->postalzipcode = (string)$rest->address->zip_postal_code;
        
        return $restData;
    }
    else
    {       
        return false;
    }
}

function UpdateRestaurant($updatedRest)
{
    $appConfigs = parse_ini_file("Lab7.ini");
    $xmlFilePath = $appConfigs["xmlFilePath"];
    $restaurants = simplexml_load_file($xmlFilePath);
 
    if ($updatedRest->id >= 0 && $updatedRest->id < count($restaurants->restaurant))
    {
        $restData = $restaurants->restaurant[$updatedRest->id];
        
        $restData->name = $updatedRest->name;
        $restData->summary = $updatedRest->summary;
        $restData->food_type = $updatedRest->foodType;
       
        $restData->rating= $updatedRest->rating->currentRating ;
        
        $restData->cost = $updatedRest->cost->currentCost;
        
        $restData->address->street_address = $updatedRest->address->street;
        $restData->address->city = $updatedRest->address->city;
        $restData->address->state_province = $updatedRest->address->provstate;
        $restData->address->zip_postal_code = $updatedRest->address->postalzipcode;
        
        $restaurants->asXML($xmlFilePath);
        
        return true;
    }
    else
    {
        return false;
    }
}

function DeleteRestaurantReviewById(int $id)
{
    $appConfigs = parse_ini_file("Lab7.ini");
    $xmlFilePath = $appConfigs["xmlFilePath"];
    $restDoc = new DOMDocument();
    $restDoc->load($xmlFilePath);
    $elementToDelete = $restDoc->getElementsByTagName("restaurant")[$id];
    
    if($elementToDelete){
        $elementToDelete->parentNode->removeChild($elementToDelete);
        $restDoc->save($xmlFilePath);
        return true;
    }
    return false;
}

function SaveNewRestaurant($newRest)
{
    $appConfigs = parse_ini_file("Lab7.ini");
    $xmlFilePath = $appConfigs["xmlFilePath"];
    $restDoc = new DOMDocument();
    $restDoc->load($xmlFilePath);
    
    $restData = $restDoc->createElement("restaurant");
    $restData->appendChild(new DOMElement("name", $newRest->name ));
    $restData->appendChild(new DOMElement("summary", $newRest->summary ));
    $restData->appendChild(new DOMElement("food_type", $newRest->foodType ));
    
    $address = $restDoc->createElement("address");
    $address->appendChild(new DOMElement("street_address", $newRest->address->street ));
    $address->appendChild(new DOMElement("city", $newRest->address->city ));
    $address->appendChild(new DOMElement("state_province", $newRest->address->provstate ));
    $address->appendChild(new DOMElement("zip_postal_code", $newRest->address->postalzipcode ));
    $restData->appendChild($address);
    
    $rating = $restDoc->createElement("rating", $newRest->rating->currentRating);
    $min = $restDoc->createAttribute("min");
    $min->value = $newRest->rating->minRating;
    $rating->appendChild($min);
    $max = $restDoc->createAttribute("max");
    $max->value = $newRest->rating->maxRating;
    $rating->appendChild($max);
    $restData->appendChild($rating);
    
    $cost = $restDoc->createElement("cost", $newRest->cost->currentCost);
    $min = $restDoc->createAttribute("min");
    $min->value = $newRest->cost->minCost;
    $cost->appendChild($min);
    $max = $restDoc->createAttribute("max");
    $max->value = $newRest->cost->maxCost;
    $cost->appendChild($max);
    $restData->appendChild($cost);
    
    $restDoc->documentElement->appendChild($restData);
    
    $restDoc->save($xmlFilePath);
    return true;

}

function loggingToFile(string $logtext) {
    $fp = fopen("c:/temp/phplog.txt", 'a');
    fwrite($fp, "\n");
    fwrite($fp, $logtext);
    fclose($fp);
}

