<?php

$link = mysqli_connect('localhost', 'root', 'admin', 'nomi') or die(mysqli_connect_error($link));
$addedEvents = array();

DEFINE('WSDL', 'http://tnwebservices-test.ticketnetwork.com/tnwebservice/v3.2/tnwebservicestringinputs.asmx?WSDL');
DEFINE('WEB_CONF_ID', 16583);
DEFINE('SPORTS', 1);
DEFINE('CONCERTS', 2);
DEFINE('THEATER', 3);
DEFINE('OTHER', 4);


$allEvents = array();

getAllEvents(1);
getAllEvents(2);
getAllEvents(3);
getAllEvents(4);

//getVanuesByCity('Washington');
function getAllEvents($parrent, $child = null, $grandChild = null) {
    global $link;
    global $addedEvents;
    $param = array(
        'websiteConfigID' => WEB_CONF_ID,
        'parentCategoryID' => $parrent,
        'childCategoryID' => $child,
        'grandchildCategoryID' => $grandChild,
        'whereClause' => 'CountryID = 217'
    );

    $client = new SoapClient(WSDL);
    $result = $client->__soapCall('GetEvents', array('parameters' => $param));
    if (is_soap_fault($result)) {
        echo '<h2>Fault</h2><pre>';
        print_r($result);
        echo '</pre>';
    } elseif (empty($result)) {
        return "No results match the specified terms";
    } else {
//        print_r($result->GetEventsResult->Event);
//        exit;
        $x = 0;
        $query = array();
        $sqlResult = mysqli_query($link, 'SELECT EventID FROM `events`');
        $allIDs = array();
        foreach ($sqlResult as $id) {
            $allIDs[] = $id['EventID'];
        }
        foreach ($result->GetEventsResult->Event as $row) {
            if (!in_array($row->ID, $allIDs)) {
                $query[] = '( NULL, ' . $row->ID . ', "' . mysqli_real_escape_string($link, $row->Name) . '", ' . $row->ParentCategoryID . ', ' . $row->ChildCategoryID . ', ' . $row->GrandchildCategoryID . ', ' . $row->CountryID . ', ' . $row->VenueID . ', ' . $row->VenueConfigurationID . ', "' . mysqli_real_escape_string($link, $row->Venue) . '", ' . $row->StateProvinceID . ', "' . mysqli_real_escape_string($link, $row->StateProvince) . '", "' . mysqli_real_escape_string($link, $row->City) . '", ' . $row->Clicks . ', "' . date("Y-m-d H:i:s", strtotime($row->Date)) . '", "' . $row->DisplayDate . '", "' . $row->IsWomensEvent . '", "' . mysqli_real_escape_string($link, $row->MapURL) . '", "' . mysqli_real_escape_string($link, $row->InteractiveMapURL) . '")';
                $x++;
            }
        }
        if (!empty($query)) {
            $addedEvents[$parrent - 1] = $x;
            mysqli_query($link, 'INSERT INTO events (ID, EventID, Name, ParentCategoryID, ChildCategoryID, GrandchildCategoryID, CountryID, VenueID, VenueConfigurationID, Venue, StateProvinceID, StateProvince, City, Clicks, Date, DisplayDate, IsWomensEvent, MapURL, InteractiveMapURL) VALUES ' . implode(',', $query)) or die(mysqli_error($link));
        }
        else{
            echo 'no recored inserted';
        }
    }
}

print_r($addedEvents);
echo array_sum($addedEvents);

























//function getEventsByCityVanue($city = null, $vanue = null) {
//    $param = array(
//        'websiteConfigID' => WEB_CONF_ID,
//        'City' => $city,
//        'Venue' => $vanue
//    );
//
//    $client = new SoapClient(WSDL);
//    $result = $client->__soapCall('GetEvents', array('parameters' => $param));
//    if (is_soap_fault($result)) {
//        echo '<h2>Fault</h2><pre>';
//        print_r($result);
//        echo '</pre>';
//    } elseif (empty($result)) {
//        return "No results match the specified terms";
//    } else {
//        print_r($result->GetEventsResult->Event);
//    }
//}
//
//function getVanuesByCity($city) {
//    $param = array(
//        'websiteConfigID' => WEB_CONF_ID,
//        'parentCategoryID' => 1,
//        'whereClause' => 'ID = 2605746'
//    );
//
//    $client = new SoapClient(WSDL);
//    $result = $client->__soapCall('GetEvents', array('parameters' => $param));
//    if (is_soap_fault($result)) {
//        echo '<h2>Fault</h2><pre>';
//        print_r($result);
//        echo '</pre>';
//    } elseif (empty($result)) {
//        return "No results match the specified terms";
//    } else {
//        print_r($result);
//        $vanues = array();
//        foreach ($result->GetEventsResult->Event as $e) {
//            if (!in_array($e->Venue, $vanues)) {
//                $vanues[] = $e->Venue;
//            }
//        }
//        print_r($vanues);
//    }
//}
