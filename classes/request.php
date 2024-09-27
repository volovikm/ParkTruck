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
    Метод getRentIntervals- принимает запрос на вывод данных об интервалах бронирования за период, выводит данные массивом в ответ
    Метод getRentIntervalData - принимает запрос на вывод данных о конкретном интервале бронирования, выводит данные массивом в ответ
    Метод stopRent - принимает запрос на отмену бронирования, запускает отмену
    Метод transportAction - принимает запрос на действия с ТС, и тип действия, запускает соответствующее действие
    Метод getTransportData - принимает запрос на вывод данных конкретного ТС, запускает вывод
    Метод userAdminAction - принимает запрос на действия с аккаунтом пользователя от администратора, запускает соответствующее действие

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

            //Запрос на действия с бронированием
            if(isset($request_content['rent_action']))
            {
                if($request_content['rent_action']=="rent_start")
                {
                    $response=$this->startRent($request_content);
                }

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на вывод данных интервалов бронирования
            if(isset($request_content['get_rent_intervals']))
            {
                $response=$this->getRentIntervals($request_content);

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на вывод данных конкретного интервала
            if(isset($request_content['get_rent_data'])) 
            {
                $response=$this->getRentIntervalData($request_content);

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на отмену бронирования
            if(isset($request_content['stop_rent'])) 
            {
                $response=$this->stopRent($request_content);

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на действия с ТС
            if(isset($request_content['transport_action']))
            {
                $response=$this->transportAction($request_content);

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на вывод данных конкретного ТС
            if(isset($request_content['get_transport_data']))
            {
                $response=$this->getTransportData($request_content);

                $this->response_json=json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            //Запрос на действия с аккаунтом от лица администратора
            if(isset($request_content['user_action']))
            {
                $response=$this->userAdminAction($request_content);

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

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/admin.php");
        $admin = new Admin();

        $auth_data=$request_content;

        //Проверка запроса на создание администратора
        $admin_data=$admin->createAdmin($auth_data["telephone"],$auth_data["password"]);
        if($admin_data!=false)
        {
            $auth_data=$admin_data;
        }

        //Проверка существования аккаунта с данным номером телефона
        $user_data=$this->findUserByTelephoneForAuth($auth_data["telephone"]);
        if(empty($user_data['id']))
        {
            $response='{"response":"account_not_exist"}';
            return($response);
        }

        //Проверка пароля
        $password_hash=$user_data["password_hash"];
        if(!password_verify($auth_data["password"], $password_hash))
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
        $reg_data["reg_confirmed"]="0";
        $reg_data["reg_date"]=date("Y-m-d");
        $reg_data["reg_time"]=date("H:i");
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

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rent.php");
        $rent=new Rent();

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

        //Проверка занятых интервалов
        $rent_interval_allowed=$rent->checkRentInterval($rent_data);
        if(!$rent_interval_allowed)
        {
            $response='{"response":"time_already_rent"}';
            return($response);
        }

        //Бронирование парковки
        $rent_data["rent_number"]=($random->randomLetterString(1)).($random->randomNumberString(3));
        $parking_place_data=($this->getParkingPlaceDataByIdRequest($rent_data["parking_place_id"]))[0];
        $rent_data["parking_place_id"]=$parking_place_data["id"];
        $rent_data["parking_id"]=$parking_place_data["parking_id"];
        $rent_data["rent_id"]=$random->randomString(20);

        //Определение номера ТС для бронирования с выбором ТС
        if($rent_data["transport_number"]=="" && $rent_data["transport_id"]!="")
        {
            $transport_data=($this->getTransportDataByIdRequest($user_data,$rent_data["transport_id"]))[0];
            $rent_data["transport_number"]=$transport_data["transport_number"];
        }

        //Определение id водителя
        $rent_data["user_id"]="0";
        if($role!="unauthorized")
        {
            $rent_data["user_id"]=$user_data["id"];
        }

        $response=$this->rentParkingPlaceRequest($user_data,$rent_data);
        if(!$response)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Успешное бронирование парковки
        $response='{
            "response":"rent_complete",
            "response_content": {
                "rent_number": "'.$rent_data["rent_number"].'",
                "rent_id": "'.$rent_data["rent_id"].'",
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

    public function getRentIntervals($request_content) //Метод получения данных об интервалах бронирования за период
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rent.php");
        $rent=new Rent();

        $parking_place_data=($this->getParkingPlaceDataByIdentifierRequest($request_content["parking_place_id"]))[0];

        $date_from=$request_content["date_from"];
        $date_to = date('Y-m-d', strtotime('+7 days', strtotime($date_from)));

        $rent_intervals=$rent->getRentIntervals($parking_place_data["id"],strtotime($date_from),strtotime($date_to));

        $dates_array=[];
        for($i=0;$i<7;$i++)
        {
            array_push($dates_array,(date('Y-m-d', strtotime('+'.$i.' days', strtotime($date_from)))));
        }

        //Вывод данных интервалов
        $response='{
            "response": "intervals_complete",
            "response_content": {
                "parking_place_id": "'.$parking_place_data["parking_place_id"].'",
                "parking_place_name": "'.$parking_place_data["parking_place_name"].'",
                "rent_intervals": '.json_encode($rent_intervals).',
                "dates": '.json_encode($dates_array).'
            }
        }';
        return($response);
    }

    public function getRentIntervalData($request_content) //Метод получения данных о конкретном интервале
    {
        $rent_interval_data=($this->getRentDataById($request_content["rent_id"]))[0];

        //Вывод данных интервалов
        $response='{
            "response": "interval_data_complete",
            "response_content": {
                "rent_data": '.json_encode($rent_interval_data).'
            }
        }';
        return($response);
    }

    public function stopRent($request_content) //Метод отмены бронирования
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $rent_id_string=$request_content["rent_id"];  
        $rent_id_array=explode("_",$rent_id_string);
        for($i=0;$i<count($rent_id_array);$i++)
        {
            $rent_id=$rent_id_array[$i];
            if($rent_id==""){continue;}

            $rent_data=($this->getRentDataById($rent_id))[0];
            $parking_place_data=($this->getParkingPlaceDataByIdRequest($rent_data["parking_place_id"]))[0];
            $parking_data=($this->parkingCardDataRequest($parking_place_data["parking_id"]))[0];

            //Проверка прав
            $edit_rights=$rights->editParkingRights($parking_data,$user_data,$role);
            if(!$edit_rights && $role=="parking_owner")
            {
                $response='{"response":"request_error"}';
                return($response);
            }

            $cancel_rent_rights=$rights->cancelRentRights($user_data,$rent_data,$parking_data);;
            if(!$cancel_rent_rights)
            {
                $response='{"response":"request_error"}';
                return($response);
            }

            //Отмена бронирования
            $response=$this->stopRentRequest($rent_id,$user_data);
            if(!$response)
            {
                $response='{"response":"request_error"}';
                return($response);
            }
        }

        //Успешная отмена бронирования
        $response='{"response":"stop_rent_complete"}';
        return($response);
    }

    public function transportAction($request_content) //Метод действий с ТС
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $action=$request_content["action"];  

        //Добавление нового ТС
        if($action=="add")
        {
            //Список всех ТС пользователя
            $user_transport_data=$this->getUserTransportDataRequest($user_data);

            //Проверка прав на добавление ТС
            $add_rights=$rights->transportRights($user_data,$role,$action,$user_transport_data);
            if(!$add_rights)
            {
                $response='{"response":"request_error"}';
                return($response);
            }

            //Добавление ТС
            $response=$this->addNewTransportRequest($request_content,$user_data);
            if(!$response)
            {
                $response='{"response":"request_error"}';
                return($response);
            }
        }

        //Редактирование ТС
        if($action=="edit")
        {
            //Список всех ТС пользователя
            $user_transport_data=$this->getUserTransportDataRequest($user_data);

            //Данные конкретного ТС
            $transport_data=($this->getTransportDataByIdRequest($user_data,$request_content["transport_id"]))[0];

            //Проверка прав на редактирование ТС
            $edit_rights=$rights->transportRights($user_data,$role,$action,$user_transport_data,$transport_data);
            if(!$edit_rights)
            {
                $response='{"response":"request_error"}';
                return($response);
            }

            //Редактирование ТС
            $response=$this->editTransportRequest($request_content,$user_data);
            if(!$response)
            {
                $response='{"response":"request_error"}';
                return($response);
            }
        }

        //Удаление ТС
        if($action=="delete")
        {
            //Список всех ТС пользователя
            $user_transport_data=$this->getUserTransportDataRequest($user_data);

            $transport_id_array=explode("_",$request_content["transport_id"]);

            for($i=0;$i<count($transport_id_array);$i++)
            {
                if($transport_id_array[$i]=="")
                {continue;}

                //Данные конкретного ТС
                $transport_data=($this->getTransportDataByIdRequest($user_data,$transport_id_array[$i]))[0];

                //Проверка прав на редактирование ТС
                $edit_rights=$rights->transportRights($user_data,$role,$action,$user_transport_data,$transport_data);
                if(!$edit_rights)
                {
                    $response='{"response":"request_error"}';
                    return($response);
                }

                //Удаление ТС
                $response=$this->deleteTransportRequest($transport_data,$user_data);
                if(!$response)
                {
                    $response='{"response":"request_error"}';
                    return($response);
                }
            }
        }

        $response='{"response":"transport_action_complete"}';
        return($response);
    }

    public function getTransportData($request_content) //Метод вывода данных конкретного ТС 
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        $transport_data=$this->getTransportDataByIdRequest($user_data,$request_content["transport_id"]);

        //Вывод данных интервалов
        $response='{
            "response": "transport_data_complete",
            "response_content": {
                "transport_data": '.json_encode($transport_data).'
            }
        }';
        return($response);
    }

    public function userAdminAction($request_content) //Метод действий с аккаунтом от администратора
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
        $account = new Account();

        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/rights_check.php");
        $rights = new Rights();

        $user_data=$account->checkAuth();
        $role=$account->getRole($user_data);

        //Проверка прав на действия с пользователями
        $action_rights=$rights->adminRights($user_data,$role);
        if(!$action_rights)
        {
            $response='{"response":"request_error"}';
            return($response);
        }

        //Выполнение действий
        $action_type=$request_content["action_type"];
        $request_content["user_id"]=explode("_",$request_content["user_id"])[1];
        $required_user_data=$this->findUserById($request_content["user_id"]);

        if($action_type=="delete")
        {
            $this->setUserDeletedRequest($required_user_data);
        }

        if($action_type=="block")
        {
            if($required_user_data["status"]=="blocked")
            {
                $this->setUserActiveRequest($required_user_data);
            }

            if($required_user_data["status"]=="active")
            {
                $this->setUserBlockedRequest($required_user_data);
            }
        }

        //Вывод данных
        $response='{
            "response": "user_action_complete"
        }';
        return($response);
    }



    //Методы работы со списками
    public function getListData($request_content) //Метод получения данных списков
    {
        $list_type=$request_content['list_type'];
        $list_info=$request_content['list_info'];

        //Список парковок
        if($list_type=="parkings")
        {
            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
            $account = new Account();

            $user_data=$account->checkAuth();
            $role=$account->getRole($user_data);

            $list_data=[];
            if($role=="parking_owner")
            {
                $parkings_data=$this->userParkingsDataRequest($user_data["id"]);
                $list_data=$parkings_data;
            }
            if($role=="admin")
            {
                $parkings_data=$this->allParkingsDataRequest();
                $list_data=$parkings_data;
            }
            
            $list_clear_data=$list_data;

            //Разделы заголовка
            $list_data["header"]=[
                "link"=>"",
                "draft"=>"",
                "name"=>"Название парковки",
                "adress"=>"Адрес"
            ];

            //Данные без изменений
            $list_data["clear_data"]=$list_clear_data;

            //Подготовка данных для вывода
            for($i=0;$i<count($list_data);$i++)
            {
                if(!isset($list_data[$i]))
                {continue;}

                $list_data[$i]["link"]["content"]="";

                $list_data[$i]["link"]["additional_info"]["link_button"]["text"]="Открыть парковку";
                $list_data[$i]["link"]["additional_info"]["link_button"]["action"]="redirect";
                $list_data[$i]["link"]["additional_info"]["link_button"]["action_info"]["link"]="parking_card.php?parking_id=".$list_data[$i]["parking_id"];

                if($list_data[$i]["draft"]=="1")
                {$list_data[$i]["draft"]="Черновик";}
                if($list_data[$i]["draft"]=="0")
                {$list_data[$i]["draft"]="";}
            }
        }

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
                "price_days"=>"Тариф:  руб\\сутки, ",
                "price_hours"=>" руб\\час ",
                "rent"=>""
            ];

            //Данные без изменений
            $list_data["clear_data"]=$list_clear_data;

            //Подготовка данных для вывода
            for($i=0;$i<count($list_data);$i++)
            {
                if(!isset($list_data[$i]))
                {continue;}

                //Размер
                if($list_data[$i]["size"]=='light_cargo')
                {$list_data[$i]["size"]="Грузовой малый";}
                if($list_data[$i]["size"]=='medium_cargo')
                {$list_data[$i]["size"]="Грузовой средний";}
                if($list_data[$i]["size"]=='light_vehicle')
                {$list_data[$i]["size"]="Легковой";}
                if($list_data[$i]["size"]=='euro_truck')
                {$list_data[$i]["size"]="Еврофура";}
                if($list_data[$i]["size"]=='hood_truck')
                {$list_data[$i]["size"]="Капотник";}
                if($list_data[$i]["size"]=='trailer_truck')
                {$list_data[$i]["size"]="Сцепка";}

                //Данные бронирования
                $list_data[$i]["rent"]=[];

                $list_data[$i]["rent"]["content"]="";

                $list_data[$i]["rent"]["additional_info"]["link_button"]["text"]="Интервалы бронирования";
                $list_data[$i]["rent"]["additional_info"]["link_button"]["action"]="show_modal_window";
                $list_data[$i]["rent"]["additional_info"]["link_button"]["action_info"]["item_id"]=$list_data[$i]["parking_place_id"];
                $list_data[$i]["rent"]["additional_info"]["link_button"]["action_info"]["block_id"]="parking_place_intervals_form";
            }
        }

        //Список ТС
        if($list_type=="transport")
        {
            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
            $account = new Account();

            $user_data=$account->checkAuth();
            $list_data=$this->getUserTransportDataRequest($user_data);
            $list_clear_data=$list_data;

            //Разделы заголовка
            $list_data["header"]=[
                "choice_checkbox"=>"",
                "transport_number"=>"Госномер",
                "transport_name"=>"Название ТС",
                "transport_size"=>"Типовой размер",
                "properties"=>"Особенности",
            ];

            //Данные без изменений
            $list_data["clear_data"]=$list_clear_data;

            //Подготовка данных для вывода
            for($i=0;$i<count($list_data);$i++)
            {
                if(!isset($list_data[$i]))
                {continue;}

                //Размер
                if($list_data[$i]["transport_size"]=='light_cargo')
                {$list_data[$i]["transport_size"]="Грузовой малый";}
                if($list_data[$i]["transport_size"]=='medium_cargo')
                {$list_data[$i]["transport_size"]="Грузовой средний";}
                if($list_data[$i]["transport_size"]=='light_vehicle')
                {$list_data[$i]["transport_size"]="Легковой";}
                if($list_data[$i]["transport_size"]=='euro_truck')
                {$list_data[$i]["transport_size"]="Еврофура";}
                if($list_data[$i]["transport_size"]=='hood_truck')
                {$list_data[$i]["transport_size"]="Капотник";}
                if($list_data[$i]["transport_size"]=='trailer_truck')
                {$list_data[$i]["transport_size"]="Сцепка";}

                //Особенности
                $properties_array=explode(" ",$list_data[$i]["properties"]);
                $list_data[$i]["properties"]="";
                if(count($properties_array)>1)
                {
                    for($j=0;$j<count($properties_array);$j++)
                    {
                        if($properties_array[$j]=="refrigerator")
                        {
                            $list_data[$i]["properties"]=$list_data[$i]["properties"]."Рефрежиратор; ";
                        }

                        if($properties_array[$j]=="oversized")
                        {
                            $list_data[$i]["properties"]=$list_data[$i]["properties"]."Негабарит; ";
                        }

                        if($properties_array[$j]=="electrocar")
                        {
                            $list_data[$i]["properties"]=$list_data[$i]["properties"]."Электромобиль; ";
                        }
                    }
                }
            }
        }

        //Список бронирований
        if($list_type=="rent")
        {
            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
            $account = new Account();

            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/date_conversion.php");
            $date_conversion = new DateConversion();

            $user_data=$account->checkAuth();
            $role=$account->getRole($user_data);

            $list_data=[];
            if($role=="parking_owner")
            {
                $parkings_data=$this->userParkingsDataRequest($user_data["id"]);
                for($i=0;$i<count($parkings_data);$i++)
                {
                    $list_data=array_merge($list_data,$this->getRentDataByParkingId($parkings_data[$i]));
                }
            }
            if($role=="driver")
            {
                $list_data=$this->getRentDataByUserId($user_data);
            }
            if($role=="admin")
            {
                $list_data=$this->getAllRentData();
            }
            
            $list_clear_data=$list_data;

            //Разделы заголовка
            $list_data["header"]=[
                "choice_checkbox"=>"",
                "rent_number"=>"Номер бронирования",
                "transport_number"=>"Госномер ТС",
                "start_datetime"=>"Начало",
                "end_datetime"=>"Конец",
                "status"=>"Статус",
                "parking_adress"=>"Адрес парковки",
            ];

            //Данные без изменений
            $list_data["clear_data"]=$list_clear_data;

            //Подготовка данных для вывода
            for($i=0;$i<count($list_data);$i++)
            {
                if(!isset($list_data[$i]))
                {continue;}

                //Адрес парковки
                $parking_place_data=($this->getParkingPlaceDataByIdRequest(id: $list_data[$i]["parking_place_id"]))[0];
                $parking_data=($this->parkingCardDataRequest($parking_place_data["parking_id"]))[0];
                $list_data[$i]["parking_adress"]=$parking_data["adress"];

                //Начало бронирования
                $list_data[$i]["start_datetime"]=($date_conversion->convertDate($list_data[$i]["rent_start_date"]))." ".$list_data[$i]["rent_start_time"];
            
                //Конец бронирования
                $list_data[$i]["end_datetime"]=($date_conversion->convertDate($list_data[$i]["rent_end_date"]))." ".$list_data[$i]["rent_end_time"];

                //Статус
                if($list_data[$i]["active"]=="1")
                {
                    $list_data[$i]["status"]="Активно";
                }
                if($list_data[$i]["active"]=="0" && $list_data[$i]["canceled"]=="0")
                {
                    $list_data[$i]["status"]="Завершено";
                }
                if($list_data[$i]["canceled"]=="1")
                {
                    $list_data[$i]["status"]="Отмена ";

                    $canceled_by_id=$list_data[$i]["canceled_by"];
                    $canceled_by_data=$this->findUserById($canceled_by_id);
                    if($canceled_by_data["role"]=="driver")
                    {
                        $list_data[$i]["status"]=$list_data[$i]["status"]."пользователем";
                    }
                    if($canceled_by_data["role"]=="parking_owner")
                    {
                        $list_data[$i]["status"]=$list_data[$i]["status"]."администрацией парковки";
                    }
                    if($canceled_by_data["role"]=="admin")
                    {
                        $list_data[$i]["status"]=$list_data[$i]["status"]."администратором";
                    }
                }
            }
        }

        //Список пользователей
        if($list_type=="users")
        {
            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/account.php");
            $account = new Account();

            require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/classes/date_conversion.php");
            $date_conversion = new DateConversion();

            $user_data=$account->checkAuth();
            $role=$account->getRole($user_data);

            $list_data=$this->getAllUsersData();

            //Разделы заголовка
            $list_data["header"]=[
                "choice_checkbox"=>"",
                "telephone"=>"Телефон",
                "role"=>"Роль",
                "reg_datetime"=>"Дата регистрации",
                "status"=>"Статус",
            ];

            //Подготовка данных для вывода
            for($i=0;$i<count($list_data);$i++)
            {
                if(!isset($list_data[$i]))
                {continue;}

                //Роль
                $list_data[$i]["role"]=$account->roleToText($list_data[$i]["role"]);

                //Дата регистрации
                $list_data[$i]["reg_datetime"]=($date_conversion->convertDate($list_data[$i]["reg_date"]))." ".$list_data[$i]["reg_time"];

                //Хэш пароля
                $list_data[$i]["password_hash"]="";

                //Статус
                if($list_data[$i]["status"]=="active")
                {$list_data[$i]["status"]="Активен";}
                if($list_data[$i]["status"]=="deleted")
                {$list_data[$i]["status"]="Удалён";}
                if($list_data[$i]["status"]=="blocked")
                {$list_data[$i]["status"]="Заблокирован";}
            }
        }


        $response=$list_data;

        return($response);
    }
}

/*
Перенести путевку на github
Сделать тестовую лицензию на путевке
*/

?>