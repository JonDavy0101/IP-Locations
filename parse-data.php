<?php

include_once('../connect.php');
include_once('../files-list.php');

$raw_str = file_get_contents(FAILED_LOGIN_SAMPLE_LOG);
$entries = explode(PHP_EOL,$raw_str);
// print_r($entries);

$Feed_data = array();
foreach($entries as $entry) {
	$data = array();
	$entryArray = explode(" ",$entry);

	// prepare and bind
	$stmt = $conn->prepare("INSERT IGNORE `invalidLogins` (`id`, `date`, `username`, `portAttempt`, `ip`, `port`) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssi", $id, $date, $username, $portAttempt, $ip, $port);

	$data['date'] = date('Y-m-d H:i:s', strtotime($entryArray[0] . '-' . $entryArray[1] . ' ' . $entryArray[2]));
	$data['username'] = $entryArray[7];
	$data['port-attempt'] = $entryArray[4];
	$data['ip'] = $entryArray[9];
	$data['port'] = $entryArray[11];

	// escape variables for security, set parameters and execute
	$date = mysqli_real_escape_string($conn, $data['date']);
	$username = mysqli_real_escape_string($conn, $data['username']);
	$portAttempt = mysqli_real_escape_string($conn, $data['port-attempt']);
	$ip = mysqli_real_escape_string($conn, $data['ip']);
	$port = mysqli_real_escape_string($conn, $data['port']);
	$id = $date . $username . $ip;
	$stmt->execute();
	$stmt->close();

	array_push($Feed_data,$data);
}

echo "Submission Successful\n";

$conn->close();
?>
