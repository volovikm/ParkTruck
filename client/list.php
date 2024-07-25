<?php
/*
    Класс List_ представляет формирование списков для отображения в интерфейсе
    Метод listColorDefinition - разделяет строки списка по цветам для отображения

    Метод parkingPlacesList - формирует список отображения парковочных мест в карточке
    
*/

class List_
{
    //Оформление списков

    public function listColorDefinition($list)
    {
        if(isset($list[0]))
        {
            for($i=0;$i<count($list);$i++)
            {
                if(($i % 2) != 0)
                {
                    $list[$i]='<div class="list_row_white">'.$list[$i];
                }
                else
                {
                    $list[$i]='<div class="list_row_blue">'.$list[$i];
                }
            }
        }
        
        return($list);
    }



    //Списки 
    public function parkingPlacesList($parking_data) 
    {
        
    }
}

?>