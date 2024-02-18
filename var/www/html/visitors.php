<?php

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'db';
$html = file_get_contents('visitors.html');
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

if($_REQUEST['user']) {
    $user = $conn -> real_escape_string($_REQUEST['user']);
    $user_id = getSingle("SELECT user_id FROM users WHERE name = '".$user."'");
    
    if (!$user_id) {
        $conn -> query("INSERT INTO users(name) VALUES ('$user')");
    }
}

if($_REQUEST['log']) {
    // if no user is passed, break
    $user = $conn -> real_escape_string($_REQUEST['user']);
    $log = $conn -> real_escape_string($_REQUEST['log']);
    $user_id = getSingle("SELECT user_id FROM users WHERE name = '".$user."'");
    $date = Date("Y-m-d H:i:s");

    if (!$user) {
        print "Please insert name";
    } else if (!$user_id) {
        $conn -> query("INSERT INTO users(name) VALUES ('$user')");
    } 

    if ($user && $user_id && $log) {
        $conn -> query("INSERT INTO logs(user_id, log, date) VALUES ('$user_id', '$log', '$date')");
    }
}

logVisitor();

echo $html;

$result = $conn -> query("

    SELECT logs.*, users.name 
    FROM logs
    JOIN users ON logs.user_id = users.user_id

    ");

print "<div class=\"outer-margin\" style=\"margin: 1%;\"> <table border=1 class=\"table table-striped\">";
print <<<EOF
<thead>
<tr>
<th scope="col">Visitor</th>
<th scope="col">Visitor's Log</th>
<th scope="col">Date</th>
</tr>
</thead>

<tbody class="table-group-divider">
EOF;

while ($row = $result -> fetch_assoc()) {
    $name = $row['name'];
    $date = $row['date'];
    $log = htmlspecialchars($row['log']);

print <<<EOF
<tr><td>{$name}</td><td>{$log}</td><td>{$date}</td></tr>
EOF;

}
print "</tbody>";
print "</table>";
print "</div>";

?>