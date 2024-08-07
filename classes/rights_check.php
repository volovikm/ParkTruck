<?php

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");
class Rights extends DataBaseRequests
{
    public function showParkingRights($parking_data,$user_data,$role,$filter=null) //Проверка прав на показ парковки
    {
        //$user_data: array | false

        //Проверки прав
        $rights=false;

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

            if($filter=="all" || $filter==null)
            {
                
                if( !($parking_data['user_id']!=$user_data['id'] && (boolean) $parking_data['draft']) )
                {
                    $rights=true;
                }

            }
            if($filter=="only_user")
            {
                
                if($parking_data['user_id']==$user_data['id'])
                {
                    $rights=true;
                }

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
}
?>