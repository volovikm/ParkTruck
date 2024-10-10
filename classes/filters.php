<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rent.php");

class Filters extends Rent
{
    public function filtersHandler($filters_arr,$user_data,$role,$parking_row,$parking_places_arr) //Метод обработки списка фильтров 
    {
        $filters_match=true;

        if($filters_arr==null)
        {return($filters_match);}

        $date_start_index="";
        $time_start_index="";
        $date_end_index="";
        $time_end_index="";

        $sizes_json=file_get_contents("documents/transport_sizes.json");
        $sizes_arr=json_decode($sizes_json,true);

        $properties_json=file_get_contents("documents/parking_properties.json");
        $properties_arr=json_decode($properties_json,true);

        $i=0;
        while($i<count($filters_arr))
        {
            $filter=$filters_arr[$i][0];
            $filter_value=$filters_arr[$i][1];

            //Парковки только данного пользователя
            if($filter=="only_user") 
            {
                if($parking_row["user_id"]!=$user_data["id"])
                {$filters_match=false;}
            }

            //По конкретному ТС
            if($filter=="transport" && $filter_value!="" && $role=="driver")
            {
                $transport_id=$filter_value;
                $transport_data=$this->getTransportDataByIdRequest($user_data,$transport_id)[0];

                //Размер ТС
                array_push($filters_arr,[$transport_data["transport_size"],"on"]);

                //Свойства ТС
                $transport_properties_array=explode(" ",$transport_data["properties"]);
                foreach($transport_properties_array as $property)
                {
                    if($property==""){continue;}

                    array_push($filters_arr,[$property,"on"]);
                }
            }

            //Размеры ТС
            foreach($sizes_arr as $size=>$value)
            {
                if($filter==$size) //Определяем является ли фильтр размером
                {
                    $size_found=false;
                    foreach($parking_places_arr as $parking_place) //Ищем данный размер в парковочных местах
                    {
                        if($parking_place["size"]==$size) //Указанный в фильтре размер найден в данной парковке
                        { 
                            $size_found=true;
                            break;
                        }
                    }

                    if(!$size_found)
                    {$filters_match=false;}
                }
            }

            //Особенности парковки
            foreach($properties_arr as $property=>$value)
            {
                if($filter==$property)
                {
                    $property_found=false;

                    if(str_contains($parking_row["properties"], $filter))
                    {$property_found=true;}
                    
                    if(!$property_found)
                    {$filters_match=false;}
                }
            }

            //Свободные в данный момент
            if($filter=="only_free") 
            {
                $is_free=false;
                foreach($parking_places_arr as $parking_place) 
                {
                    if($parking_place["status"]=="free") 
                    { 
                        $is_free=true;
                        break;
                    }
                }
                
                if(!$is_free)
                {$filters_match=false;}
            }

            //Фильтр по времени
            if($filter=="date_start"){$date_start_index=$i;}
            if($filter=="time_start"){$time_start_index=$i;}
            if($filter=="date_end"){$date_end_index=$i;}
            if($filter=="time_end"){$time_end_index=$i;}

            $i++;
        }

        //Проверка интервала времени
        if($date_start_index!="" && $time_start_index!="" && $date_end_index!="" && $time_end_index!="")
        {
            $interval_found=false;
            foreach($parking_places_arr as $parking_place) 
            {
                $rent_data=[];
                $rent_data["date_start"]=$filters_arr[$date_start_index][1];
                $rent_data["time_start"]=$filters_arr[$time_start_index][1];
                $rent_data["date_end"]=$filters_arr[$date_end_index][1];
                $rent_data["time_end"]=$filters_arr[$time_end_index][1];
                $rent_data["parking_place_id"]=$parking_place["id"];

                $rent_interval=$this->checkRentInterval($rent_data);

                if($rent_interval)
                {$interval_found=true;}
            }

            if(!$interval_found)
            {$filters_match=false;}
        }

        return($filters_match);
    }

    public function parseFilters($filters_string)//Метод преобразования строки фильтров в массив
    {
        $filters_arr=[];
        
        $filters_arr=json_decode($filters_string,true);

        return($filters_arr);
    }
}

?>