<?php
require('src/autoload.php');

use phpFastCache\CacheManager;
function getTweeds($search, $lat, $long)
{
    global $twitter;

    // Setup File Path on your config files
    phpFastCache\CacheManager::setDefaultConfig(array(
        "path" => '/tmp', // or in windows "C:/tmp/"
    ));

    // In your class, function, you can call the Cache
    $InstanceCache = CacheManager::getInstance('files');


    $cached = $InstanceCache->getItem("getTweeds $search $lat $long");
    if (!$cached->isHit() || $cached->get() === null)
    {
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $defaultKM = '2km';
        $numTweeds = 10;
        $settings = [
            'consumer_key'              => $twitter['apikey'],
            'consumer_secret'           => $twitter['secretkey'],
            'oauth_access_token'        => $twitter['tokens'],
            'oauth_access_token_secret' => $twitter['secretToken'],
            ];
        $requestMethod = 'GET';
        $getfield = '?q='.$search.'&geocode='.$lat.','.$long.','.$defaultKM;


        $twitter = new TwitterAPIExchange($settings);
        $response =  $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();
        $result = json_decode($response);
        
        $cached->set($result, 3600);// 1 hour
        $InstanceCache->save($cached);
    }
    $result = $cached->get();
    return $result;
}

function getPlaceFull($search)
{
    $placeId = getPlace($search);
    $coordinates = getCoordinates($placeId);
    return [
        'placeId'       => $placeId,
        'coordinates'   => $coordinates
    ];
}
function getCoordinates($placeId)
{
    global $gmapKey;
    $curl = curl_init();

    $url = 'https://maps.googleapis.com/maps/api/place/details/json?';

    $data = array(
          'key' => $gmapKey,
          'placeid' => $placeId
    );

    $url.= http_build_query($data);

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
      ),
    ));

    $coordinates = null;

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $array = json_decode($response);

      $coordinates = $array->result->geometry->location;

    return $coordinates;
}

function getPlace($search)
{
    global $gmapKey;
    $curl = curl_init();
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?';

    $data = array(
          'key' => $gmapKey,
          'input' => $search
    );

    $url.= http_build_query($data);
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
      ),
    ));

    $placeId = null;

        $response = curl_exec($curl);
        
        $err = curl_error($curl);

        $array = json_decode($response);

        $placeId = $array->predictions[0]->place_id;

        curl_close($curl);

    return $placeId;
}


$action = $_GET['type'];

switch ($action) {
    case 'coordinates':
        $search = $_GET['search'];
        $result = getPlaceFull($search);
        break;

    case 'search':
        $search = $_GET['search'];
        $lat    = $_GET['lat'];
        $long   = $_GET['long'];
        $result = getTweeds($search, $lat, $long);
        break;

    default:
        break;
}
echo json_encode($result);
