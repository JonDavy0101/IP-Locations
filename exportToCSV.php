<?php
 
// Database Connection
require_once("../connect.php");
 
// get Users
$query = "SELECT ip, latitude, longitude FROM ipLocation";
if (!$result = mysqli_query($conn, $query)) {
    exit(mysqli_error($conn));
}
 
$users = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

$output = fopen('./Users.csv', 'w');
fputcsv($output, array('ip', 'latitude', 'longitude'));
 
if (count($users) > 0) {
    foreach ($users as $row) {
        fputcsv($output, $row);
    }
}
?>