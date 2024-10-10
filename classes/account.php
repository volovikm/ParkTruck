<?php
/*
    Класс Account представляет действия с аккаунтом пользователя
    Метод authUser - авторизация пользователя после предварительной проверки данных
    Метод regConfirmUser - подтверждение регистрации пользователя
    Метод logoutUser - выход из аккаунта
    Метод complexUserCheck - сбор проверок пользователя
    Метод getRole - поучение роли пользователя, без авторизации возвращает unauthorized
    Метод roleToText - получение текста роли
    Метод allowActionByRole - определение прав на действия по роли
    Метод allowActionByUser - определение прав на действия по id пользователя

    Метод checkAuth - проверка наличия авторизации пользователя по сессии, возвращает данные пользователя или false
    Метод checkRequiredRegConfirm - проверка необходимости подтверждения номера телефона, при отсутствии необходимости направляет на index
    Метод checkRegAuthAllowed - проверка возможности пройти авторизацию/регистрацию (невозможно если аккаунт уже авторизован)
    Метод checkRegConfirmCodeSMSTimeout - проверка наличия таймаута отправки СМС с кодом подтверждения регистрации
*/

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/session.php");

class Account extends Session
{
    public $allowed_roles;

    //Методы действий
    public function authUser($user_data) 
    {
        $this->sessionStart();

        $_SESSION = [];
        $_SESSION['auth']=true; 
        $_SESSION['user_id']=$user_data['id']; 
    }

    public function regConfirmUser($user_data)
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");
        $db_request = new DataBaseRequests();

        $user_id=$user_data['id'];
        $db_request->confirmRegUser($user_id);
    }

    public function logoutUser()
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");

        $this->sessionStart();
        $_SESSION = [];

        $redirect = new Redirect();
        $page= "ParkTruck/index.php";
        $redirect->redirectTo($page);
    }

    public function complexUserCheck($check_role=false)
    {
        //Проверка авторизации
        $user_data=$this->checkAuth();
        if($user_data==false){
            return($user_data);
        }

        //Проверка необходимости подтвердить номер телефона
        if($user_data['reg_confirmed']=='0')
        {
            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
            $redirect = new Redirect();
            $page= "ParkTruck/client-new/account/reg_confirm.php";
            $redirect->redirectTo($page);
        }

        //Проверка роли
        if($check_role)
        {

        }

        return($user_data);
    }

    public function getRole($user_data)
    {
        $role="unauthorized";
        if(isset($user_data['role']))
        {
            $role=$user_data['role'];
        }

        return($role);
    }

    public function roleToText($role)
    {
        $role_text=false;

        if($role=="driver")
        {
            $role_text="Водитель";
        }
        if($role=="parking_owner")
        {
            $role_text="Владелец парковки";
        }
        if($role=="admin")
        {
            $role_text="Администратор";
        }

        return($role_text);
    }

    public function allowActionByRole($user_data,$allowed_roles)
    {
        $allowed=false;

        if($user_data===false && !in_array("unauthorized",$allowed_roles))
        {return($allowed);}

        $user_role=$this->getRole($user_data);

        foreach($allowed_roles as $allowed_role)
        {
            if($allowed_role==$user_role)
            {
                $allowed=true;
            }
        }

        return($allowed);
    }

    public function allowActionByUser($user_data,$allowed_user_id)
    {
        $allowed=false;

        if($user_data===false)
        {return($allowed);}

        $user_id=$user_data['id'];

        foreach($allowed_user_id as $allowed_id)
        {
            if($allowed_id==$user_id)
            {
                $allowed=true;
            }
        }

        return($allowed);
    }

    //Методы проверок
    public function checkAuth()
    {
        $this->sessionStart();

        if(isset($_SESSION['auth']) && $_SESSION['auth']===true)
        {
            $user_data=$this->findUserById($_SESSION['user_id']);
            if(count($user_data)!=0)
            {
                return($user_data);
            }
        }

        return(false);
    }

    public function checkRequiredRegConfirm()
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$this->checkAuth();
        if($user_data===false || $user_data['reg_confirmed']=='1')
        {
            $page= "ParkTruck/index.php";
            $redirect->redirectTo($page);
        }
    }

    public function checkRegAuthAllowed()
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$this->checkAuth();
        if($user_data!==false)
        {
            $page= "ParkTruck/index.php";
            $redirect->redirectTo($page);
        }
    }

    public function checkRegConfirmCodeSMSTimeout()
    {
        $user_data=$this->checkAuth();

        $timeout=false;
        $timeout_datetime=$user_data['reg_confirm_code_sms_time'];
        $timeout_datetime = strtotime($timeout_datetime); 
        $now_datetime = strtotime(date("Y-m-d H:i:s")); 

        if($timeout_datetime!=NULL && ($now_datetime-$timeout_datetime)<59)
        {
            $timeout=true;
        }

        return($timeout);
    }
}

?>