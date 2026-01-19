<?php
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = date("Y-m-d H:i:s");

    $log = "$time | Opened by: $email | IP: $ip\n";
    file_put_contents("mail_open_log.txt", $log, FILE_APPEND);
}

header("Content-Type: image/png");
readfile("tracker.png");
exit;
