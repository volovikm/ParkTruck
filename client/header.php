<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");

class Header extends Account
{
    public $role;
    public $user_data;

    public function displayHeader() 
    {
        if($this->user_data !== false)
        {
            $user_data=$this->user_data;

            //Данные авторизации с авторизацией
            $telephone=$user_data["telephone"];
            $role=$this->roleToText($user_data["role"]);
            $this->role=$user_data["role"];

            $auth_data='
            <div>
                <div class="auth_info">
                    <div>
                        '.$role.': '.$telephone.'
                    </div>
                    <div id="logout_button" class="link_button logout_link">
                        Выйти
                    </div>
                </div>
            </div>
            ';

            //Список меню с авторизацией
            $menu_list='';
            if(($this->role) =="driver")
            {
                $menu_list=$menu_list.'
                <div><a href="transport.php" class="menu-item">Транспортные средства</a><div>
                <div><a href="rent.php" class="menu-item">Бронирования</a><div>
                ';
            }
            
        }
        else
        {
            //Данные авторизации без авторизации
            $auth_data='
            <div>
                <div id="auth_button" class="main_button header-button">
                    Войти
                </div>
                <div id="reg_button" class="secondary_button header-button">
                    Регистрация
                </div>
            </div>
            ';

            //Список меню без авторизации
            $menu_list='';
        }
        $auth_role=$this->role;

        //Разметка шапки
        $header='
        <link rel="stylesheet" href="styles/main.css">
        <link rel="stylesheet" href="styles/header.css">
        <header class="header">

            <div class="side left">
                <div class="menu">
                    <input type="checkbox" id="burger-checkbox" class="burger-checkbox">
                    <label for="burger-checkbox" class="burger"></label>
                    <ul class="menu-list">
                        <div><a href="map.php" class="menu-item">Главная</a><div>
                        '.$menu_list.'
                        <div><a href="support.php" class="menu-item">Поддержка</a><div>
                    </ul>
                </div>
            </div>

            <div class="side right">
                <div class="auth_data">
                    '.$auth_data.'
                </div>
            </div>

            <script src="scripts/main.js"></script>
            <script src="scripts/header.js"></script>

        </header>
        ';

        return($header);
    }
}

?>