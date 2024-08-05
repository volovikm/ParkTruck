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
                <option value="C">Грузовой</option>
                <option value="CE">Грузовой с прицепом</option>
                <option value="C1">Малый грузовой</option>
                <option value="B">Легковой</option>
            </select>
        ';
        
        return($input);
    }

    //Поле ввода длины парковочного места 
    public function lengthInput($value="")
    {
        $input='
            <input id="length_" class="basic_input" type="number" placeholder="Длина, м" value="'.$value.'">
        ';

        return($input);
    }

    //Поле ввода ширины парковочного места 
    public function widthInput($value="")
    {
        $input='
            <input id="width" class="basic_input" type="number" placeholder="Ширина, м" value="'.$value.'">
        ';

        return($input);
    }

    //Поле ввода высоты парковочного места 
    public function heightInput($value="")
    {
        $input='
            <input id="height" class="basic_input" type="number" placeholder="Высота, м" value="'.$value.'">
        ';

        return($input);
    }

    //Поле ввода стоимости парковочного места 
    public function priceInput($value="")
    {
        $input='
            <div class="label_div">
                <label class="input_label">Стоимость, руб</label>
            </div>
            <input id="price" class="basic_input" type="number" placeholder="" value="'.$value.'">
        ';

        return($input);
    }

    //Поле выбора единиц стоимости парковочного места 
    public function priceUnitsSelect()
    {
        $input='
            <div class="label_div">
                <label class="input_label">За</label>
            </div>
            <select id="price_units" name="price_units" class="basic_input">
                <option value="hours">Час</option>
                <option value="days">Сутки</option>
            </select>
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

    //Поле ввода госномера
    public function transportNumberInput($value="")
    {
        $input='
            <div class="label_div">
                <label class="input_label">Госномер ТС</label>
            </div>
            <input id="transport_number" class="basic_input" type="number" value="'.$value.'">
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
}

?>