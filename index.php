<?php
$host = "n";
$user = "n";
$password = "n";
$dbname = "n";

function returnDate(){
	$timeZone = new \DateTimeZone('America/Toronto');
	$time = new \DateTimeImmutable();
	$time = $time->setTimezone($timeZone);
	return $time->format('Y-m-d H:i:s');
}

function returnDay(){
	$timeZone = new \DateTimeZone('America/Toronto');
	$time = new \DateTimeImmutable();
	$time = $time->setTimezone($timeZone);
	return $time->format('Y-m-d');
}

function cleanArr($input){
	$data = filter_var($input);
	$cleand = htmlspecialchars($data);
	return $cleand;
}

$method = $_SERVER['REQUEST_METHOD'];
$ipv4 = $_SERVER['REMOTE_ADDR'];

$srvArr = "SERVER: " . implode("++", $_SERVER['argv']) . $_SERVER['PHP_SELF'] . $_SERVER['GATEWAY_INTERFACE'] . $_SERVER['SERVER_ADDR'] . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_SOFTWARE'] . $_SERVER['SERVER_PROTOCOL'] . $_SERVER['REQUEST_METHOD'] . $_SERVER['REQUEST_TIME'] . $_SERVER['QUERY_STRING'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_URI'];
$reqArr = implode("++", $_REQUEST);
$time = returnDate();

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$cleansrv = cleanArr($srvArr);
$cleanreq = cleanArr($reqArr);
$sql = "INSERT INTO events (phptime, ipv4, serverArr, requestArr) VALUES ('$time', '$ipv4', '$cleansrv', '$cleanreq')";
$conn->query($sql);
// For error checking
// if ($conn->query($sql) === TRUE) {  } else { echo "Error: " . $sql . "<br>" . $conn->error; }



switch ($method) {
    case 'GET':
    	//if stmt to handle cases where no target is provided in GET request
		if (is_null($_GET['t'])) {
			$target = "null";
		} else {
			$target = $_GET['t'];
		}
    	handleGet($conn, $target);
        break;
    case 'POST':
    	$postStr = implode(",", $_POST);
    	$postStrClean = cleanArr($postStr);
    	$target = cleanArr($_POST['t']);
    	handlePost($conn, $target, $postStrClean);
        break;
    case 'PUT':
        echo "PUT requests not yet supported";
        break;
    case 'DELETE':
        echo "DELETE requests not yet supported";
        break;
    default:
        echo "Invalid request method";
        break;
}

function handleGet($conn, $target){
    switch ($target) {
		case 'HdM3':
			header("Location: /api/select.php?select=HdM3");
			break;
		default:
			header("Location: /api/error.html");
			break;
	}
}

function handlePost($conn, $target, $str){
    switch ($target) {
		case 'DH': // Daily Health submissions
			$postTime = time();
			$postDay = returnDay();
			$postSQL = "INSERT INTO DAILY_HEALTH (timestamp,day,user,q1,q2,q3,q4,q5,q6,q7,q8) VALUES ('$postTime', '$postDay', 'M3AA', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h');";
			$conn->query($postSQL);
			break;
		default:
			header("Location: /api/error.html");
			break;
	}
}


?>
