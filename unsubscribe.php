<?php

require 'dbconnections.php';

//Преку Post се праќа uid 
if(isset($_POST['uid']) && !is_null($_POST['uid'])){
    $id = $_POST['uid'];

	//Се брише коисникот од базата, unsubscribe.
    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM `Notifications` WHERE `uid`='".$id."'";

    if ($conn->query($sql) === FALSE) {
        die("Delete failed");
    }

}
//Redirect-ирај до login.php после бришењето.
header("Location: https://fbnotificationmailer.000webhostapp.com/login.php");
exit();
