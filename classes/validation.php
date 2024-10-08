<?php

class Validation 
{
    public function validateTelephone($telephone) 
    {
        $telephone=trim($telephone);
        $firstChar = substr($telephone, 0, 1);
        $secondChar = substr($telephone, 1, 1);

        //Проверка первого символа
        if($firstChar !='7' && $firstChar !='8' && $firstChar !='+')
        {
            //echo("invalid first char");
            return(false);
        }
        if($firstChar =='+' && $secondChar != '7')
        {
            //echo("invalid second char");
            return(false);
        }

        //Проверка длины строки
        $telephone=str_replace("+","",$telephone);
        if(strlen($telephone)!=11)
        {
            //echo("invalid length");
            return(false);
        }

        //Проверка вхождений инородных символов
        if (preg_match("/[^0-9]/",$telephone)) 
        {
            //echo("invalid regexp");
            return(false);
        }

        return(true);
    }

    public function validateRole($role) 
    {
        if($role=="driver" || $role=="parking_owner")
        {
            return(true);
        }

        return(false);
    }

    public function validateCoordinates($coordinate,$type)
    {
        $degree = explode(".", $coordinate);

        //Проверка градусов
        if(!isset($degree[0]))
        {return(false);}

        $degree=(int) $degree[0];

        if($type=="latitude")
        {
            if($degree < -90 || $degree > 90)
            {return(false);}
        }

        if($type=="longitude")
        {
            if($degree < -180 || $degree > 180)
            {return(false);}
        }
        
        return(true);
    }

    public function validateNumbers($number)
    {
        if($number=="" || is_numeric($number))
        {
            return(true);
        }
        return(false);
    }

    public function validateDate($date)
    {
        return(is_numeric(strtotime($date)));
    }

    public function validateTime($time)
    {
        $time_arr=explode(":",$time);

        if($time_arr[0]<=23 && $time_arr[0]>=0 && $time_arr[1]>=0 && $time_arr[1]<=59)
        {
            return(true);
        }
        return(false);
    }

    public function validateDateTimeMatch($datetime_start,$datetime_end)
    {
        if(!is_numeric(strtotime($datetime_start)) || !is_numeric(strtotime($datetime_end)))
        {return(false);}

        if(strtotime($datetime_end)>strtotime($datetime_start))
        {
            return(true);
        }
        return(false);
    }

    //Функции валидации данных парковочных мест
    public function validateParkingPlace($parking_place,$parking_places_array)
    {
        $valid=false;

        //Проверка размера
        $valid_size=$this->validateSize($parking_place["size"]);

        //Проверка стоимости
        $valid_price_days=$this->validateNumbers($parking_place["price_days"]);
        $valid_price_hours=$this->validateNumbers($parking_place["price_hours"]);

        if($valid_size && $valid_price_days && $valid_price_hours)
        {
            $valid=true;
        }

        return($valid);
    }

    public function validateSize($size)
    {
        if($size == "light_cargo" ||
        $size == "medium_cargo" ||
        $size == "light_vehicle" ||
        $size == "euro_truck" ||
        $size == "hood_truck" ||
        $size == "trailer_truck")
        {
            return(true);
        }
        return(false);
    }

    public function validatePriceUnits($price_units)
    {
        if($price_units == "hours" ||
        $price_units == "days")
        {
            return(true);
        }
        return(false);
    }

    //Функции валидации данных бронирования
    public function validateRentData($rent_data)
    {
        $valid=false;

        //Проверка госномера
        $valid_transport_number=$this->validateTransportNumber($rent_data["transport_number"]);

        //Проверка id ТС
        $valid_transport_id=$this->validateNumbers($rent_data["transport_id"]);

        //Проверка дат
        $valid_date_start=$this->validateDate($rent_data["date_start"]);
        $valid_date_end=$this->validateDate($rent_data["date_end"]);

        //Проверка времени
        $valid_time_start=$this->validateTime($rent_data["time_start"]);
        $valid_time_end=$this->validateTime($rent_data["time_end"]);

        //Проверка соответствия времени начала и времени конца
        $valid_datetime_match=$this->validateDateTimeMatch($rent_data["date_start"]." ".$rent_data["time_start"],$rent_data["date_end"]." ".$rent_data["time_end"]);

        if($valid_transport_number && $valid_transport_id && $valid_date_start && $valid_date_end && $valid_time_start && $valid_time_end && $valid_datetime_match)
        {
            $valid=true;
        }

        return($valid);
    }

    public function validateTransportNumber($transport_number)
    {
        return(true);
    }
}

?>