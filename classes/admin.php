<?php

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");

class Admin extends DataBaseRequests
{
    public $admin_create_login="CreateAdmin2033";
    public $admin_create_password="WheelCap2033CreateAdmin";
    public $admin_login="Admin";
    public $admin_password="123456789qQ";

    public function createAdmin($login,$password) //Метод создания аккаунта администратора
    {
        if($login!=$this->admin_create_login && $password!=$this->admin_create_password)
        {return(false);}

        //Создание аккаунта администратора
        $reg_data['telephone']=$this->admin_login;
        $reg_data['role']="admin";
        $reg_data['password']=$this->admin_password;
        $reg_data['password_hash']=password_hash($this->admin_password,PASSWORD_DEFAULT);
        $reg_data['reg_confirm_code']="ADMIN";
        $reg_data['reg_confirmed']="1";
        $reg_data["reg_date"]=date("Y-m-d");
        $reg_data["reg_time"]=date("H:i");

        //Проверка наличия существующего аккаунта администратора
        $account_check_array=$this->checkExistingAccount($reg_data,true);
        if(isset($account_check_array['id']))
        {
            return(false);
        }

        //Внесение данных нового аккаунта в базу
        $response=$this->regNewUserRequest($reg_data);
        if(!$response)
        {
            return(false);
        }

        return($reg_data);
    }
}

?>