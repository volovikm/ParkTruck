<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/header.php");
$header = new Header();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
$redirect = new Redirect();

//Проверка пользователя
$user_data=$account->complexUserCheck();

//Заголовок
$header->user_data = $user_data;
$header=$header->displayHeader();

?>
<html>
    <head>
        <script src="scripts/jquery_ajax.js"></script>
        <script src="scripts/request.js"></script>
        <script src="scripts/main.js"></script>


        <link rel="stylesheet" href="styles/main.css">
    </head>

    <body>
        <?php echo($header); ?>
    </body>
    

</html>