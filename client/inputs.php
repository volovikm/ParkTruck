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
        $input='
            <div class="label_div">
                <label class="input_label">Типовой размер</label>
            </div>
            <select id="size" name="size" class="basic_input">
                <option value="light_cargo">Грузовой малый</option> 
                <option value="medium_cargo">Грузовой средний</option>
                <option value="light_vehicle">Легковой</option>
                <option value="euro_truck">Еврофура</option>
                <option value="hood_truck">Капотник</option>
                <option value="trailer_truck">Сцепка</option>
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
        $input='
            <div class="label_div">
                <label class="input_label">Транспортное средство</label>
            </div>
            <select id="transport" name="transport" class="basic_input">
                
            </select>
        ';
        
        return($input);
    }

    //Поле ввода госномера
    public function transportNumberInput($value="")
    {
        $input='
            <div class="label_div">
                <label class="input_label">Госномер ТС</label>
            </div>
            <input id="transport_number" class="basic_input" value="'.$value.'">
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