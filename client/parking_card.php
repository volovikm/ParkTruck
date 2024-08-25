<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/header.php");
$header = new Header();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/forms.php");
$form = new Form();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/request.php");
$request = new Request();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/parking_card.php");
$parking_card = new ParkingCard();

//Проверка пользователя
$user_data=$account->complexUserCheck();

//Заголовок
$header->user_data = $user_data;
$header=$header->displayHeader();

//Действия с карточкой парковки
$form_data=[];
$parking_card_action=$parking_card->defineAction();
switch ($parking_card_action) {
    case "watch":
        $form_data=$parking_card->watchHandler();
        break;
    case "create_new":
        $form_data=$parking_card->createNewHandler($user_data);
        break;
    case "edit":
        $form_data=$parking_card->editHandler($user_data);
        break;
    case "delete":

        break;
}

//Форма парковочной карточки
$form=$form->parkingCardForm($form_data);
?>
<html>
    <head>
        <script src="scripts/jquery_ajax.js"></script>
        <script src="scripts/request.js"></script>
        <script src="scripts/main.js"></script>
        <script src="scripts/moment.js"></script>

        <link rel="stylesheet" href="styles/parking_card.css">
        <link rel="stylesheet" href="styles/main.css">
    </head>

    <body>
        <?php echo($header); ?>
        <?php echo($form); ?>
    </body>
    

</html>