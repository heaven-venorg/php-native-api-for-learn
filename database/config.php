<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rest_api";

$con = mysqli_connect($host, $user, $pass, $dbname);
if ($con->connect_errno) {
    die("Connect Error" . $con->connect_errno);
}