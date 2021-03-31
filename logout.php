<?php

require_once __DIR__ . '/vendor/autoload.php';
// Вклучи ја конфигурацијата за поврзување со базата
require_once 'dbconnections.php';

// Креираме нова сесија за најавениот корисник
if(!session_id()) {
    session_start();
}

// Инстанцирај нов Facebook клиент со клучевите со facebook апликацијата
$fb = new Facebook\Facebook([
    'app_id' => '1811428989124622',
    'app_secret' => '7aad9ccaa0fdc235ee853bd570e7a181',
    'default_graph_version' => 'v2.8',
]);

// Инстанцирај помошна класа за генерирање на урл при најава со Facebook
$helper = $fb->getRedirectLoginHelper();

try {
	//По успешна најава со помош на helper-от од Facebook го превземаме AccessToken-от 
    $accessToken = $helper->getAccessToken();
	//Со добиениот AccessToken правиме повик кон Facebook за податоците User Id и Email на корисникот.
    $response = $fb->get('/me?fields=id,email', $accessToken);
	//Помошна функција за превземање на добиениот резултат.
    $userNode = $response->getGraphUser();

	//Ги земаме id и email.
    $id = $userNode['id'];
    $email = $userNode['email'];

	//Се конектираме со MySql базата.
    $conn = mysqli_connect($servername,$username,$password,$dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

	//Додаваме редица во базата за регистрираниот корисник со неговото id и email.
    $sql = "INSERT INTO `Notifications` (`id`, `uid`, `email`) VALUES (NULL, '".$id."', '".$email."');";

    if ($conn->query($sql) === FALSE) {
        die("Insert failed");
    }
    $conn->close();

//Доколку има некоја грешка да се прикаже
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    $error = 'Graph returned an error: ' . $e->getMessage();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    $error = 'Facebook SDK returned an error: ' . $e->getMessage();
} catch(Exception $e){
    $error = $e->getMessage();
}
//Линк кој го користиме за да се вратиме назад.
$loginUrl = "https://fbnotificationmailer.000webhostapp.com/login.php";

?>

<!-- Генерирај го хтмлот за прикажување на страната на корисникот. На оваа страна му прикажуваме на корисникот дека е најавен и му даваме можност да се одјави-->
<html>
<head>
    <title>Notification Mailer</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
</head>

<body>

<div class="container">
    <div class="row">
        <div class="span12">
            <div class="thumbnail center well well-small text-center">
                <h2>Facebook Notification Mailer</h2>

                <?php if(isset($error)){   ?>

                    <p><?php echo $error; ?></p>

                <?php }else { ?>

                    <p style="color:#57C1DA;font-size:20px;">You have successfully subscribed to our notification mailing list.</p>

                <?php } ?>


                <form action="/unsubscribe.php" method="post">
                    <input type="submit" class="btn btn-primary" value="Unsubscribe">
                    <input type="hidden" name="uid" value="<?php echo $id ?>">
                    <a href="<?php echo $loginUrl ?>" class="btn btn-info">Go Back</a>
                </form>
            </div>
        </div>
    </div>
</div>


</body>

</html>
