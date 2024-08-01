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

    //Функции валидации данных парковочных мест
    public function validateParkingPlace($parking_place)
    {
        $valid=false;

        //Проверка размера
        $valid_size=$this->validateSize($parking_place["size"]);

        //Проверка числовых размеров
        $valid_length=$this->validateNumbers($parking_place["length_"]);
        $valid_width=$this->validateNumbers($parking_place["width"]);
        $valid_height=$this->validateNumbers($parking_place["height"]);

        //Проверка стоимости
        $valid_price=$this->validateNumbers($parking_place["price"]);

        //Проверка единиц измерения стоимости
        $valid_price_units=$this->validatePriceUnits($parking_place["price_units"]);

        if($valid_size && $valid_length && $valid_width && $valid_height && $valid_price && $valid_price_units)
        {
            $valid=true;
        }

        return($valid);
    }

    public function validateSize($size)
    {
        if($size == "C" ||
        $size == "CE" ||
        $size == "C1" ||
        $size == "B")
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
}

?>