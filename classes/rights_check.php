<?php

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");
class Rights extends DataBaseRequests
{
    public function showParkingRights($parking_data,$user_data,$role) //Проверка прав на показ парковки
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

        if($role=="admin")
        {
            $rights=true;
        }

        if($role=="driver" || $role=="unauthorized")
        {

            if(!((boolean) $parking_data['draft']))
            {
                $rights=true;
            }

        }
        if($role=="parking_owner")
        {
            if($user_data===false)
            {return($rights);}

            if( !($parking_data['user_id']!=$user_data['id'] && (boolean) $parking_data['draft']) )
            {
                $rights=true;
            }
        }

        return($rights);
    }

    public function editParkingRights($parking_data,$user_data,$role) //Проверка прав на редактирование/удаление парковки
    {
        //$user_data: array | false

        //Получение данных парковки
        $parking_data=$this->parkingCardDataRequest($parking_data['parking_id']);
        if(!isset($parking_data[0]))
        {return(false);}

        $parking_data=$parking_data[0];

        //Проверки прав
        $rights=false;

        if($user_data===false)
        {return($rights);}

        if($role=="parking_owner" && $parking_data["user_id"]==$user_data["id"])
        {
            $rights=true;
        }

        return($rights);
    }

    public function createNewParkingRights($user_data,$role) //Проверка прав на создание новой парковки
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

        if($user_data===false)
        {return($rights);}

        if($role=="parking_owner")
        {
            $rights=true;
        }

        return($rights);
    }

    public function rentRights($rent_data,$user_data,$role) //Проверка прав на бронирование парковочного места
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

        if($user_data===false || $role== "driver")
        {
            
            //Проверка интервалов бронирования
            $rights=true;

        }

        return($rights);
    }

    public function transportRights($user_data,$role,$action,$user_transport_data,$transport_data=false) //Проверка прав на действия с ТС
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

        if($action=="add")
        {
            if($user_data!==false && $role=="driver" && count($user_transport_data)<=100)
            {
                
                $rights=true;
    
            }
        }
        
        if(($action=="edit" || $action=="delete") && $transport_data!=false)
        {
            if($user_data!==false && $role=="driver" && $transport_data["user_id"]==$user_data["id"])
            {
                
                $rights=true;
    
            }
        }
        
        return($rights);
    }

    public function cancelRentRights($user_data,$rent_data,$parking_data) //Проверка прав на отмену бронирования
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

        if($parking_data["user_id"]==$user_data["id"])
        {
            $rights=true;
        }

        if($rent_data["user_id"]==$user_data["id"])
        {
            $rights=true;
        }

        if($user_data["role"]=="admin")
        {
            $rights=true;
        }

        return($rights);
    }

    public function adminRights($user_data,$role)//Проверка прав администратора
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

        if($role=="admin")
        {
            $rights=true;
        }

        return($rights);
    }
}
?>