<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client-new/forms.php");
$form = new Form();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
$redirect = new Redirect();

$user_data=$account->complexUserCheck();
$role=$account->getRole($user_data);

//Проверка пользователя
$view_page_allowed=$account->allowActionByRole($user_data,["unauthorized","driver","admin"]);
if(!$view_page_allowed)
{$redirect->redirectTo($redirect->index);}

//Форма бронирования
$form_data['action']="rent";
$form=$form->parkingPlaceRentForm($form_data);

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
        <?php echo($form); ?>
    </body>
    

</html>