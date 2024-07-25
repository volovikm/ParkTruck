<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/header.php");
$header = new Header();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
$account = new Account();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/client/inputs.php");
$input = new Input();

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/session.php");
$session = new Session();

$user_data=$account->complexUserCheck();

$header->user_data = $user_data;
$header=$header->displayHeader();

//Изменение интерфейса в зависимости от прав ролей

//Кнопка добавления парковки
$allowed_roles=['parking_owner'];
$show_add_parking_button_allowed=$account->allowActionByRole($user_data,$allowed_roles);
$add_parking_button="";
if($show_add_parking_button_allowed){
    $add_parking_button='
    <div class="single_map_button_div">
        <button id="add_parking_button" class="map_button add_parking_button">+</button>
        <button id="cancel_add_parking_button" class="cancel_add_parking_button">&#10006;</button>
    </div>
    ';
}

//select выбора фильтра
$allowed_roles=['parking_owner'];
$show_filter_select_allowed=$account->allowActionByRole($user_data,$allowed_roles);
$filter_select=$input->filterMapSelect();
$filter="";
if($show_filter_select_allowed){

    $filter='
    <div class="filter" id="filter">
        <div class="filter_text">
            Показывать:
        </div>
        <div class="filter_select_div">
            '.$filter_select.'
        </div>
    </div>
    ';
}

//<script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
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

        <?php echo($filter); ?>

        <div class="selection_marker" id="selection_marker"></div>

        <div class="map_buttons_div">
            <?php echo($add_parking_button); ?>
        </div>

    </body>
    

</html>