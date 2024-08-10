<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");

class Rent extends DataBaseRequests
{
    public function getRentIntervals($parking_place_id) //Метод определения интервалов бронирования, возвращает массив с интервалами
    {
        $intervals=[];

        $rent_data=$this->getActiveRentData($parking_place_id);

        for($i=0;$i<count($rent_data);$i++)
        {
            $datetime_start=$rent_data[$i]["rent_start_date"]." ".$rent_data[$i]["rent_start_time"];
            $datetime_end=$rent_data[$i]["rent_end_date"]." ".$rent_data[$i]["rent_end_time"];
        }

        return($intervals);
    }

    public function checkRentStatus($parking_place_id) //Метод определения текущего статуса бронирования места, true | false
    {
        $rent=false;

        return($rent);
    }
}

?>