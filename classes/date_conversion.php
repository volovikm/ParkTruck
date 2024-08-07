<?php

class DateConversion 
{
    public function convertDate($date) 
    {
        $base_date_array=preg_split('/-/', $date); 
        $day=$base_date_array[2];
        $month=$base_date_array[1];
        $year=$base_date_array[0];
        $result_date=$day.".".$month.".".$year;

        return($result_date);
    }
}

?>