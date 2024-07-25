<?php
/*
    Класс Session представляет действия с сессией
    Метод sessionStart - открытие сессии
*/

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");

class Session extends DataBaseRequests
{
    public function sessionStart() 
    {
        if(!isset($_SESSION))
        {
            session_start();
        }
    }

    public function saveDataToSession($key,$value)
    {
        $this->sessionStart();

        $_SESSION[$key]=$value;
    }

    public function getDataFromSession($key)
    {
        $this->sessionStart();

        if(isset($_SESSION[$key]))
        {
            return($_SESSION[$key]);
        }

        return(false);
    }
        
}

?>