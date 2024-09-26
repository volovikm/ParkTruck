<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/header.php");
$header = new Header();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/forms.php");
$form = new Form();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
$redirect = new Redirect();

//Проверка пользователя
$user_data=$account->complexUserCheck();
$view_page_allowed=$account->allowActionByRole($user_data,["admin"]);
if(!$view_page_allowed)
{$redirect->redirectTo($redirect->index);}

//Заголовок
$header->user_data = $user_data;
$header=$header->displayHeader();

//Форма списка пользователей
$form=$form->usersForm();
?>
<html>
    <head>
        <script src="scripts/jquery_ajax.js"></script>
        <script src="scripts/request.js"></script>
        <script src="scripts/main.js"></script>
        <script src="scripts/moment.js"></script>
        
        <script src="scripts/users.js"></script>

        <link rel="stylesheet" href="styles/users.css">
        <link rel="stylesheet" href="styles/main.css">
    </head>

    <body>
        <?php echo($header); ?>
        <?php echo($form); ?>
    </body>
    

</html>