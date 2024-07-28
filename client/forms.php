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

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $action=$form_data['action'];
 
        //Элементы интерфейса
        $name_display='
            <span class="info_note_header_value">Название парковки: </span> 
            <span class="info_note_value">'.$form_data['name'].' </span> 
        ';
        $adress_input="";
        $coordinates_display='';
        $buttons='';

        $parking_place_form="";

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
            <button onclick="parkingCardFormHandler(`'.$action.'`)" class="main_button sidebar_button" type="button">Сохранить парковку</button>
        </div>
        ';

        //Кнопка сохранить как черновик
        $save_parking_card_as_draft_button=' 
        <div class="sidebar_button_div">
            <button onclick="parkingCardFormHandler(`'.$action.'`,true)" class="secondary_button sidebar_button" type="button">Сохранить как черновик</button>
        </div>
        ';

        //Кнопка сохранить черновик 
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
            <button onclick="copyParkingPlaceButtonHandler()" class="disabled_button sidebar_button" type="button">Скопировать парковочное место</button>
        </div>
        ';

        //Кнопка удалить парковочное место
        $delete_parking_place_button='
        <div class="sidebar_button_div">
            <button onclick="deleteParkingPlaceButtonHandler()" class="disabled_button sidebar_button" type="button">Удалить парковочное место</button>
        </div>
        ';

        //Кнопка изменить парковочное место
        $change_parking_place_button='
        <div class="sidebar_button_div">
            <button onclick="changeParkingPlaceButtonHandler()" class="disabled_button sidebar_button" type="button">Изменить парковочное место</button>
        </div>
        ';

        //Кнопка забронировать парковочное место
        $rent_parking_place_button='
        <div class="sidebar_button_div">
            <button onclick="rentParkingPlaceButtonHandler()" class="main_button sidebar_button" type="button">Забронировать</button>
        </div>
        ';



        //Кнопки общих действий

        //Кнопка выйти
        $exit_button='
        <div class="sidebar_button_div">
            <button id="cancel_button" onclick="cancelButtonHandler()" class="secondary_button sidebar_button" type="button">На главную</button>
        </div>
        ';



        //Разделение интерфейса в зависимости от действий и ролей
        if($role=="unauthorized" || $role=="driver")
        {
            $buttons=$buttons.$rent_parking_place_button; //Кнопка забронировать (зависима от выбора места - возможен множественный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
        }

        if($role=="parking_owner" && $action=="watch")
        {
            if($draft){$buttons=$buttons.$save_draft_parking_card_button;} //Кнопка сохранить черновик
            $buttons=$buttons.$edit_parking_card_button; //Кнопка редактировать карточку парковки
            $buttons=$buttons.$change_parking_place_button; //Кнопка действий с парковочным местом (указать занятым, освободить, запретить занимать) (зависима от выбора места - только одиночный выбор)
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
            <div class="info_note_header_value">Координаты: </div> 
            <div>'.$latitude_input.' </div> 
            <div>'.$longitude_input.' </div> 
            ';

            //Форма ввода данных парковочного места
            $form_data["action"]="create_new";
            $parking_place_form=$this->parkingPlacesForm($form_data);

            $buttons=$buttons.$save_parking_card_button; //Кнопка сохранить
            $buttons=$buttons.$save_parking_card_as_draft_button; //Кнопка сохранить как черновик
            $buttons=$buttons.$add_parking_place_button; //Кнопка добавить парковочное место
            $buttons=$buttons.$copy_parking_place_button; //Кнопка скопировать существующее парковочное место N раз (зависима от выбора места - только одиночный выбор)
            $buttons=$buttons.$delete_parking_place_button; //Кнопка удалить парковочное место (зависима от выбора места - возможен множественный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
        } 

        if($role=="parking_owner" && $action=="edit")
        {
            //Форма ввода данных парковочного места
            $parking_place_form=$this->parkingPlacesForm($form_data);

            $buttons=$buttons.$save_parking_card_button; //Кнопка сохранить
            $buttons=$buttons.$add_parking_place_button; //Кнопка добавить парковочное место
            $buttons=$buttons.$cancel_button;//Кнопка отменить
            //Кнопка скопировать существующее парковочное место N раз (зависима от выбора места - только одиночный выбор)
            //Кнопка редактировать парковочное место (зависима от выбора места)
            //Кнопка удалить парковочное место (зависима от выбора места - возможен множественный выбор)
            $buttons=$buttons.$exit_button; //Кнопка выйти
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

                    <div id="list_row_pattern_1" class="list_row_1 list_row list_row_pattern"></div>
                    <div id="list_row_pattern_2" class="list_row_2 list_row list_row_pattern"></div>
                    <input id="choice_checkbox_pattern" class="choice_checkbox choice_checkbox_pattern" type="checkbox">
                    <input id="choice_input" class="choice_input">
                    
                    <script src="scripts/list.js"></script>
                    <script>listRequest("parking_places","'.$form_data['parking_id'].'");</script>
                </div>

                '.$parking_place_form.'
                
            </div>

            <script src="scripts/forms.js"></script>
            <script src="scripts/parking_card.js"></script>
            
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
        $parking_place_lenght_input="";
        $parking_place_width_input="";
        $parking_place_height_input="";
        $parking_place_price_input="";
        $parking_place_price_units_select="";
        $parking_place_height_checkbox="";
        $parking_place_size_select="";

        //Поле ввода типового размера парковочного места
        $parking_place_size_select=$this->sizeSelect();

        //Поле ввода длины парковочного места
        $parking_place_lenght_input=$this->lengthInput();

        //Поле ввода ширины парковочного места
        $parking_place_width_input=$this->widthInput();

        //Поле ввода высоты парковочного места
        $parking_place_height_input=$this->heightInput();

        //Чекбокс отсутствия ограничения высоты парковочного места
        $parking_place_height_checkbox=$this->checkBox("height_not_limited",true);

        //Поле ввода стоимости парковочного места
        $parking_place_price_input=$this->priceInput();

        //Поле выбора единиц измерения стоимости парковочного места
        $parking_place_price_units_select=$this->priceUnitsSelect();

        $form='
        <div id="parking_place_form" class="base_form interface_block parking_place_form_div">
            <div class="form_header">Парковочное место</div>

            <div class="input_form_div">
                <div>
                '.$parking_place_size_select.'
                </div>

                <div class="label_div">
                    <label class="input_label">Или</label>
                </div>

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

                <div class="price_div">
                '.$parking_place_price_input.'
                </div>

                <div class="price_units_div">
                '.$parking_place_price_units_select.'
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

        return($form);
    }


}

?>