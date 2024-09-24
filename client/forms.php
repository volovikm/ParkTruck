<?php
require_once("inputs.php");

class Form extends Input
{
    //Форма авторизации
    public function authForm() 
    {
        $telephone_input=$this->telephoneInput();
        $password_input=$this->passwordInput(false);

        $form='
            <form id="auth_form" class="interface_block base_form">
                <div class="form_header">Вход</div>
                <div class="input_form_div">

                    '.$telephone_input.'
                    '.$password_input.'

                    <div class="error_message_div">
                        <div id="error_message" class="error_message">

                        </div>
                    </div>
                
                    <div class="password_restore_link_div">
                        <a class="link" href="password_restore.php">Восстановить пароль</a>
                    </div>

                    <div class="auth_button_div">
                        <button onclick="authFormHandler()" class="main_button" type="button">Войти</button>
                    </div>

                    <div class="reg_redirect_link_div">
                        <div>
                            <a target="_top" href="../account/reg.php">Зарегистрироваться</a>
                        </div>
                    </div>

                    <div class="support_links_div">
                        <div>
                            <a target="_blank" href="../client/support.php?section=license_agreement">Лицензионное соглашение</a>
                        </div>
                        <div>
                            <a target="_blank" href="../client/support.php?section=license_agreement">Соглашение о персональных данных</a>
                        </div>
                    </div>

                </div>

            </form>
            <script src="../scripts/forms.js"></script>
            <script src="../scripts/auth.js"></script>
        ';

        return($form);
    }

    //Форма регистрации
    public function regForm()
    {
        $telephone_input=$this->telephoneInput();
        $password_input=$this->passwordInput(false);
        $password_repeat_input=$this->passwordInput(true);
        $role_input=$this->roleSelect();
        $license_checkbox=$this->checkBox("license_checkbox");

        $form='
            <form id="reg_form" class="interface_block base_form">
                <div class="form_header">Регистрация</div>
                <div class="input_form_div">

                    '.$role_input.'
                    '.$telephone_input.'
                    '.$password_input.'
                    '.$password_repeat_input.'

                    <div class="error_message_div">
                        <div id="error_message" class="error_message">

                        </div>
                    </div>

                    <div class="reg_button_div">
                        <button onclick="regFormHandler()" class="main_button" type="button">Зарегистрироваться</button>
                    </div>

                    <div class="licence_checkbox_div">
                        '.$license_checkbox.'
                        <label>С правилами <a class="licence_checkbox_link" target="_blank" href="">лицензионного соглашения</a> ознакомлен, <a class="licence_checkbox_link" target="_blank" href="">на передачу, обработку и хранение персональных данных</a> согласен</label>
                    </div>

                    <div class="auth_redirect_link_div">
                        <div>
                            <a target="_top" href="../account/auth.php">Войти если уже есть аккаунт</a>
                        </div>
                    </div>

                </div>

            </form>
            <script src="../scripts/forms.js"></script>
            <script src="../scripts/reg.js"></script>
        ';

        return($form);
    }

    //Форма подтверждения номера телефона
    public function regConfirmForm($telephone)
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        $confirm_code_input=$this->confirmCodeInput();

        $timer_active=$account->checkRegConfirmCodeSMSTimeout();

        $disable_button_script="";
        if($timer_active)
        {
            $disable_button_script='
                let timer=readCookie("timer");
                disableSendCodeButton(send_code_button);
            ';
        }

        $form='
            <form id="reg_confirm_form" class="interface_block base_form">
                <div class="form_header">Подтверждение регистрации</div>
                <div class="form_header_note">На номер телефона '.$telephone.' направлено СМС с кодом, введите данный код ниже.</div>
                <div class="input_form_div">

                    '.$confirm_code_input.'

                    <div class="error_message_div">
                        <div id="error_message" class="error_message">

                        </div>
                    </div>
                
                    <div class="send_code_div">
                        <div class="send_code_button_div">
                            <button id="send_code_button" class="secondary_button" type="button">Отправить код</button>
                        </div>
                    </div>

                    <div class="reg_confirm_button_div">
                        <button onclick="regConfirmFormHandler()" class="main_button" type="button">Подтвердить</button>
                    </div>

                    <div class="logout_link_div">
                        <div>
                            <a target="_top" href="../account/logout.php">Выйти из аккаунта</a>
                        </div>
                    </div>

                </div>

            <script src="../scripts/forms.js"></script>
            <script src="../scripts/reg_confirm.js"></script>

            <script>
                '.$disable_button_script.'
            </script>

            </form>
        ';

        return($form);
    }

    //Форма карточки парковки
    public function parkingCardForm($form_data)
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");
        $sql = new DataBaseRequests();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $action=$form_data['action'];

        //Проверки прав на действия
        $show_rights=$rights->showParkingRights($form_data,$user_data,$role);
        $create_new_rights=$rights->createNewParkingRights($user_data,$role);
        $edit_rights=$rights->editParkingRights($form_data,$user_data,$role);

        if($action=="watch" && !$show_rights ||
        $action=="create_new" && !$create_new_rights ||
        $action=="edit" && !$edit_rights)
        {$redirect->redirectTo($redirect->index);}
 
        //Элементы интерфейса
        $name_display='
            <span class="info_note_header_value">Название парковки: </span> 
            <span class="info_note_value">'.$form_data['name'].' </span> 
        ';
        $adress_input="";
        $coordinates_display='';
        $buttons='';

        //Особенности парковки
        $properties_images="";
        $parking_properties=file_get_contents("../documents/parking_properties.json");
        $parking_properties=json_decode($parking_properties,true);
        $properties_array=explode(" ",$form_data['properties']);
        foreach($properties_array as $key=>$value)
        {   
            if($value!="")
            {
                $properties_images=$properties_images."<img class='properties_image' src='../images/".$value.".svg'>";
            }
        }

        $properties_display='
            <span class="info_note_header_value">Особенности: </span> 
            <span>'.$properties_images.'</span>
        ';
        

        $parking_place_form="";
        $parking_place_rent_form="";
        $parking_place_intervals_form="";

        $button_scripts="";
        $other_scripts="";

        $draft_info=""; //Информация о черновике
        $draft=false;
        if($form_data['draft']=='1')
        {
            $draft=true;
            $draft_info='
            <div class="info_note_div">
                <span class="info_note_header_value">Черновик:</span> 
                <span class="info_note_value">Сохраните парковку чтобы её видели все пользователи</span> 
            </div>
            ';
        }



        //Кнопки действия по парковке

        //Кнопка сохранить
        $save_parking_card_button=' 
        <div class="sidebar_button_div">
            <button onclick="parkingCardFormHandler(`'.$action.'`,false,`'.$form_data['parking_id'].'`)" class="main_button sidebar_button" type="button">Сохранить парковку</button>
        </div>
        ';

        //Кнопка сохранить как черновик 
        $save_parking_card_as_draft_button=' 
        <div class="center_text">
            <span class="link_button" onclick="parkingCardFormHandler(`'.$action.'`,true,`'.$form_data['parking_id'].'`)">Сохранить как черновик</span>
        </div>
        ';

        //Кнопка сохранить черновик как общедоступную парковку
        $save_draft_parking_card_button=' 
        <div class="sidebar_button_div">
            <button onclick="parkingCardFormHandler(`save_draft`,false,`'.$form_data['parking_id'].'`)" class="main_button sidebar_button" type="button">Сохранить парковку</button>
        </div>
        ';

        //Кнопка редактировать парковку
        $edit_parking_card_button='
        <div class="sidebar_button_div">
            <button id="edit_button" class="secondary_button sidebar_button" type="button">Редактировать</button>
        </div>
        ';

        //Кнопка отменить редактирование
        $cancel_button='
        <div class="sidebar_button_div">
            <button id="cancel_edit_button" class="secondary_button sidebar_button" type="button">Отменить</button>
        </div>
        ';

        //Кнопка удалить парковку
        $delete_parking_button='
        <div class="sidebar_button_div">
            <div class="center_text">
                <button id="delete_parking_button"  class="link_button text_negative" type="button">Удалить парковку</button>
            </div>
        </div>';
        $button_scripts=$button_scripts.'<script>deleteParkingButtonHandler("'.$form_data['parking_id'].'");</script>';
        

        //Кнопки действия по парковочному месту

        //Кнопка добавить парковочное место
        $add_parking_place_button='
        <div class="sidebar_button_div">
            <button id="add_parking_place_button" class="main_button sidebar_button" type="button">Добавить парковочное место</button>
        </div>
        ';

        //Кнопка скопировать существующее парковочное место
        $copy_parking_place_button='
        <div class="sidebar_button_div">
            <button id="copy_parking_place_button" class="disabled_button sidebar_button" type="button">Скопировать парковочное место</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`copy_parking_place_button`,`secondary_button`,1)</script>';

        //Кнопка редактировать парковочное место
        $edit_parking_place_button='
        <div class="sidebar_button_div">
            <button id="edit_parking_place_button" class="disabled_button sidebar_button" type="button">Редактировать парковочное место</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`edit_parking_place_button`,`secondary_button`,1)</script>';

        //Кнопка удалить парковочное место
        $delete_parking_place_button='
        <div class="sidebar_button_div">
            <button id="delete_parking_place_button" class="disabled_button sidebar_button" type="button">Удалить парковочное место</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`delete_parking_place_button`,`secondary_button`,Infinity)</script>';

        //Кнопка изменить парковочное место
        $change_parking_place_button='
        <div class="sidebar_button_div">
            <button id="change_parking_place_button" class="disabled_button sidebar_button" type="button">Изменить парковочное место</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`change_parking_place_button`,`secondary_button`,1)</script>';

        //Кнопка забронировать парковочное место
        $rent_parking_place_button='
        <div class="sidebar_button_div">
            <button id="rent_parking_place_button" class="main_button sidebar_button" type="button">Забронировать</button>
        </div>
        ';



        //Кнопки общих действий

        //Кнопка выйти
        $exit_button='
        <div class="sidebar_button_div">
            <button id="cancel_button" class="secondary_button sidebar_button" type="button">На главную</button>
        </div>
        ';



        //Форма визуализации интервалов бронирования
        $parking_place_intervals_form=$this->parkingPlaceIntervalsForm($form_data);



        //Разделение интерфейса в зависимости от действий и ролей
        if($role=="unauthorized" || $role=="driver")
        {
            $form_data['action']="rent";
            $parking_place_rent_form=$this->parkingPlaceRentForm($form_data);

            $buttons=$buttons.$rent_parking_place_button; //Кнопка забронировать (зависима от выбора места - возможен множественный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
        }

        if($role=="parking_owner" && $action=="watch")
        {
            if($draft){$buttons=$buttons.$save_draft_parking_card_button;} //Кнопка сохранить черновик
            if($edit_rights){$buttons=$buttons.$edit_parking_card_button;} //Кнопка редактировать карточку парковки
            //$buttons=$buttons.$change_parking_place_button; //Кнопка действий с парковочным местом (редактировать,указать занятым, освободить, запретить занимать) (зависима от выбора места - только одиночный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
        }

        if($role=="parking_owner" && $action=="create_new")
        {
            //Поле ввода адреса
            $adress_input=$this->invisibleInput("adress");

            //Поле ввода названия парковки
            $name_input=$this->nameInput($form_data['name']);
            $name_display='
            <div class="info_note_header_value">Название парковки: </div> 
            <div>'.$name_input.' </div> 
            ';

            //Поле ввода координат
            $latitude_input=$this->latitudeInput($form_data['latitude']);
            $longitude_input=$this->longitudeInput($form_data['longitude']);
            $coordinates_display='
            <div class="invisible_input">
            <div>'.$latitude_input.' </div> 
            <div>'.$longitude_input.' </div> 
            </div> 
            ';

            //Поле ввода особенностей
            $properties_display='<span class="info_note_header_value">Особенности: </span> ';
            foreach($parking_properties as $key => $value)
            {
                $checkbox=$this->checkBox($key);
                $properties_display=$properties_display.'
                <div>
                '.$checkbox.'<label>'.$value.'</label>
                </div>';
            }

            //Форма ввода данных парковочного места
            $form_data["action"]="create_new";
            $parking_place_form=$this->parkingPlacesForm($form_data);

            $buttons=$buttons.$save_parking_card_button; //Кнопка сохранить
            $buttons=$buttons.$save_parking_card_as_draft_button; //Кнопка сохранить как черновик
            $buttons=$buttons.$add_parking_place_button; //Кнопка добавить парковочное место
            $buttons=$buttons.$copy_parking_place_button; //Кнопка скопировать существующее парковочное место N раз (зависима от выбора места - только одиночный выбор)
            $buttons=$buttons.$edit_parking_place_button; //Кнопка редактирования парковочного места (зависима от выбора места - только одиночный выбор)
            $buttons=$buttons.$delete_parking_place_button; //Кнопка удалить парковочное место (зависима от выбора места - возможен множественный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
        } 

        if($role=="parking_owner" && $action=="edit")
        {
            //Поле ввода адреса
            $adress_input=$this->invisibleInput("adress");

            //Поле ввода названия парковки
            $name_input=$this->nameInput($form_data['name']);
            $name_display='
            <div class="info_note_header_value">Название парковки: </div> 
            <div>'.$name_input.' </div> 
            ';

            //Поле ввода особенностей
            $properties_array=explode(" ",$form_data['properties']);
            $properties_display='<span class="info_note_header_value">Особенности: </span> ';
            foreach($parking_properties as $key => $value) //Все особенности из списка
            {
                $checked=false;
                foreach($properties_array as $key1 => $value1) //Особенности данного места из базы
                {
                    if($value1==$key)
                    {
                        $checked=true;
                    }
                }

                $checkbox=$this->checkBox($key,$checked);
                $properties_display=$properties_display.'
                <div>
                '.$checkbox.'<label>'.$value.'</label>
                </div>';
            }

            //Форма ввода данных парковочного места
            $parking_place_form=$this->parkingPlacesForm($form_data);

            $buttons=$buttons.$save_parking_card_button; //Кнопка сохранить
            $buttons=$buttons.$add_parking_place_button; //Кнопка добавить парковочное место
            $buttons=$buttons.$cancel_button;//Кнопка отменить
            $buttons=$buttons.$copy_parking_place_button; //Кнопка скопировать существующее парковочное место N раз (зависима от выбора места - только одиночный выбор)
            $buttons=$buttons.$edit_parking_place_button; //Кнопка редактирования парковочного места (зависима от выбора места - только одиночный выбор)
            $buttons=$buttons.$delete_parking_place_button; //Кнопка удалить парковочное место (зависима от выбора места - возможен множественный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
            $buttons=$buttons.$delete_parking_button; //Кнопка удалить карточку
        }



        //Скрипт сброса серверных данных в localstorage
        if($action=="create_new" || true)
        {
            $other_scripts=$other_scripts."<script>dropParkingPlacesData();</script>";
        }



        $form='

        <form id="parking_card_form" class="parking_card_form">

            <div class="sidebar">

                <div class="info_block">

                    <div class="info_note_main_header_div">
                        Общая информация
                    </div>

                    '.$draft_info.'

                    <div class="info_note_div">
                        <span class="info_note_header_value">Адрес: </span> 
                        <span id="adress_line" class="info_note_value">'.$form_data['adress'].' </span> 
                    </div>

                    <div class="info_note_div">
                        '.$coordinates_display.'
                        '.$adress_input.'
                    </div> 

                    <div class="info_note_div">
                        '.$name_display.'
                    </div>  

                    <div class="info_note_div">
                        '.$properties_display.'
                    </div>  

                </div>

                <div class="error_message_div">
                    <div id="error_message" class="error_message">

                    </div>
                </div>

                <div class="buttons_block">
                    '.$buttons.'
                </div>

            </div>

            <div class="main_space">

                <div class="info_note_main_header_div">
                    Парковочные места
                </div>

                <div class="list_filter_div">

                </div>

                <div id="list_container" class="list">

                    <div id="list_rows" class="list_rows">
                    </div>

                    <div id="list_content" class="list_content">
                    </div>

                    <div class="list_row_1 list_row"></div>
                    <div id="list_row_pattern_1" class="list_row_1 list_row list_row_pattern"></div>
                    <div id="list_row_pattern_2" class="list_row_2 list_row list_row_pattern"></div>
                    <input id="choice_checkbox_pattern" class="choice_checkbox choice_checkbox_pattern" type="checkbox">
                    <input id="choice_input" class="choice_input">
                    
                </div>

                '.$parking_place_form.'
                '.$parking_place_rent_form.'
                '.$parking_place_intervals_form.'
                
            </div>\

            <script src="scripts/list.js"></script>
            <script>listRequest("parking_places","'.$form_data['parking_id'].'");</script>

            <script src="scripts/forms.js"></script>

            <script src="scripts/parking_card.js"></script>
            
            '.$button_scripts.'
            '.$other_scripts.'

            <script>editButtonHandler("'.$form_data['parking_id'].'");</script>
            <script>cancelEditButtonHandler("'.$form_data['parking_id'].'");</script>
            <script>setAdressFromCookie("'.$action.'");</script>

        </form>
        ';

        return($form);
    }

    //Форма парковочного места
    public function parkingPlacesForm($form_data)
    {
        $action=$form_data["action"];
        $parking_place_name_input="";
        $parking_place_price_days_input="";
        $parking_place_price_hours_input="";
        $parking_place_size_select="";

        //Поле ввода внутреннего номера парковочного места
        $parking_place_name_input=$this->parkingPlaceNameInput();

        //Поле ввода типового размера парковочного места
        $parking_place_size_select=$this->sizeSelect();

        //Поле ввода стоимости (в днях) парковочного места
        $parking_place_price_days_input=$this->priceInput("days");

        //Поле ввода стоимости (в часах) парковочного места
        $parking_place_price_hours_input=$this->priceInput("hours");

        $form='
        <div id="parking_place_form" class="base_form interface_block parking_place_form_div">
            <div class="form_header">Парковочное место</div>

            <div class="input_form_div">

                <div>
                '.$parking_place_name_input.'
                </div>

                <div>
                '.$parking_place_size_select.'
                </div>

                <div class="price_div">
                '.$parking_place_price_days_input.'
                </div>

                <div class="price_div">
                '.$parking_place_price_hours_input.'
                </div>

            </div>

            <div class="error_message_div">
                <div id="error_message_parking_place" class="error_message">

                </div>
            </div>

            <div class="button_div">
                <button id="save_parking_place_button" onclick="parkingPlaceFormHandler(`'.$action.'`)" class="main_button" type="button">Сохранить</button>
                <button id="cancel_parking_place_button" class="secondary_button" type="button">Отменить</button>
            </div>

        </div>
        ';

        /*
        <div>
                '.$parking_place_lenght_input.'
                </div>
                <div>
                '.$parking_place_width_input.'
                </div>
                <div>
                '.$parking_place_height_input.'
                </div>

                <div class="height_checkbox_div">
                '.$parking_place_height_checkbox.'
                <label>Высота не ограничена</label>
                </div>
        */

        return($form);
    }

    //Форма бронирования парковочного места
    public function parkingPlaceRentForm($form_data)
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");
        $sql = new DataBaseRequests();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        //Поле ввода госномера
        $transport_number_input=$this->transportNumberInput();

        //Поле выбора ТС
        $transport_select="";
        if($role=="driver")
        {
            $transport_array=$sql->getUserTransportDataRequest($user_data);
            $transport_select_arr=$this->transportSelect($transport_array);

            $transport_select=$transport_select_arr["select"];
            if($transport_select_arr["transport_par"]) //В списке есть хотя бы одно ТС
            {
                $transport_number_input=$this->transportNumberInput(true);
            }
        }

        //Поле ввода даты, времени начала бронирования
        $datetime_start=$this->dateTimeInput("start","Дата, время начала бронирования",true);

        //Поле ввода даты окончания бронирования
        $datetime_end=$this->dateTimeInput("end","Дата, время окончания бронирования",true);

        $form='

        <div id="parking_place_rent_form" class="base_form interface_block parking_place_form_div">
            <div class="form_header">Парковочное место</div>

            <div class="parking_place_rent_info">
                <div>
                    Внутренний номер: <span id="parking_place_name_span"></span>
                </div>
                <div>
                    Стоимость за сутки: <span id="price_days_span"></span> <span> руб</span> 
                </div>
                <div>
                    Стоимость за час: <span id="price_hours_span"></span> <span> руб</span> 
                </div>
            </div>

            <div class="input_form_div">

                <div>
                '.$transport_select.'
                </div>

                <div>
                '.$transport_number_input.'
                </div>

                <div>
                '.$datetime_start.'
                </div>

                <div>
                '.$datetime_end.'
                </div>

                <div class="result_price_div">
                    Итоговая стоимость: <span id="result_price_span"></span>
                    <span class="invisible_input" id="result_price_value"></span>
                </div>
                
            </div>

            <div class="error_message_div">
                <div id="error_message_rent_parking_place" class="error_message">

                </div>
            </div>

            <div class="button_div">
                <button id="save_parking_place_rent_button" class="main_button" type="button">Подтвердить</button>
                <button id="cancel_parking_place_rent_button" class="secondary_button" type="button">Отменить</button>
            </div>

        </div>
        ';

        return($form);
    }

    //Форма визуализации интервалов бронирования парковочного места
    public function parkingPlaceIntervalsForm($form_data)
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        //Проверки прав на действия
        $edit_rights=$rights->editParkingRights($form_data,$user_data,$role);

        //Инфографика отображения интервалов
        $intervals_display="";
        for($i=0;$i<7;$i++)
        {
            $intervals_display=$intervals_display.'
            <div id="intervals_display_row_'.$i.'" class="intervals_display_row">

                <div id="intervals_days_column_'.$i.'" class="intervals_days_column">
                
                </div>

                <div id="intervals_display_column_'.$i.'" class="intervals_display_column">
                        
                </div>
                        
            </div>

            <div id="intervals_time_row_'.$i.'" class="intervals_time_row">

                <div id="timeline_div_'.$i.'" class="timeline_div">

                </div>
                
            </div>
        ';
        }

        //Форма с информацией о конкретном интервале
        $interval_form="";

        //Разделение интерфейса в зависимости от прав
        if($edit_rights)
        {
            $interval_form=$interval_form.'
            <div id="interval_div" class="interval_div">
                <div>
                    Выбранный интервал: <span id="interval_time_span"></span>
                </div>
                <div>
                    Номер бронирования: <span id="interval_rent_number_span"></span>
                </div>
                <div>
                    Номер ТС: <span id="interval_transport_number_span"></span>
                </div>
                <div class="center_text">
                    <button id="stop_rent_button" class="secondary_button" type="button">Отменить бронирование</button>
                </div>
            </div>
            ';
        }

        if(!$edit_rights)
        {
            $interval_form=$interval_form.'
            <div id="interval_div" class="interval_div">
                Выбранный интервал: <span id="interval_time_span"></span>
            </div>
            ';
        }
        

        //Поле ввода даты "от"
        $date_from_input=$this->dateInput("from","Период: 7 дней с ",true);

        $form='
        <div id="parking_place_intervals_form" class="interface_block parking_place_form_div intervals_form_div">
            <div class="form_header">Парковочное место</div>

            <div class="parking_place_intervals_info">
                <div class="parking_place_name_div">
                    Внутренний номер: <span id="parking_place_intervals_name_span"></span>
                </div>
            </div>

            <div class="input_form_div">

            <div>
                '.$date_from_input.'
            </div>

            '.$interval_form.'

            <div class="intervals_info_display_div">

                '.$intervals_display.'
                
            </div>
                
            </div>

            <div class="button_div">
                <button id="cancel_parking_place_intervals_button" class="secondary_button" type="button">Закрыть</button>
            </div>

        </div>
        ';

        return($form);
    }

    //Форма списка ТС
    public function transportForm($form_data)
    {
        $buttons="";

        $button_scripts="";

        //Кнопка добавить ТС
        $add_transport_button='
        <div class="sidebar_button_div">
            <button id="add_transport_button" class="main_button sidebar_button" type="button">Добавить ТС</button>
        </div>
        ';

        //Кнопка редактировать ТС
        $edit_transport_button='
        <div class="sidebar_button_div">
            <button id="edit_button" class="disabled_button sidebar_button" type="button">Редактировать</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`edit_button`,`secondary_button`,1)</script>';

        //Кнопка удалить ТС
        $delete_transport_button='
        <div class="sidebar_button_div">
            <button id="delete_button" class="disabled_button sidebar_button" type="button">Удалить</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`delete_button`,`secondary_button`,Infinity)</script>';

        //Кнопка выйти
        $exit_button='
        <div class="sidebar_button_div">
            <button id="cancel_button" class="secondary_button sidebar_button" type="button">На главную</button>
        </div>
        ';

        $buttons=$buttons.$add_transport_button;
        $buttons=$buttons.$edit_transport_button;
        $buttons=$buttons.$delete_transport_button;
        $buttons=$buttons.$exit_button;

        //Форма отдельного ТС
        $edit_transport_form=$this->editTransportForm($form_data);

        $form='

        <form id="transport_form" class="transport_form">

            <div class="sidebar">

                <div class="error_message_div">
                    <div id="error_message" class="error_message">

                    </div>
                </div>

                <div class="buttons_block">
                    '.$buttons.'
                </div>

            </div>

            <div class="main_space">

                <div class="info_note_main_header_div">
                    Транспортные средства
                </div>

                <div class="list_filter_div">

                </div>

                <div id="list_container" class="list">

                    <div id="list_rows" class="list_rows">
                    </div>

                    <div id="list_content" class="list_content">
                    </div>

                    <div class="list_row_1 list_row"></div>
                    <div id="list_row_pattern_1" class="list_row_1 list_row list_row_pattern"></div>
                    <div id="list_row_pattern_2" class="list_row_2 list_row list_row_pattern"></div>
                    <input id="choice_checkbox_pattern" class="choice_checkbox choice_checkbox_pattern" type="checkbox">
                    <input id="choice_input" class="choice_input">
                    
                </div>

                '.$edit_transport_form.'
                
            </div>\

            <script src="scripts/list.js"></script>
            <script>listRequest("transport","'.$form_data['id'].'");</script>

            '.$button_scripts.'

            <script src="scripts/forms.js"></script>

            <script src="scripts/transport.js"></script>

        </form>';

        return($form);
    }

    //Форма отдельного ТС
    public function editTransportForm($form_data)
    {
        //Поле id ТС
        $transport_id_input=$this->invisibleInput("transport_id"); 
        
        //Поле госномера ТС
        $transport_number_input=$this->transportNumberInput();

        //Поле названия ТС
        $transport_name_input=$this->transportNameInput();

        //Поле ввода размера 
        $transport_size_select=$this->sizeSelect();

        //Поле ввода особенностей ТС
        $transport_options_block=$this->transportOptionsBlock();

        $form='
        <div id="edit_transport_form" class="base_form interface_block edit_transport_form_div">
            <div class="form_header">Транспортное средство</div>

            <div class="input_form_div">

                <div>
                '.$transport_id_input.'
                </div>

                <div>
                '.$transport_number_input.'
                </div>

                <div>
                '.$transport_name_input.'
                </div>

                <div>
                '.$transport_size_select.'
                </div>

                <div>
                '.$transport_options_block.'
                </div>

            </div>

            <div class="error_message_div">
                <div id="error_message_transport" class="error_message">

                </div>
            </div>

            <div class="button_div">
                <button id="save_transport_button" class="main_button" type="button">Сохранить</button>
                <button id="cancel_transport_button" class="secondary_button" type="button">Отменить</button>
            </div>

        </div>
        ';

        return($form);
    }

    //Форма списка бронирований
    public function rentForm($form_data)
    {
        $buttons="";

        $button_scripts="";

        //Кнопка отменить бронирование
        $cancel_rent_button='
        <div class="sidebar_button_div">
            <button id="cancel_rent_button" class="disabled_button sidebar_button" type="button">Отменить бронирование</button>
        </div>
        ';
        $button_scripts=$button_scripts.'<script>enableListButtons(`cancel_rent_button`,`secondary_button`,Infinity)</script>';

        //Кнопка выйти
        $exit_button='
        <div class="sidebar_button_div">
            <button id="cancel_button" class="secondary_button sidebar_button" type="button">На главную</button>
        </div>
        ';

        $buttons=$buttons.$cancel_rent_button;
        $buttons=$buttons.$exit_button;

        $form='

        <form id="rent_form" class="rent_form">

            <div class="sidebar">

                <div class="error_message_div">
                    <div id="error_message" class="error_message">

                    </div>
                </div>

                <div class="buttons_block">
                    '.$buttons.'
                </div>

            </div>

            <div class="main_space">

                <div class="info_note_main_header_div">
                    Бронирования
                </div>

                <div class="list_filter_div">

                </div>

                <div id="list_container" class="list">

                    <div id="list_rows" class="list_rows">
                    </div>

                    <div id="list_content" class="list_content">
                    </div>

                    <div class="list_row_1 list_row"></div>
                    <div id="list_row_pattern_1" class="list_row_1 list_row list_row_pattern"></div>
                    <div id="list_row_pattern_2" class="list_row_2 list_row list_row_pattern"></div>
                    <input id="choice_checkbox_pattern" class="choice_checkbox choice_checkbox_pattern" type="checkbox">
                    <input id="choice_input" class="choice_input">
                    
                </div>
                
            </div>\

            <script src="scripts/list.js"></script>
            <script>listRequest("rent","'.$form_data['id'].'");</script>

            '.$button_scripts.'

            <script src="scripts/forms.js"></script>

            <script src="scripts/rent.js"></script>

        </form>';

        return($form);
    }

    //Форма списка парковок
    public function userParkingsForm($form_data)
    {
        $buttons="";

        $button_scripts="";

        //Кнопка выйти
        $exit_button='
        <div class="sidebar_button_div">
            <button id="cancel_button" class="secondary_button sidebar_button" type="button">На главную</button>
        </div>
        ';

        $buttons=$buttons.$exit_button;

        $form='

        <form id="parkings_form" class="parkings_form">

            <div class="sidebar">

                <div class="error_message_div">
                    <div id="error_message" class="error_message">

                    </div>
                </div>

                <div class="buttons_block">
                    '.$buttons.'
                </div>

            </div>

            <div class="main_space">

                <div class="info_note_main_header_div">
                    Парковки
                </div>

                <div class="list_filter_div">

                </div>

                <div id="list_container" class="list">

                    <div id="list_rows" class="list_rows">
                    </div>

                    <div id="list_content" class="list_content">
                    </div>

                    <div class="list_row_1 list_row"></div>
                    <div id="list_row_pattern_1" class="list_row_1 list_row list_row_pattern"></div>
                    <div id="list_row_pattern_2" class="list_row_2 list_row list_row_pattern"></div>
                    <input id="choice_checkbox_pattern" class="choice_checkbox choice_checkbox_pattern" type="checkbox">
                    <input id="choice_input" class="choice_input">
                    
                </div>
                
            </div>

            <script src="scripts/list.js"></script>
            <script>listRequest("parkings","'.$form_data['id'].'");</script>

            '.$button_scripts.'

            <script src="scripts/forms.js"></script>

            <script src="scripts/rent.js"></script>

        </form>';

        return($form);
    }

}

?>