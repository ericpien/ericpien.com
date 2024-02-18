<?php

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'db';
$html = file_get_contents('index.html');
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . $conn -> connect_error);
}

function getSingle($query) {
    global $conn; // in php, global vars needs to be declared within a function to be used

    $result = $conn -> query($query);
    $row = $result -> fetch_row();
    return $row[0];
}

function logVisitor() {
    global $conn;
    $ip = $conn -> real_escape_string($_SERVER['REMOTE_ADDR']);
    $vid = getSingle("SELECT vid FROM visitors WHERE ip = '".$ip."'");
    
    if (!$vid) {
        $conn -> query("INSERT INTO visitors(ip) VALUES ('$ip')");
    }
}

logVisitor();

// Output the content of index.html
echo $html;

?>