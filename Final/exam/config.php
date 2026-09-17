<?php
//define database connection
define('DB_SERVER','127.0.0.1');
define('DB_USERNAME','piona');
define('DB_PASSWORD', 'cabantog');
define('DB_NAME','itc127-cs2c-2025-cabantog');
//attemp to connect tp the database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
//check if teh connection is unsuccessful
if($link === false)
{
die("ERROR: Could not connect, " . mysqli_connect_error());
}
//set timezone
date_default_timezone_set('Asia/Manila');

?>