<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");

class Header extends Account
{
    public $user_data;

    public function displayHeader($show_info=false,$info_type=false) 
    {
        /*
            $info_type: show_add_parking_button | false
        */

        $account_block_content="";
        $info_block_content="";

        //Список пунктов меню
        $menu_list='
        <div><a href="map.php" class="menu-item">Главная</a></div>
        <div><a href="support.php" class="menu-item">Поддержка</a></div>
        ';

        if($this->user_data !== false) //Заголовок с авторизацией
        {
            $user_data=$this->user_data;

            $telephone=$user_data["telephone"];
            $role=$user_data["role"];

            if($role =="parking_owner")
            {
                $menu_list=$menu_list.'
                <div><a href="rent.php" class="menu-item">Бронирования</a></div>
                <div><a href="parkings.php" class="menu-item">Мои парковки</a></div>
                ';
            }
            if($role =="driver")
            {
                $menu_list=$menu_list.'
                <div><a href="transport.php" class="menu-item">Транспортные средства</a></div>
                <div><a href="rent.php" class="menu-item">Бронирования</a></div>
                ';
            }
            if($role =="admin")
            {
                $menu_list=$menu_list.'
                <div><a href="users.php" class="menu-item">Пользователи</a></div>
                <div><a href="parkings.php" class="menu-item">Парковки</a></div>
                <div><a href="rent.php" class="menu-item">Бронирования</a></div>
                ';
            }

            $account_block_content='
            <div>
                <div class="auth_info">
                    <div class="inline_div">
                        '.$telephone.'
                    </div>
                    <div id="logout_button" class="link_button logout_link inline_div">
                        Выйти
                    </div>
                </div>

                <div class="menu_list">
                    '.$menu_list.'
                </div>
            </div>
            ';

            //Содержимое центрального блока
            if($show_info)
            {
                //Кнопка добавления/отмены парковки
                if($info_type=="show_add_parking_button")
                {
                    $info_block_content='
                    <div class="info_block_buttons_div">
                        <button id="add_parking_button" class="secondary_button info_block_button invisible_input inline_div">Добавить парковку</button>
                        <button id="cancel_add_parking_button" class="secondary_button info_block_button invisible_input">Отменить</button>
                    </div>
                    ';
                }
            }
        }
        else //Заголовок без авторизации 
        {
            $account_block_content="<iframe class='auth_frame' src='./account/auth.php'></iframe>";
        }

        //Разметка шапки
        $header='
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/header.css">

        <div class="header">

            <div class="left-side">

                <div id="account_button" class="account_button">
                </div>

                <div id="account_block" class="account_block">
                    '.$account_block_content.'
                </div>
                
            </div>

            <div class="center-block">

                <div id="info_block" class="info_block">
                    '.$info_block_content.'
                </div>
                
            </div>
        
            <div class="right-side">

                <div id="search_button" class="search_button">
                </div>
                <div id="filter_button" class="filter_button">
                </div>
                
            </div>

            <script src="scripts/main.js"></script>
            <script src="scripts/header.js"></script>

        </div>
        ';

        return($header);
    }
}

?>