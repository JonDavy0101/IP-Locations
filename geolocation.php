<?php

include_once('../connect.php');

// set IP address and API access key 

$access_key = 'add_key';

// select unique ip addresses that haven't been searched yet
$sql = "SELECT DISTINCT invalidLogins.ip FROM invalidLogins2 WHERE invalidLogins.ip NOT IN (SELECT ipLocation.ip FROM ipLocation)";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
		$searchIp = $row["ip"];
		// Initialize CURL:
		$ch = curl_init('http://api.ipstack.com/' . $searchIp . '?access_key=' . $access_key . '');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Store the data:
		$json = curl_exec($ch);
		curl_close($ch);

		// Decode JSON response:
		$api_result = json_decode($json, true);

		// prepare and bind
		$stmt = $conn->prepare("INSERT INTO `ipLocation` (`id`, `ip`, `type`, `continentCode`, `continentName`, `countryCode`, `countryName`, `regionCode`, `regionName`, `city`, `zip`, `latitude`, `longitude`, `countryCapital`, `countryFlag`, `isEu`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssssssssisssss", $ip, $type, $continentCode, $continentName, $countryCode, $countryName, $regionCode, $regionName, $city, $zip, $latitude, $longitude, $countryCapital, $countryFlag, $isEu);

		// escape variables for security, set parameters and execute
		$ip = mysqli_real_escape_string($conn, $api_result['ip']);
		$type = mysqli_real_escape_string($conn, $api_result['type']);
		$continentCode = mysqli_real_escape_string($conn, $api_result['continent_code']);
		$continentName = mysqli_real_escape_string($conn, $api_result['continent_name']);
		$countryCode = mysqli_real_escape_string($conn, $api_result['country_code']);
		$countryName = mysqli_real_escape_string($conn, $api_result['country_name']);
		$regionCode = mysqli_real_escape_string($conn, $api_result['region_code']);
		$regionName = mysqli_real_escape_string($conn, $api_result['region_name']);
		$city = mysqli_real_escape_string($conn, $api_result['city']);
		$zip = mysqli_real_escape_string($conn, $api_result['zip']);
		$latitude = mysqli_real_escape_string($conn, $api_result['latitude']);
		$longitude = mysqli_real_escape_string($conn, $api_result['longitude']);
		$countryCapital = mysqli_real_escape_string($conn, $api_result['location']['capital']);
		$countryFlag = mysqli_real_escape_string($conn, $api_result['location']['country_flag']);
		$isEu = mysqli_real_escape_string($conn, $api_result['location']['is_eu']);
		$stmt->execute();
		$stmt->close();

		echo $isEu;
    }
} else {
    $conn->close();
    exit("0 new addresses \n");
}

echo "Submission successful \n";
$conn->close();













/*

// Initialize CURL:
$ch = curl_init('http://api.ipstack.com/'.$ip.'?access_key='.$access_key.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Store the data:
$json = curl_exec($ch);
curl_close($ch);

// Decode JSON response:
$api_result = json_decode($json, true);

// Output the "capital" object inside "location"
echo $api_result['location']['capital'] . "\n";
echo "IP: " . $api_result['ip'] . "\n";
echo "Country: " . $api_result['country_name'] . "\n";

*/

?>