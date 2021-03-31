<?php
/**
 * Created by PhpStorm.
 * User: gognj_000
 * Date: 1/25/2017
 * Time: 4:27 PM
 */

 //Повикување на autoload.php која ги лоадира останатите библиотеки кои ги имаме инсталирано со Composer
 require_once __DIR__ . '/vendor/autoload.php';

 //Ако не е стартувана сесијата, стартувај ја.
if(!session_id()) {
    session_start();
}

// Конфигурирање на Facebook клиентот со клучевите од нашата Facebook апликација.
$fb = new Facebook\Facebook([
    'app_id' => '1811428989124622',
    'app_secret' => '7aad9ccaa0fdc235ee853bd570e7a181',
    'default_graph_version' => 'v2.8',
]);
//Помошна класа овозможена од Facebook библиотеката
$helper = $fb->getRedirectLoginHelper();

//Пермисии кои ќе се бараат од User-от при најава со Facebook
$permissions = ['email', 'user_likes', 'user_tagged_places', 'public_profile ', 'user_about_me',
    'user_photos', 'user_posts', 'user_videos']; // optional
//Генерирање на логин линк до Facebook. Кој ќе се користи во html <a> таг како линк за логин до Facebook.
$loginUrl = $helper->getLoginUrl('https://fbnotificationmailer.000webhostapp.com/logout.php', $permissions);

?>


<!--Од тука надоле се генерира html кодот за прикажување на страницата-->
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

                <p>Subscribe to our facebook notification mailer and stay tuned.</p>

                <form action="" method="post">
                    <a href="<?php echo $loginUrl ?>" class="btn btn-info">Subscribe with Facebook!</a>
                </form>
            </div>
        </div>
    </div>
</div>


</body>

</html>

