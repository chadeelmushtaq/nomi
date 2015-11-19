<?php

DEFINE('WSDL', 'http://tnwebservices-test.ticketnetwork.com/tnwebservice/v3.2/tnwebservicestringinputs.asmx?WSDL');
DEFINE('WEB_CONF_ID', 16583);
DEFINE('SPORTS', 1);
DEFINE('CONCERTS', 2);
DEFINE('THEATER', 3);
DEFINE('OTHER', 4);

//getAllEvents(1);
getVanuesByCity('Washington');

function getAllEvents($parrent, $child = null, $grandChild = null) {
    $param = array(
        'websiteConfigID' => WEB_CONF_ID,
        'parentCategoryID' => $parrent,
        'childCategoryID' => $child,
        'grandchildCategoryID' => $grandChild
    );

    $client = new SoapClient(WSDL);
    $result = $client->__soapCall('GetEvents', array('parameters' => $param));
    if (is_soap_fault($result)) {
        echo '<h2>Fault</h2><pre>';
        print_r($result);
        echo '</pre>';
    } elseif(empty($result)) {
        return "No results match the specified terms";
    } else {
        print_r($result->GetEventsResult->Event);
    }
}


function getEventsByCityVanue($city=null, $vanue = null) {
    $param = array(
        'websiteConfigID' => WEB_CONF_ID,
        'City' => $city,
        'Venue' => $vanue
    );

    $client = new SoapClient(WSDL);
    $result = $client->__soapCall('GetEvents', array('parameters' => $param));
    if (is_soap_fault($result)) {
        echo '<h2>Fault</h2><pre>';
        print_r($result);
        echo '</pre>';
    } elseif(empty($result)) {
        return "No results match the specified terms";
    } else {
        print_r($result->GetEventsResult->Event);
    }
}

function getVanuesByCity($city) {
    $param = array(
        'websiteConfigID' => WEB_CONF_ID,
        'whereClause' => 'ID=2475167"'
    );

    $client = new SoapClient(WSDL);
    $result = $client->__soapCall('GetEvents', array('parameters' => $param));
    if (is_soap_fault($result)) {
        echo '<h2>Fault</h2><pre>';
        print_r($result);
        echo '</pre>';
    } elseif(empty($result)) {
        return "No results match the specified terms";
    } else {
        print_r($result);
        $vanues=array();
        foreach($result->GetEventsResult->Event as $e){
            if(!in_array($e->Venue, $vanues)){
                $vanues[]=$e->Venue;
            }
        }
        print_r($vanues);
    }
}

