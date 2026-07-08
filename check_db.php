<?php
$mysqli = new mysqli("localhost", "root", "password", "middle74_gl_hfg");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
$res = $mysqli->query("SHOW COLUMNS FROM master_oto_jurnal_detail");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
