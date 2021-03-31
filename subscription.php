<?php

require 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require 'dbconnections.php';

// Го конфигурираме серверот да ги зачувува потенцијалните ерори што би можеле да излезат
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");

//Превземање на содржината испратена од Facebook.
$txt = file_get_contents('php://input');
//Декодирање на String во php object.
$txt = json_decode($txt);


//Facebook ги праќа податоците во низа entry. Ако постои таа низа земи го првиот елемент, таму се наоѓа id на корисникот.
$entries = $txt->entry;
if(!is_null($entries) && count($entries) > 0) {
    $entry = $entries[0];
    $uid = $entry->uid;

//Конектирај се со базата.
    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
//Земи го корисникот според uid-то што го добивме од Facebook.
    $sql = "SELECT * FROM `Notifications` WHERE `uid` = '".$uid."' LIMIT 1";
    $result = $conn->query($sql);

	//ако има резултат од пребарувањето
    if ($result->num_rows > 0) {
        // output data of each row
		//Превземи го резултатот
        while ($row = $result->fetch_assoc()) {
            //Земи ja email колоната за корисникот и испрати му email.
            sendEmail($row["email"]);
        }
    }


}

//Потребно за верификација од страна на Facebook дека е валиден серверот кој го имаме внесено на Facebook апликацијата
$challenge = $_REQUEST['hub_challenge'];
$verify_token = $_REQUEST['hub_verify_token'];
if($challenge && $verify_token){
    echo $challenge;
}

//Функција за испраќање на email.
function sendEmail($email){

//Библиотеката PhpMailer. Краирање на нов email. 
    $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'fbnotificationmailer@gmail.com';                 // SMTP username
    $mail->Password = 'qwe123qwe123';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom('fbnotificationmailer@gmail.com', 'FB Notification Mailer');
    $mail->addAddress($email, '');     // Add a recipient
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'Hello,'. $email.'<br/> I am here to inform you that you have been tagged on Facebook. You can check your notifications by clicking '
        .'<a href="https://www.facebook.com/notifications">here</a>.';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	//Праќање на email-от
    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }

}






