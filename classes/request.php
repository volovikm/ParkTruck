<?php
/*
    Класс Request представляет действия с запросами с клиента
    Метод parseRequest - разбирает содержимое запроса, определяет назначение запроса, запускает соответсвующий метод
    Метод sendResponse - выводит ответ на клиент

    Метод getParkingsData - находит метки парковок в зависимости от запроса пользователя
    Метод auth - принимает данные авторизации, проверяет их, запускает процесс авторизации
    Метод reg - принимает данные регистрации, проверяет их, запускает процесс авторизации после успешной регистрации
    Метод regConfirm - принимает код из смс, проверяет его, запускает подтверждение регистрации
    Метод sendSMSRegConfirmCode - принимает запрос на отправку кода по СМС, запускает отправку СМС
    Метод addNewParkingCard - принимает запрос на добавление новой карточки парковки, проверяет данные, запускает добавление
    Метод editParkingCard - принимает запрос на редактирование существующей карточки парковки, проверяет данные, запускает редактирование
    Метод saveDraftParkingCard - принимает запрос на сохранение черновика, запускает запрос в базу на сохранение черновика
    Метод deleteParkingCard - принимает запрос на удаление существующей парковки, запускает запрос в базу
    Метод startRent - принимает запрос на начало бронирования парковочного места, запускает бронирование

    Метод getListData - принимает запрос на вывод данных списка, возвращает массив списка
*/

require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/data_base/bd.php");

class Request extends DataBaseRequests
{
    public $post;
    public $response_json='{"response":"unknown_request"}';

    public function parseRequest() 
    {
        $request_fields=$this->post;

        //Определение содержимого запроса
        if(!empty($request_fields['request_content']))
        {
            $request_content_json=$request_fields['request_content'];
            $request_content=json_decode($request_content_json,true);

            $response=[]; //Массив значений для вывода

            //Действия по каждому запросу

            //Запрос на вывод меток парковок на карте
            if(isset($request_content['get_parkings_data']))
            {
                $response=$this->getParkingsData($request_content);
                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на авторизацию
            if(isset($request_content['auth']))
            {
                $response=$this->auth($request_content);
                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на регистрацию
            if(isset($request_content['reg']))
            {
                $response=$this->reg($request_content);
                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на подтверждение регистрации
            if(isset($request_content['reg_confirm']))
            {
                $response=$this->regConfirm($request_content);
                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запррос на отправку СМС кода подтверждения регистрации
            if(isset($request_content['send_sms_reg_confirm_code']))
            {
                $response=$this->sendSMSRegConfirmCode($request_content);
                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на действия с карточкой парковки
            if(isset($request_content['parking_card_action']))
            {
                if($request_content['parking_card_action']=="create_new")
                {
                    $response=$this->addNewParkingCard($request_content);
                }

                if($request_content['parking_card_action']=="edit")
                {
                    $response=$this->editParkingCard($request_content);
                }

                if($request_content['parking_card_action']=="save_draft")
                {
                    $response=$this->saveDraftParkingCard($request_content);
                }

                if($request_content['parking_card_action']=="delete")
                {
                    $response=$this->deleteParkingCard($request_content);
                }
                
                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на дейтсвия с бронированием
            if(isset($request_content['rent_action']))
            {
                if($request_content['rent_action']=="rent_start")
                {
                    $response=$this->startRent($request_content);
                }

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на вывод данных списка
            if(isset($request_content['list']))
            {
                $response=$this->getListData($request_content);

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function sendResponse() 
    {
        echo($this->response_json);
    }



    //Методы обработки каждого запроса

    public function getParkingsData($request_content) //Метод вывода всех меток парковок на карту
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/session.php");
        $session = new Session();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        //Получение данных всех парковок из базы
        $data=$this->allParkingsDataRequest();

        $result_data=$data;
        for($i=0;$i<count($data);$i++)
        {
            $show_rights=$rights->showParkingRights($data[$i],$user_data,$role,$request_content['filter']);
            if(!$show_rights)
            {
                unset($result_data[$i]);
            }

            //Добавление id текущего пользователя в массивы парковок
            $result_data[$i]['current_user_id']="unauthorized";
            if($user_data!==false)
            {
                $result_data[$i]['current_user_id']=$user_data['id'];
            }
        }

        $response=$result_data;

        return($response);
    }

    public function auth($request_content) //Метод авторизации пользователя
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        $auth_data=$request_content;
        $telephone=$auth_data["telephone"];
        $password=$auth_data["password"];

        //Проверка существования аккаунта с данным номером телефона
        $user_data=$this->findUserByTelephone($telephone);
        if(empty($user_data['id']))
        {
            $response='{"response":"account_not_exist"}';
            return($response);
        }

        //Проверка пароля
        $password_hash=$user_data["password_hash"];
        if(!password_verify($password, $password_hash))
        {
            $response='{"response":"account_not_exist"}';
            return($response);
        }

        //Успешная авторизация
        $account->authUser($user_data);
        $response='{"response":"auth_complete"}';
        return($response);
    }

    public function reg($request_content) //Метод регистрации пользователя
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/random.php");
        $random=new Random();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/validation.php");
        $validation = new Validation();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/sms_sender.php");
        $SMS = new SMSsender();

        $reg_data=$request_content;
        $reg_data['password_hash']=password_hash($reg_data['password'],PASSWORD_DEFAULT);
        $reg_data['reg_confirm_code']=$random->randomNumberString(6);

        //Валидация полученных данных
        $valid=$validation->validateTelephone($reg_data['telephone']);
        if(!$valid)
        {
            $response='{"response":"invalid_telephone"}';
            return($response);
        }
        $valid=$validation->validateRole($reg_data['role']);
        if(!$valid)
        {
            $response='{"response":"invalid_role"}';
            return($response);
        }

        //Проверка существования аккаунта с данным номером телефона (подтверждённый аккаунт)
        $account_check_array=$this->checkExistingAccount($reg_data,true);
        if(isset($account_check_array['id']))
        {
            $response='{"response":"account_exists"}';
            return($response);
        }

        //Проверка существования аккаунта с данным номером телефона (неподтверждённый аккаунт)
        $account_check_array=$this->checkExistingAccount($reg_data,false);

        if(isset($account_check_array['id']))
        {
            $user_id=$account_check_array['id'];
            $response= $this->deleteExistingAccount($user_id); //Удаление неподтверждённого аккаунта с данной ролью и номером телефона
            if(!$response)
            {
                $response='{"response":"reg_request_error"}';
                return($response);
            }
        }

        //Внесение данных нового аккаунта в базу
        $response=$this->regNewUserRequest($reg_data);
        if(!$response)
        {
            $response='{"response":"reg_request_error"}';
            return($response);
        }

        //Успешная регистрация - проходим авторизацию
        $user_data=$this->findUserByTelephone($reg_data["telephone"]);

        //Отправка кода подтверждения
        $SMS->sendRegConfirmSMS($user_data['telephone'],$user_data['reg_confirm_code']);
        $account->authUser($user_data);
        $response='{"response":"reg_complete"}';

        return($response);
    }

    public function regConfirm($request_content) //Метод подтверждения регистрации кодом из смс
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        $reg_confirm_data=$request_content;
        $reg_confirm_code=$reg_confirm_data["reg_confirm_code"];

        //Проверка кода подтверждения регистрации данного пользователя
        $user_data=$account->checkAuth();
        if($user_data===false)
        {
            $response='{"response":"not_auth"}';
            return($response);
        }
        if($user_data['reg_confirm_code']!=$reg_confirm_code)
        {
            $response='{"response":"wrong_reg_confirm_code"}';
            return($response);
        }

        //Успешное подтверждение регистрации
        $account->regConfirmUser($user_data);
        $response='{"response":"reg_confirm_complete"}';
        return($response);
    }

    public function sendSMSRegConfirmCode($request_content) //Метод отправки кода подтверждения регистрации по СМС
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/sms_sender.php");
        $SMS = new SMSsender();

        $user_data=$account->checkAuth();

        //Определение таймаута
        $timeout=$account->checkRegConfirmCodeSMSTimeout();

        //Проверка таймаута
        if($timeout)
        {
            $response='{"response":"timeout"}';
            return($response);
        }

        //Отправка СМС с кодом
        $this->updateRegConfirmSMSTimeout($user_data['id'],date("Y-m-d H:i:s"));
        $SMS->sendRegConfirmSMS($user_data['telephone'],$user_data['reg_confirm_code']);
        $response='{"response":"sms_sent"}';

        return($response);
    }

    public function addNewParkingCard($request_content) //Метод добавления новой парковки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/validation.php");
        $validation = new Validation();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/random.php");
        $random=new Random();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);
        
        $parking_data=$request_content;

        //Валидация полученных данных
        $valid_latitude=$validation->validateCoordinates($parking_data['latitude'],"latitude");
        $valid_longitude=$validation->validateCoordinates($parking_data['longitude'],"longitude");
        if(!$valid_latitude || !$valid_longitude)
        {
            $response='{"response":"invalid_coordinates}';
            return($response);
        }

        //Проверка существования парковки с данными координатами
        $parking_check_array=$this->findParkingByCoordinates($parking_data['latitude'],$parking_data['longitude']);
        if(isset($parking_check_array['id']))
        {
            $response='{"response":"parking_coordinates_exist"}';
            return($response);
        }

        //Проверка наличия хотя бы одного парковочного места
        $parking_places=$request_content['parking_places'];
        if(count($parking_places)<1)
        {
            $response='{"response":"no_parking_places"}';
            return($response);
        }

        //Валидация данных парковочных мест
        for($i=0;$i<count($parking_places);$i++)
        {
            $valid_parking_place=$validation->validateParkingPlace($parking_places[$i],$parking_places);
            if(!$valid_parking_place)
            {
                $response='{"response":"invalid_parking_places"}';
                return($response);
            }
        }

        //Оставшиеся данные парковки
        $parking_data['user_id']=$user_data['id'];
        $parking_data['parking_id']=$random->randomString(20);
        for($i=0;$i<count($parking_places);$i++)
        {
            $parking_places[$i]["parking_place_id"]=$random->randomString(20);
        }

        //Проверка прав
        $create_new_rights=$rights->createNewParkingRights($user_data,$role);
        if(!$create_new_rights)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Внесение данных новой парковки в базу
        $response=$this->addNewParkingRequest($parking_data);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Внесение данных парковочных мест в базу
        $this->addNewParkingPlacesRequest($parking_places,$parking_data['parking_id']);

        //Успешное добавление парковки
        $response='{"response":"parking_card_add_complete"}';
        
        return($response);
    }

    public function editParkingCard($request_content) //Метод редактирования карточки парковки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/validation.php");
        $validation = new Validation();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/random.php");
        $random=new Random();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $parking_data=$request_content;

        //Проверка прав
        $edit_rights=$rights->editParkingRights($parking_data,$user_data,$role);
        if(!$edit_rights)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Валидация данных парковочных мест
        $parking_places=$request_content['parking_places'];
        for($i=0;$i<count($parking_places);$i++)
        {
            $valid_parking_place=$validation->validateParkingPlace($parking_places[$i],$parking_places);
            if(!$valid_parking_place)
            {
                $response='{"response":"invalid_parking_places"}';
                return($response);
            }
        }

        //Редактирование данных парковки в базе
        $response=$this->editParkingRequest($user_data["id"],$parking_data);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Редактирование данных парковочных мест в базе
        $parking_places=$request_content['parking_places'];
        for($i=0;$i<count($parking_places);$i++)
        {
            $parking_places[$i]["parking_place_id"]=$random->randomString(20);
        }
        $this->deleteAllParkingPlacesRequest($parking_data['parking_id']);
        $this->addNewParkingPlacesRequest($parking_places,$parking_data['parking_id']);

        //Успешное редактирование парковки
        $response='{"response":"parking_card_edit_complete"}';
        return($response);
    }

    public function saveDraftParkingCard($request_content) //Метод сохранения черновика парковки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $parking_data=$request_content;

        //Проверка прав
        $edit_rights=$rights->editParkingRights($parking_data,$user_data,$role);
        if(!$edit_rights)
        {
            $response='{"response":"request_error"}';
            return($response);
        }
        
        //Изменение статуса черновика
        $response=$this->removeDraftStatusRequest($user_data['id'],$parking_data);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Успешная отметка черновика
        $response='{"response":"parking_card_add_draft_complete"}';
        return($response);
    }

    public function deleteParkingCard($request_content) //Метод удаления парковки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/redirect.php");
        $redirect = new Redirect();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $parking_data=$request_content;

        //Проверка прав
        $edit_rights=$rights->editParkingRights($parking_data,$user_data,$role);
        if(!$edit_rights)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Удаление парковки
        $response=$this->deleteParkingCardRequest($user_data['id'],$parking_data);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Успешное удаление парковки
        $response='{"response":"delete_complete"}';
        return($response);
    }

    public function startRent($request_content) //Метод бронирования парковки
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/validation.php");
        $validation = new Validation();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/random.php");
        $random=new Random();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/date_conversion.php");
        $date_conversion=new DateConversion();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $rent_data=$request_content;

        //Валидация данных бронирования
        $valid_rent_data=$validation->validateRentData($rent_data);
        if(!$valid_rent_data)
        {
            $response='{"response":"invalid_rent_data"}';
            return($response);
        }

        //Проверка прав
        $rent_rights=$rights->rentRights($rent_data,$user_data,$role);
        if(!$rent_rights)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Бронирование парковки
        $rent_data["rent_number"]=($random->randomLetterString(1)).($random->randomNumberString(3));
        $parking_place_data=($this->getParkingPlaceDataByIdRequest($rent_data["parking_place_id"]))[0];
        $rent_data["parking_place_id"]=$parking_place_data["parking_place_id"];
        $rent_data["parking_id"]=$parking_place_data["parking_id"];

        $response=$this->rentParkingPlaceRequest($user_data,$rent_data);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        /*
        $response=$this->setParkingPlaceRentRequest($rent_data["parking_place_id"]);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }
            */

        //Успешное бронирование парковки
        $response='{
            "response":"rent_complete",
            "response_content": {
                "rent_number": "'.$rent_data["rent_number"].'",
                "parking_id": "'.$rent_data["parking_id"].'",
                "rent_start_date": "'.$date_conversion->convertDate($rent_data["date_start"]).'",
                "rent_end_date": "'.$date_conversion->convertDate($rent_data["date_end"]).'",
                "rent_start_time": "'.$rent_data["time_start"].'",
                "rent_end_time": "'.$rent_data["time_end"].'",
                "result_price": "'.$rent_data["result_price"].'",
                "transport_number": "'.$rent_data["transport_number"].'"
            }
        }';
        return($response);
    }



    //Методы работы со списками
    public function getListData($request_content) //Метод получения данных списков
    {
        $list_type=$request_content['list_type'];
        $list_info=$request_content['list_info'];

        //Список парковочных мест
        if($list_type=="parking_places")
        {
            $parking_id=$list_info;
            $list_data=$this->allParkingPlacesRequest($parking_id);
            $list_clear_data=$list_data;

            //Разделы заголовка
            $list_data["header"]=[
                "choice_checkbox"=>"",
                "parking_place_name"=>"Внутренний номер",
                "size"=>"Размер",
                "price"=>"Стоимость",
                "length_"=>"Длина, м",
                "width"=>"Ширина, м",
                "height"=>"Высота, м",
                "rent"=>""
            ];

            //Данные без изменений
            $list_data["clear_data"]=$list_clear_data;

            //Подготовка данных для вывода
            for($i=0;$i<count($list_data);$i++)
            {
                if(!isset($list_data[$i]))
                {continue;}

                //Единицы измерения стоимости
                if($list_data[$i]["price_units"]=="days")
                {$list_data[$i]["price"]=$list_data[$i]["price"]." руб\сутки";}
                if($list_data[$i]["price_units"]=="hours")
                {$list_data[$i]["price"]=$list_data[$i]["price"]." руб\час";}

                //Органичение высоты
                if($list_data[$i]["height_not_limited"]=='1')
                {$list_data[$i]["height"]="Не ограничена";}

                //Размер
                if($list_data[$i]["size"]=='C')
                {$list_data[$i]["size"]="Грузовой";}
                if($list_data[$i]["size"]=='CE')
                {$list_data[$i]["size"]="Грузовой с прицепом";}
                if($list_data[$i]["size"]=='C1')
                {$list_data[$i]["size"]="Малый грузовой";}
                if($list_data[$i]["size"]=='B')
                {$list_data[$i]["size"]="Легковой";}

                //Данные бронирования
                $rent=$list_data[$i]["rent"];
                $list_data[$i]["rent"]=[];
                if($rent=="1")
                {
                    $list_data[$i]["rent"]["content"]="Забронировано";

                    $list_data[$i]["rent"]["additional_info"]["style"]="negative";

                    $list_data[$i]["rent"]["additional_info"]["block_choice"]=true;

                    $list_data[$i]["rent"]["additional_info"]["link_button"]["text"]="Интервалы бронирования";
                    $list_data[$i]["rent"]["additional_info"]["link_button"]["link"]="rent_info.php?parking_place_id=".$list_data[$i]["parking_place_id"];
                }
                else
                {
                    $list_data[$i]["rent"]["content"]="Свободно";

                    $list_data[$i]["rent"]["additional_info"]["style"]="positive";

                    $list_data[$i]["rent"]["additional_info"]["block_choice"]=false;

                    $list_data[$i]["rent"]["additional_info"]["link_button"]["text"]="Интервалы бронирования";
                    $list_data[$i]["rent"]["additional_info"]["link_button"]["link"]="rent_info.php?parking_place_id=".$list_data[$i]["parking_place_id"];
                }
            }
        }

        $response=$list_data;

        return($response);
    }
}

?>