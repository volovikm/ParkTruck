<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");

class Rent extends DataBaseRequests
{
    public function getRentIntervals($parking_place_id,$timestamp_start=false,$timestamp_end=false) //Метод определения интервалов бронирования, возвращает массив с интервалами
    {
        $intervals=[];
        $intervals["start"]=[];
        $intervals["end"]=[];

        $rent_data=$this->getActiveRentData($parking_place_id);

        for($i=0;$i<count($rent_data);$i++)
        {
            $datetime_start=$rent_data[$i]["rent_start_date"]." ".$rent_data[$i]["rent_start_time"];
            $datetime_end=$rent_data[$i]["rent_end_date"]." ".$rent_data[$i]["rent_end_time"];

            if((strtotime($datetime_start)>=$timestamp_start || $timestamp_start===false) && (strtotime($datetime_end)<=$timestamp_end || $timestamp_end===false))
            {
                array_push($intervals["start"],$datetime_start);
                array_push($intervals["end"],$datetime_end);
            }
        }

        return($intervals);
    }

    public function checkRentStatus($rent_data) //Метод определения текущего статуса бронирования места, true | false
    {
        $rent=false;

        $today_timestamp=strtotime(date("Y-m-d H:i"));
        $intervals=$this->getRentIntervals($rent_data["parking_place_id"]);

        for($i=0;$i<count($intervals["start"]);$i++)
        {
            $start_timestamp=strtotime($intervals["start"][$i]);
            $end_timestamp=strtotime($intervals["end"][$i]);

            if($today_timestamp<=$end_timestamp && $today_timestamp>=$start_timestamp)
            {
                $rent=true;
            }
        }

        return($rent);
    }

    public function checkRentInterval($rent_data) //Метод определения возможности забронировать место на данный период, true | false
    {
        $rent_allowed=true;

        $rent_start_timestamp=strtotime($rent_data["date_start"]." ".$rent_data["time_start"]); //Начало текущего бронирования
        $rent_end_timestamp=strtotime($rent_data["date_end"]." ".$rent_data["time_end"]); //Конец текущего бронирования

        $intervals=$this->getRentIntervals($rent_data["parking_place_id"]);

        for($i=0;$i<count($intervals["start"]);$i++)
        {
            $start_timestamp=strtotime($intervals["start"][$i]); //Начало существующего интервала бронирования
            $end_timestamp=strtotime($intervals["end"][$i]); //Конец существующего интервала бронирования

            //Новый интервал начинается до существующего и заканчивается во время существующего
            if($rent_start_timestamp<$start_timestamp && $rent_end_timestamp<$end_timestamp)
            {$rent_allowed=false;}

            //Новый интервал начинается до существующего и заканчивается после окончания существующего
            if($rent_start_timestamp<$start_timestamp && $rent_end_timestamp>=$end_timestamp)
            {$rent_allowed=false;}

            //Новый интервал начинается во время существующего и заканчивается после окончания существующего
            if($rent_start_timestamp>=$start_timestamp && $rent_start_timestamp<$end_timestamp && $rent_end_timestamp>=$end_timestamp)
            {$rent_allowed=false;}

            //Новый интервал начинается во время существующего и заканчивается во время существующего
            if($rent_start_timestamp>=$start_timestamp && $rent_start_timestamp<=$end_timestamp && $rent_end_timestamp<=$end_timestamp && $rent_end_timestamp>=$start_timestamp)
            {$rent_allowed=false;}

            //Новый интервал равен существующему
            if($rent_start_timestamp==$start_timestamp && $rent_end_timestamp==$end_timestamp)
            {$rent_allowed=false;}

        }

        return($rent_allowed);
    }
}

?>