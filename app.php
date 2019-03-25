<?php
/**
 * Created by PhpStorm.
 * User: danielbla
 * Date: 25-3-2019
 * Time: 17:31
 */
session_start();


function run_update_task(){
    $rows = get_database_records();

    while ($row = $rows) {
        create_sensor_data();
    }
}

function get_database_records(){

    $conn = pg_pconnect("dbname=publisher");
    if (!$conn) {
        echo "An error occurred.\n";
        exit;
    }

    $result = pg_query($conn, "SELECT id,mac, value, created_at FROM readings");
    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }
return pg_fetch_row($result);
}


function create_sensor_data($name,$value, $instance_url, $access_token) {
    $url = "$instance_url/services/data/v40.0/sobjects/PlantEvent__e/";

    $content = json_encode(array("ExternalId__c" => $name,"Value__c"=>$value));

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
        array("Authorization: OAuth $access_token",
            "Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ( $status != 201 ) {
        die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }

    echo "HTTP status $status creating event<br/><br/>";

    curl_close($curl);

    $response = json_decode($json_response, true);

    $id = $response["id"];

    return $id;
}

>