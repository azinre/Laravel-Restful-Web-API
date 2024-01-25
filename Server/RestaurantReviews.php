<?php
include_once './Common/Classes.php';
include_once './Common/Functions.php';

if($_SERVER['REQUEST_METHOD'] === "GET")
{
    if (isset($_GET["action"]) && $_GET["action"] === "GetRestaurantNames") 
    {
        $names = GetRestaurantNames();
        print json_encode($names);
        exit();
    }
    elseif (isset($_GET["id"])){
        $review = GetRestaurantReviewById($_GET['id']);
        if ($review){
            $jsonStr = json_encode($review);
            print $jsonStr;
            exit();
        }
    }
    else {
        $reviews = GetAllRestaurantReviews();
        if ($reviews){
            $jsonStr = json_encode($reviews);
            print $jsonStr;
            exit();
        }
    }
    http_response_code(400);
    exit();
}

else if ($_SERVER['REQUEST_METHOD'] === "PUT")
{
    $requestHeaders = array_change_key_case(getallheaders(), CASE_LOWER);
    if (!isset ($requestHeaders['contetnt-type'])
            || strpos($requestHeaders['contetnt-type'], 'application/json') !== false){
        $requestBody = file_get_contents('php://input');
        $updaterest = json_decode($requestBody);
        if($updaterest != null){
            if(UpdateRestaurant($updaterest)){
                http_response_code(200);
                exit();
            }

        }
    }
    http_response_code(400);
    exit();    
}

else if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
    // Delete the specified restaurant review
    if (isset($_GET['id'])) {
        if (DeleteRestaurantReviewById($_GET['id']))
        http_response_code(200);
            exit();
        }    
    http_response_code(400);
    exit();    
}
    
  

else if ($_SERVER['REQUEST_METHOD'] === "POST")
{
    $requestHeaders = array_change_key_case(getallheaders(), CASE_LOWER);
    if (!isset ($requestHeaders['contetnt-type'])
            || strpos($requestHeaders['contetnt-type'], 'application/json') !== false){
        $requestBody = file_get_contents('php://input');
        $newrest = json_decode($requestBody);
        if($newrest != null){
            if(SaveNewRestaurant($newrest)){
                http_response_code(200);
                exit();
            }

        }
    }
    http_response_code(400);
    exit();    
}



