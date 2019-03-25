<?php
/**
 * Created by PhpStorm.
 * User: danielbla
 * Date: 25-3-2019
 * Time: 18:05
 */
$conn = pg_pconnect("dbname=publisher");
if (!$conn) {
    echo "An error occurred.\n";
    exit;
}

$result = pg_query($conn, "SELECT is,mac, value, created_at FROM readings");
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

while ($row = pg_fetch_row($result)) {
    echo "Author: $row[0]  E-mail: $row[1]";
    echo "<br />\n";
}

>