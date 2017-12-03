<?php
class TwitterApi
{
    protected $apikey;
    protected $secretkey;
    protected $token;
    protected $secrettoken;
    protected $url = 'https://api.twitter.com/1.1/search/tweets.json';

    public function __construct()
    {

    }

    /**
    * It gets tweeds filtered by location
    *
    *
    */
    public function searchTweetsByCoordinates($coordinates)
    {
        $requestMethod = 'GET';
        $getfield = '?q=test&geocode=37.781157,-122.398720,1mi&count=100';

        $twitter = new TwitterAPIExchange($settings);
        $response =  $twitter->setGetfield($getfield)
            ->buildOauth($this->url, $requestMethod)
            ->performRequest();

        var_dump(json_decode($response));
    }

}


?>