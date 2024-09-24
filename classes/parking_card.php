<?php

class ParkingCard extends DataBaseRequests
{
    public $name;
    public $action; //create_new | edit | watch | delete
    public $latitude;
    public $longitude;

    public function defineAction() //Определение текущего действия с карточкой
    {
        $action="watch";
        if(isset($_GET['new_parking_card']))
        {
            $action="create_new";
        }
        if(isset($_GET['edit']))
        {
            $action="edit";
        }
        if(isset($_GET['delete']))
        {
            $action="delete";
        }
        return($action);
    }

    public function watchHandler() //Обработчик просмотра карточки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        //Проверки достоверности данных
        if(!isset($_GET['parking_id']))
        {$redirect->redirectTo($redirect->index);}
        $parking_id=$_GET['parking_id'];
        $parking_data=$this->parkingCardDataRequest($parking_id);
        if(!isset($parking_data[0]))
        {$redirect->redirectTo($redirect->index);}

        $parking_data=$parking_data[0];

        $form_data=$parking_data;

        $form_data['parking_id']=$parking_id;
        
        $form_data['action']="watch";

        return($form_data);
    }

    public function createNewHandler($user_data) //Обработчик создания новой карточки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        //Проверка прав
        $role=$account->getRole($user_data);
        $create_new_rights=$rights->createNewParkingRights($user_data,$role);
        if(!$create_new_rights)
        {
            $redirect->redirectTo($redirect->index);
        }

        $form_data['parking_id']="";
        $form_data['name']="";
        $form_data['latitude']=$_GET['latitude'];
        $form_data['longitude']=$_GET['longitude'];
        $form_data['adress']="";
        $form_data['user_id']="";
        $form_data['draft']="0";
        $form_data['properties']="0";

        $form_data['action']="create_new";

        return($form_data);
    }

    public function editHandler() //Обработчик редактирования карточки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        //Проверки достоверности данных
        if(!isset($_GET['parking_id']))
        {$redirect->redirectTo($redirect->index);}
        $parking_id=$_GET['parking_id'];
        $parking_data=$this->parkingCardDataRequest($parking_id);
        if(!isset($parking_data[0]))
        {$redirect->redirectTo($redirect->index);}

        $parking_data=$parking_data[0];

        $form_data=$parking_data;

        $form_data['parking_id']=$parking_id;
        $form_data['action']="edit";

        return($form_data);
    }

}

?>