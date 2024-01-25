<?php
include_once "./Common/Classes.php";
include_once "./Common/Functions.php";

    session_start();
   
    $appConfigs = parse_ini_file("Lab7Part2.ini");
    extract($appConfigs);
    
    extract($_POST);
    $confirmation = false;
    
    // Fetching all restaurant names from the restaurant review Web API
    $restNames = Array();
    $curlHandle = curl_init($restaurantNamesAPIURL);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curlHandle, CURLOPT_VERBOSE, true); 
    // curl_setopt($curlHandle, CURLOPT_STDERR, $verboseFile);
    // Bypass SSL certificate verification
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curlHandle);
    //var_dump($response);
    //fclose($verboseFile);
    if ($response === false) {
        echo "cURL Error: " . curl_error($curlHandle);
    } else {
        $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        if ($responseCode >= 200 && $responseCode < 300) {
            $restNames = json_decode($response);
            //var_dump($restNames);
        } else {
            echo "Error fetching restaurant names: HTTP status code $responseCode";
        }
    }

    curl_close($curlHandle);

    
    // Code to handle form submission for viewing a selected restaurant's review
    if (isset($btnRestSelected) && $drpRestaurant !== '-1' && $drpRestaurant !== '-2')
    { 
         //Add your code here to get the user selected restaurant review from the restaurant review Web API
         //and display the result on the page. 
        $constructed_url = $restaurantReviewAPIURL . "/" . $drpRestaurant;
        //var_dump($constructed_url);
        $curlHandle = curl_init($constructed_url);
        //var_dump($curlHandle);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curlHandle);
        //var_dump($response);
        if ($response === false) {
            $confirmation = "cURL Error: " . curl_error($curlHandle);
        }else { $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

            curl_close($curlHandle);
            if ($responseCode >= 200 && $responseCode < 300) {
            $rest = json_decode($response);
            displayRestaurantDataOnPage($rest);
            } else {
            $confirmation = "Error fetching restaurant review: HTTP status code $responseCode";
            }
        }
    }
     

         //Uncomment the following line to display the restaurant review.
         //displayRestaurantDataOnPage($rest);

     
    else if (isset($btnRestSelected) && $drpRestaurant === '-2')
    {
        $rest = new Restaurant();
        displayRestaurantDataOnPage($rest);
    }

    // Code to handle form submission for saving changes to a restaurant's review
    else if (isset($btnSaveChange))
    {
        
        $rest = getRestaurantDataFromPage();
        $drpRatingMax = $rest->rating->maxRating;
        $drpRatingMin = $rest->rating->minRating;
        $drpCostMax = $rest->cost->maxCost;
        $drpCostMin = $rest->cost->minCost;
        //var_dump($rest);
        $curlHandle = curl_init($restaurantReviewAPIURL . "/" . $drpRestaurant);
        
        curl_setopt_array($curlHandle, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: json'
            ),
            CURLOPT_POSTFIELDS => json_encode($rest)
        ));
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curlHandle);
        //var_dump($response);
        $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
        if ($responseCode >= 200 && $responseCode < 300) {
            $confirmation = "Revised Restaurant Review has been saved.";
        } else {
            $confirmation = "Something went wrong, revised Restaurant Review is NOT saved.";
        }
        
    }
    
    else if (isset($btnDelete)) {
        
            //Add your code here to delete the user selected restaurant review from the restaurant review Web API

        $curlHandle = curl_init($restaurantReviewAPIURL . "/" . $drpRestaurant);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandle);
        $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
        //if (strpos($responseCode, '2') === 0) {
        if ($responseCode >= 200 && $responseCode < 300) {
            header("Location: RestaurantReviews.php");
            // exit();
        } else {
            $confirmation = "Something went wrong, this Restaurant Review is NOT deleted.";
        }
        
    }
    
    else if (isset($btnSaveNew)) {
        $rest = getRestaurantDataFromPage(); // Get the restaurant data from the form
    
        $curlHandle = curl_init($restaurantReviewAPIURL);
        curl_setopt_array($curlHandle, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json', 
                'Accept: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($rest)
        ));
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false); // For development only
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false); // For development only
    
        $response = curl_exec($curlHandle);
        $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
    
        if ($responseCode >= 200 && $responseCode < 300) {
            header("Location: RestaurantReviews.php");
            exit();
        } else {
            $confirmation = "Something went wrong, new Restaurant Review is NOT saved. Response code: $responseCode";
        }
    }
    
    include "./Common/Header.php";
?>

<div class="container"> 
     <div class="row vertical-margin">
        <div class="col-md-10 text-center"><h1>Online Restaurant Review</h1></div>
    </div>
    <br/>
    <form action="RestaurantReviews.php" method="post" id="restaurant-review-form">
        <p>Select a restaurant from the dropdown list to view/edit its review or create a new restaurant review</p>
        <div class="row form-group">
            <div class="col-md-2"><label>Restaurant:</label></div>
            <div class="col-md-6">                
                <select name="drpRestaurant" id="drpRestaurant" class="form-control" onchange="onRestaurantChanged();">
                    <option value="-1">Select ... </option>
                    <?php 
                        foreach ($restNames as $index => $name) {
                            echo "<option value='{$index}' " . (isset($drpRestaurant) && $drpRestaurant == $index ? 'selected' : '') . ">{$name}</option>";
                        }
                    ?>
                    <option disabled>──────────</option>
                    <option value="-2" <?php echo (isset($drpRestaurant) && $drpRestaurant === '-2' ? 'selected' : '') ?>>Create a new Restaurant review</option>
                </select>
                <input type="submit" name="btnRestSelected" id="btnRestSelected" style="display: none" value="SelectRest">
            </div>
        </div>
        <div id="restaurant-info" >
            <div class="row form-group" style="display: <?php print isset($drpRestaurant) && $drpRestaurant === '-2' ? 'block':'none';?>">
                <div class="col-md-2"><label>Restaurant Name:</label></div>
                <div class="col-md-6">
                    <input type="text" class="form-control"  style="width : 100%" name="txtRestName" value="<?php print isset($txtRestName)? $txtRestName:""; ?>"/>
                </div>
            </div>          
            <div class="row form-group">
                <div class="col-md-2"><label>Street Address:</label></div>
                <div class="col-md-6">
                    <input type="text" class="form-control"  style="width : 100%" name="txtStreetAddress" value="<?php print isset($txtStreetAddress)? $txtStreetAddress:""; ?>"/>
                </div>
            </div>
             <div class="row form-group">
                <div class="col-md-2"><label>City:</label></div>
                <div class="col-md-6">
                    <input type="text" class="form-control" style="width : 100%" name="txtCity"  value="<?php print isset($txtCity)? $txtCity:""; ?>"/>
                </div>
            </div>
             <div class="row form-group">
                <div class="col-md-2"><label>Province/State:</label></div>
                <div class="col-md-6">
                    <input type="text" class="form-control"  style="width : 100%" name="txtProvinceState"  value="<?php print isset($txtProvinceState)? $txtProvinceState:""; ?>"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-2"><label>Postal/Zip Code:</label></div>
                <div class="col-md-6">
                    <input type="text" class="form-control"  style="width : 100%" name="txtPostalZipCode"  value="<?php print isset($txtPostalZipCode)? $txtPostalZipCode:"";  ?>"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-2"><label>Summary:</label></div>
                <div class="col-md-6">
                    <textarea class="form-control" rows="6" style="width : 100%" name="txtSummary" ><?php print isset($txtSummary)? $txtSummary:"";?></textarea> 
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-2"><label>Food Type:</label></div>
                <div class="col-md-6">
                    <input type="text" class="form-control" style="width : 100%" name="txtFoodType"  value="<?php print isset($txtFoodType)? $txtFoodType:""; ?>"/>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-2"><label>Cost:</label></div>
                <div class="col-md-6">
                    <select name="drpCost" class="form-control">
                        <?php 
                            for($i = $drpCostMin; $i <= $drpCostMax; $i++) 
                            {
                                print "<option value='$i' ".(isset($drpCost) && $drpCost == $i ? 'Selected' :'' )." >$i</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-2"><label>Rating:</label></div>
                <div class="col-md-6">
                    <select name="drpRating" class="form-control">
                        <?php 
                            for($i = $drpRatingMin; $i <= $drpRatingMax; $i++) 
                            {
                                print "<option value='$i' ".(isset($drpRating) && $drpRating == $i ? 'Selected' :'' )." >$i</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row form-group" style="display: <?php print isset($drpRestaurant) && $drpRestaurant === '-2' ? 'none':'block';?>">
                <div class="col-md-10 col-md-offset-2">
                    <input type='submit'  class="btn btn-primary btn-min-width" name='btnSaveChange' value='Save Changes'/>
                    &nbsp; &nbsp;
                    <input type='submit'  class="btn btn-secondary btn-min-width" name='btnDelete' value='Delete This Restaurant'
                           onclick="return confirm('Please confirm to delete restaurant <?php print isset($txtRestName) ? $txtRestName:"" ; ?>');"/>
                </div>
            </div>
            <div class="row form-group" style="display: <?php print isset($drpRestaurant) && $drpRestaurant === '-2' ? 'block':'none';?>">
                <div class="col-md-10 col-md-offset-2">
                    <input type='submit'  class="btn btn-primary btn-min-width" name='btnSaveNew' value='Save New Restaurant'/>
                </div>
            </div>
            <div class="row" style="display: <?php print ($confirmation ?  'block' :'none' )?>" >
                <div class="col-md-8"><Label ID="lblConfirmation" class="form-control alert-success">
                    <?php print isset($confirmation)? $confirmation:""; ?></Label>
                </div>
            </div>
        </div>
    </form>
</div>
<br/>

<script type="text/javascript">
if (document.getElementById('drpRestaurant').value === "-1")
{ 
    document.getElementById('restaurant-info').style.display = 'none';
}

//event handler for restaurant name dropdown list
function onRestaurantChanged( )
{     
    if (document.getElementById('drpRestaurant').value !== "-1")
    {
        var selectRestButton = document.getElementById('btnRestSelected');
        selectRestButton.click();
    } 
    else
    {
         document.getElementById('restaurant-info').style.display = 'none';
    }
} 
 </script>
<?php include "./Common/Footer.php"; ?>