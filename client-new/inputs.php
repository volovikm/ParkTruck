<?php

class Input 
{
    //Поле ввода телефона
    public function telephoneInput() 
    {
        $input='
            <input id="telephone" type="telephone" class="basic_input account_input" placeholder="Номер телефона">
        ';

        return($input);
    }

    //Поле ввода пароля
    public function passwordInput($repeat) 
    {
        $id="password";
        $placeholder="Пароль";
        if($repeat){
            $id="password_repeat";
            $placeholder="Повтор пароля";
        }

        $input='
            <input id="'.$id.'" type="password" class="basic_input account_input" placeholder="'.$placeholder.'">
            <div class="password-control_div">
                <a class="password-control" onclick="show_hide_password(this,`'.$id.'`);"></a>
            </div>
        ';

        return($input);
    }

    //Поле выбора роли
    public function roleSelect() 
    {
        $input='
            <div class="label_div">
                <label class="input_label">Вы регистрируетесь как:</label>
            </div>
            <select id="role" name="role" class="basic_input account_input">
                <option value="driver">Водитель</option>
                <option value="parking_owner">Владелец парковки</option>
            </select>
        ';

        return($input);
    }

    //Чекбокс
    public function checkBox($name,$checked=false)
    {
        if($checked)
        {$checked="checked";}
        else
        {$checked="";}
        
        $input='
             <input id="'.$name.'" name="'.$name.'" type="checkbox" '.$checked.'>
        ';

        return($input);
    }

    //Поле ввода кода подтверждения регистрации
    public function confirmCodeInput()
    {
        $input='
            <input id="reg_confirm_code" type="number" class="basic_input account_input" placeholder="Код из СМС">
        ';

        return($input);
    }


    //Поле ввода общего названия
    public function nameInput($value="")
    {
        $input='
            <input id="name" class="basic_input" placeholder="Название" value="'.$value.'">
        ';

        return($input);
    }

    //Поле выбора фильтра отображения парковок на карте
    public function filterMapSelect()
    {
        $input='
            <select id="filter" name="filter" class="filter_map_select">
                <option id="filter_all" value="all" >Все парковки</option>
                <option id="filter_only_user" value="only_user">Только мои парковки</option>
            </select>
        ';

        return($input);
    }

    //Скрытое поле ввода
    public function invisibleInput($id="",$value="")
    {
        $input='
            <input id="'.$id.'" class="invisible_input" value="'.$value.'">
        ';

        return($input);
    }

    //Поле ввода даты
    public function dateInput($name,$label,$set_today_date=false,$set_min_today=false)
    {
        $input='
            <div class="label_div">
                <label class="input_label">'.$label.'</label>
            </div>
            <input id="date_'.$name.'" class="date_input" type="date">
        ';

        if($set_today_date)
        {
            $input=$input."<script>setTodayDate('date_".$name."','".$set_min_today."')</script>";
        }

        return($input);
    }



    //Поля формы карточки парковки

    //Поле ввода широты 
    public function latitudeInput($value="")
    {
        $input='
            <input id="latitude" class="basic_input" placeholder="Широта" value="'.$value.'">
        ';

        return($input);
    }

    //Поле ввода долготы
    public function longitudeInput($value="")
    {
        $input='
            <input id="longitude" class="basic_input" placeholder="Долгота" value="'.$value.'">
        ';

        return($input);
    }


    //Поля формы парковочного места

    //Поле ввода типового размера парковочного места
    public function sizeSelect()
    {
        $options="";
        $sizes=file_get_contents("../documents/transport_sizes.json");
        $sizes=json_decode($sizes,true);
        foreach($sizes as $key=>$value)
        {   
            $options=$options.'<option value="'.$key.'">'.$value.'</option>';
        }

        $input='
            <div class="label_div">
                <label class="input_label">Типовой размер</label>
            </div>
            <select id="size" name="size" class="basic_input">

                '.$options.'

            </select>
        ';
        
        return($input);
    }

    //Поле ввода стоимости парковочного места 
    public function priceInput($units,$value="")
    {
        $units_base=$units;
        if($units=="days"){$units="руб/сутки";}
        if($units=="hours"){$units="руб/час";}

        $input='
            <div class="label_div">
                <label class="input_label">Тариф, '.$units.'</label>
            </div>
            <input id="price_'.$units_base.'" class="basic_input" type="number" placeholder="" value="'.$value.'">
        ';

        return($input);
    }

    //Поле ввода внутреннего номера парковочного места
    public function parkingPlaceNameInput($value="")
    {
        $input='
            <div class="label_div">
                <label class="input_label">Внутренний номер/название</label>
            </div>
            <input id="parking_place_name" class="basic_input" max="10" value="'.$value.'">
        ';

        return($input);
    }


    //Поля формы бронирования парковочного места

    //Поле выбора ТС
    public function transportSelect($transport_array)
    {
        $return_arr=[];

        $options="";
        for($i=0;$i<count($transport_array);$i++)
        {
            $return_arr["transport_par"]=true;

            $options=$options.'<option value="'.$transport_array[$i]["id"].'">'.$transport_array[$i]["transport_number"].", ".$transport_array[$i]["transport_name"].'</option>';
        }

        if(count($transport_array)==0 || (count($transport_array)==1 && $transport_array[0]=="empty"))
        {
            $return_arr["transport_par"]=false;

            $return_arr["select"]='
            <div class="label_div">
                <label class="input_label">Транспортное средство</label>
            </div>
            <div class="text_wrap text_to_left margin_bottom_block">
                <a href="../client/transport.php" target="_blank">Добавьте транспортные средства</a> в учётную запись, чтобы выбирать ТС при бронировании. 
            </div>
            ';
            return($return_arr);
        }

        $return_arr["select"]='
            <div class="label_div">
                <label class="input_label">Транспортное средство</label>
            </div>
            <select id="transport" name="transport" class="basic_input">

                '.$options.'
                
            </select>
        ';
        
        return($return_arr);
    }

    //Поле ввода госномера
    public function transportNumberInput($show_placeholder=false,$value="")
    {
        $class_placeholder="invisible_input";
        $class_input="";
        if($show_placeholder)
        {
            $class_placeholder="";
            $class_input="invisible_input";
        }

        $transport_size_select=$this->sizeSelect();

        $input='
            <div id="placeholder_div" class="'.$class_placeholder.'">
                <div onclick="switchVisibility(`show_input_div`,`placeholder_div`);" class="input_label link_button text_to_left margin_bottom_block">Ввести госномер и размер ТС вручную</div>
            </div>

            <div id="show_input_div" class="'.$class_input.'">
                <div class="label_div">
                    <label class="input_label">Госномер ТС</label>
                </div>
                <input id="transport_number" class="basic_input" value="'.$value.'">

                '.$transport_size_select.'
                
            </div>
        ';

        return($input);
    }

    //Поля одновременного ввода даты и времени
    public function dateTimeInput($name,$label,$set_today_date=false,$set_today_time=false)
    {
        $input='
            <div class="label_div">
                <label class="input_label">'.$label.'</label>
            </div>
            <input id="date_'.$name.'" class="datetime_input" type="date"">
            <input id="time_'.$name.'" class="datetime_input" type="time"">
        ';

        return($input);
    }


    //Поля формы добавления ТС

    //Поле ввода названия ТС
    public function transportNameInput($value="")
    {
        $input='
            <div class="label_div">
                <label class="input_label">Название ТС</label>
            </div>
            <input id="transport_name" class="basic_input" value="'.$value.'">
        ';
 
        return($input);
    }

    //Поле ввода опций ТС
    public function transportOptionsBlock()
    {
        $refrigerator_checkbox=$this->checkBox("refrigerator");
        $refrigerator=$refrigerator_checkbox.'<label>Рефрежиратор</label>';

        $oversized_checkbox=$this->checkBox("oversized");
        $oversized=$oversized_checkbox.'<label>Негабарит</label>';

        $electrocar_checkbox=$this->checkBox("electrocar");
        $electrocar=$electrocar_checkbox.'<label>Электромобиль</label>';

        $input='
        <div class="text_to_left">
            <div class="label_div">
                <label class="input_label">Особенности ТС</label>
            </div>

            <div>
                '.$refrigerator.'
            </div>

            <div>
                '.$oversized.'
            </div>

            <div>
                '.$electrocar.'
            </div>
        </div>
        ';
 
        return($input);
    }
}

?>