<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client-new/header.php");
$header_ = new Header();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client-new/forms.php");
$form = new Form();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/session.php");
$session = new Session();

$user_data=$account->complexUserCheck();
$role=$account->getRole($user_data);

$header_->user_data = $user_data;
$header=$header_->displayHeader();

//Кнопка добавления парковки
$allowed_roles=['parking_owner'];
$show_add_parking_button_allowed=$account->allowActionByRole($user_data,$allowed_roles);
$add_parking_button="";
if($show_add_parking_button_allowed){
    $header=$header_->displayHeader(true,"show_add_parking_button");
}

//Превью парковки
$parking_preview_form=$form->parkingPreviewForm($role);

?>
<html>
    <head>
        <script src="scripts/jquery_ajax.js"></script>
        <script src="scripts/request.js"></script>

        <script src="https://api-maps.yandex.ru/2.1/?apikey=7cc01c8e-de4d-44ba-a18c-60b3c16bca02&lang=ru_RU" type="text/javascript"></script>
        <script src="scripts/main.js"></script>
        <script src="scripts/map.js"></script>

        <link rel="stylesheet" href="styles/map.css">
    </head>

    <body>
        <?php echo($header); ?>
        
        <div class="map" id="map"></div>

        <div class="selection_marker" id="selection_marker"></div>

        <div class="parking_preview_div" id="parking_preview_div">
            <?php echo($parking_preview_form); ?>
        </div>

    </body>
    

</html>