<?php

defined('APP_ACCESS') or exit('Access denied');

define('SITE_NAME','Domain Suggestions');

// see GoDaddy API documentation - https://developer.godaddy.com/doc
// url to check domain availability
//https://api.ote-godaddy.com

$API_KEY = '3mM44UbCEmWE6L_STNndtN6ekZBCvGNbHfS6t';
$API_SECRET = '8dWb4Fi8tt79pLz4KCDApv';

$prepend = '';

$typer = 'extension';
//$typer = 'EXTENSION';
$typer = 'KEYWORD_SPIN';

$msg = '';
$domainsAvailable = $searchDNAvailable = 0;


// if the submit button is pressed
if(isset($_POST['submit']) && $_POST['submit'] == 'Search'){

    // set error message by default
    $msg = '<p>Please enter keywords to search variations.</p>';

    // time to clean up submitted keywords to prevent SQL injection
    $_POST['domain'] = filter_var($_POST['domain'], FILTER_SANITIZE_STRING);
    $_POST['domain'] = RemoveSpecialChar($_POST['domain']);
    $keywords = $prepend.' '.$_POST['domain'];
    $keywords = rtrim($keywords);

    // if $_POST['domain'] is NOT empty, means contains a value
    if(!empty($keywords)){

        $msg = '';
        $typer = 'KEYWORD_SPIN';
        $searchDNAvailable = 1;
        $searchdn = getDomains($typer,$keywords);

    }
}

/*****************************************************************************
**** FUNCTIONS
*****************************************************************************/

function getDomains($source,$keys){

    global $API_SECRET, $API_KEY;

    /* function that makes request for domain suggestions using GoDaddy API  */

    //CC_TLD,EXTENSION,KEYWORD_SPIN,PREMIUM,cctld,extension,keywordspin,premium
    //$source = 'KEYWORD_SPIN'; 
    //$source = 'keywordspin';
    //$source = 'EXTENSION';
    //$source = 'extension';
    //$source = 'PREMIUM';
    //$source = 'premium';
    //$source = 'CC_TLD';
    //$source = 'cctld';
    //&& trim($keywords) !== trim($prepend)

    // adds characters for space 
    $keywords = str_replace(' ', '%20', $keys);

    // set country
    $country = 'US';

    // set the number of results to return
    //$limit = '100';
    //$limit = '24';
    $limit = '12';
    //$limit = '6';

    // url for GoDaddy API
    $url = "https://api.ote-godaddy.com/v1/domains/suggest?query=$keywords&country=$country&sources=$source&limit=$limit&waitMs=10000";

    // set your key and secret
    $header = array(
        "Authorization: sso-key $API_KEY:$API_SECRET"
    );

    //open connection
    $ch = curl_init();
    $timeout=60;

    //set the url and other options for curl
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); // Values: GET, POST, PUT, DELETE, PATCH, UPDATE 
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $variable);
    //curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    //execute call and return response data.
    $result = curl_exec($ch);

    //close curl connection
    curl_close($ch);

    // decode the json response
    $dn = json_decode($result, true);

    return $dn;
}


function RemoveSpecialChar($value){
    // removes any special chracters from keyword submittal
    $result  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$value);
    return $result;
}

function printDomainResults($searchData,$ticker=NULL){
    // prepare the html to display domains linked to GoDaddy availablility check and registration page
    global $searchDNAvailable, $domainsAvailable;

    switch($ticker){
        case 'search':
            $throttle = $searchDNAvailable;
        break;
        default:
            $throttle = $domainsAvailable;
        break;
    }
    
    $data = '';

    // check if error code
    if(isset($searchData['code'])){

        $searchDN = 0;

        $errmsg = '';

        $errmsg = explode(":",$searchData['message']);

        $errmsg = '<h2 style="text-align:center;">'.$errmsg[0].'</h2>';
        return $searchData['message'];
    }

    if($throttle){

        $i = $m = 0;
        foreach($searchData as $searchDomainName){

            if($i < 1){
                $data .= '<div class="row mh1">';
            }

            $data .= '<div class="col-md-2 text-center"><a href="https://www.godaddy.com/domains/searchresults.aspx?ci=83269&checkAvail=1&domainToCheck='.$searchDomainName['domain'].'" target="_blank">'.$searchDomainName['domain'].'</a></div>';

            $i++;

            if($i == 6){
                $data .= '</div>';
                $i = 0;
            }

            $m++;

        }

        // echo '<pre>';
        // print_r($domainsList);
        // echo '</pre>';

    }
    // else {
    //     $data = $errmsg;
    // }
    
    //die($ticker);

    $well = 'row';
    $well = 'col-sm';
    //$well = 'container-fluid';

    if(!empty($ticker)){
        $well .= ' well';
    }

    // container-fluid
    $data = '<h4 style="margin-top: 7vh !important;">Click to Register Domains</h4><div class="'.$well.'">'.$data.'</div>';

    return $data;
}