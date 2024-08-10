<?php
        class DataBaseRequests
        {
                //Соединение с базой данных
                public function connectDataBase() 
                {
                        //$db = new PDO('mysql:host=prk4440820.mysql;dbname=prk4440820_base', 'prk4440820_adm', 'kQp3JiE/');
                        $db = new PDO('mysql:host=127.0.0.1;dbname=prk4440820_base', 'root', '');
                        return($db);
                }

                
                //Запросы по аккаунту

                //Запрос на регистрацию пользователя
                public function regNewUserRequest($reg_data) 
                {
                        $db=$this->connectDataBase();

                        try {
                                $sql = "INSERT INTO users (
                                telephone, 
                                role,
                                password_hash,
                                reg_confirm_code
                                ) VALUES (
                                :telephone, 
                                :role,
                                :password_hash,
                                :reg_confirm_code
                                )";
                                $stmt=$db->prepare($sql);
                                $stmt->bindValue(":telephone", $reg_data['telephone']);
                                $stmt->bindValue(":role", $reg_data['role']);
                                $stmt->bindValue(":password_hash", $reg_data['password_hash']);
                                $stmt->bindValue(":reg_confirm_code", $reg_data['reg_confirm_code']);
                                $affectedRowsNumber=$stmt->execute();
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                                return(false);
                        }catch (PDOException $e) {}
                }    

                //Запрос на проверку существования аккаунта с данным номером телефона и ролью
                public function checkExistingAccount($reg_data,$confirmed) 
                {
                        $db=$this->connectDataBase();

                        if($confirmed){
                                $reg_confirmed='1';
                        }else{
                                $reg_confirmed='0';
                        }

                        try 
                        {
                                $sql="SELECT * FROM `users` WHERE 
                                telephone = :telephone AND 
                                reg_confirmed = :reg_confirmed";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":telephone", $reg_data['telephone']);
                                $stmt->bindValue(":reg_confirmed", $reg_confirmed);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if(isset($array[0]))
                                {
                                        $array=$array[0];
                                }
                                return($array);
                        }catch (PDOException $e) {}
                        return(false);
                }

                //Запрос на удаление существующего аккаунта с данным телефоном и ролью
                public function deleteExistingAccount($user_id)
                {
                        $db=$this->connectDataBase();

                        try {
                                $sql = "DELETE FROM users WHERE 
                                id = :user_id";
                                $stmt=$db->prepare($sql);
                                $stmt->bindValue(":user_id", $user_id);
                                $affectedRowsNumber=$stmt->execute();
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                                return(false);
                        }catch (PDOException $e) {}
                }

                //Запрос на данные аккаунта с номером телефона
                public function findUserByTelephone($telephone) 
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql="SELECT * FROM `users` WHERE 
                                telephone = :telephone AND
                                deleted = :deleted";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":telephone", $telephone);
                                $stmt->bindValue(":deleted", '0');
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if(isset($array[0]))
                                {
                                        $array=$array[0];
                                }
                                return($array);
                        }catch (PDOException $e) {}
                        return(false);
                }

                //Запрос на данные аккаунта по id
                public function findUserById($user_id) 
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql="SELECT * FROM `users` WHERE 
                                id = :user_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if(isset($array[0]))
                                {
                                        $array=$array[0];
                                }
                                return($array);
                        }catch (PDOException $e) {}
                        return(false);
                }

                //Запрос на подтверждение регистрации пользователя
                public function confirmRegUser($user_id) 
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql = "UPDATE users SET
                                reg_confirmed=:reg_confirmed
                                WHERE 
                                id=:user_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":reg_confirmed", '1');
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $affectedRowsNumber=$stmt->execute(); 
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                        }catch (PDOException $e) {}
                        return(false);
                }

                //Запрос на обновление времени таймаута отправки СМС с кодом подтверждение регистрации
                public function updateRegConfirmSMSTimeout($user_id,$new_timeout) 
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql = "UPDATE users SET
                                reg_confirm_code_sms_time=:reg_confirm_code_sms_time
                                WHERE 
                                id=:user_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":reg_confirm_code_sms_time", $new_timeout);
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $affectedRowsNumber=$stmt->execute(); 
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                        }catch (PDOException $e) {}
                        return(false);
                }



                //Запросы по парковкам

                //Запрос данных всех парковок
                public function allParkingsDataRequest() 
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parkings`";
                                $stmt = $db->prepare($sql);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }

                //Запрос данных всех активных парковок LEGACY
                public function allActiveParkingsDataRequest() 
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parkings` WHERE draft='0'";
                                $stmt = $db->prepare($sql);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }

                //Запрос данных парковок пользователя
                public function userParkingsDataRequest($user_id) 
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parkings` 
                                WHERE
                                user_id = :user_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }

                //Запрос данных конкретной парковки
                public function parkingCardDataRequest($parking_id) 
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parkings` 
                                WHERE
                                parking_id = :parking_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":parking_id", $parking_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }

                //Запрос данных парковки по координатам
                public function findParkingByCoordinates($latitude,$longitude)
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parkings` 
                                WHERE
                                latitude = :latitude AND
                                longitude = :longitude";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":latitude", $latitude);
                                $stmt->bindValue(":longitude", $longitude);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }

                //Запрос на добавление новой парковки
                public function addNewParkingRequest($parking_data) 
                {
                        $db=$this->connectDataBase();

                        try {
                                $sql = "INSERT INTO parkings (
                                parking_id,
                                name, 
                                latitude,
                                longitude,
                                adress,
                                user_id,
                                draft
                                ) VALUES (
                                :parking_id,
                                :name, 
                                :latitude,
                                :longitude,
                                :adress,
                                :user_id,
                                :draft
                                )";
                                $stmt=$db->prepare($sql);
                                $stmt->bindValue(":parking_id", $parking_data['parking_id']);
                                $stmt->bindValue(":name", $parking_data['name']);
                                $stmt->bindValue(":latitude", $parking_data['latitude']);
                                $stmt->bindValue(":longitude", $parking_data['longitude']);
                                $stmt->bindValue(":adress", $parking_data['adress']);
                                $stmt->bindValue(":user_id", $parking_data['user_id']);
                                $stmt->bindValue(":draft", $parking_data['draft']);
                                $affectedRowsNumber=$stmt->execute();
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                                return(false);
                        }catch (PDOException $e) {}
                }

                //Запрос на отключение статуса черновика
                public function removeDraftStatusRequest($user_id,$parking_data)
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql = "UPDATE parkings SET
                                draft='0'
                                WHERE 
                                parking_id=:parking_id AND 
                                user_id=:user_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":parking_id", $parking_data['parking_id']);
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $affectedRowsNumber=$stmt->execute(); 
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                        }catch (PDOException $e) {}
                        return(false);
                }

                //Запрос на редактирование существующей парковки
                public function editParkingRequest($user_id,$parking_data)
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql = "UPDATE parkings SET
                                name=:name
                                WHERE 
                                parking_id=:parking_id AND 
                                user_id=:user_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":name", $parking_data['name']);
                                $stmt->bindValue(":parking_id", $parking_data['parking_id']);
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                $affectedRowsNumber=$stmt->execute(); 
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                        }catch (PDOException $e) {}
                        return(false);
                }

                //Запрос на удаление существующей парковки
                public function deleteParkingCardRequest($user_id,$parking_data)
                {
                        $db=$this->connectDataBase();

                        try {
                                $sql = "DELETE FROM parkings WHERE 
                                user_id = :user_id AND 
                                parking_id = :parking_id";
                                $stmt=$db->prepare($sql);
                                $stmt->bindValue(":user_id", $user_id);
                                $stmt->bindValue(":parking_id", $parking_data['parking_id']);
                                $affectedRowsNumber=$stmt->execute();
                                if($affectedRowsNumber > 0 ){

                                        $response=$this->deleteAllParkingPlacesRequest($parking_data['parking_id']);
                                        return($response);
                                }
                                return(false);
                        }catch (PDOException $e) {}
                }



                //Запросы по парковочным местам
                public function allParkingPlacesRequest($parking_id) 
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parking_places` WHERE parking_id= :parking_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":parking_id", $parking_id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }

                public function addNewParkingPlacesRequest($parking_places,$parking_id) 
                {
                        $db=$this->connectDataBase();

                        foreach($parking_places as $parking_place)
                        {
                                try {
                                        $sql = "INSERT INTO parking_places (
                                        parking_id,
                                        parking_place_id,
                                        parking_place_name,
                                        size, 
                                        price_days,
                                        price_hours,
                                        length_,
                                        width,
                                        height,
                                        height_not_limited
                                        ) VALUES (
                                        :parking_id,
                                        :parking_place_id,
                                        :parking_place_name,
                                        :size, 
                                        :price_days,
                                        :price_hours,
                                        :length_,
                                        :width,
                                        :height,
                                        :height_not_limited
                                        )";
                                        $stmt=$db->prepare($sql);
                                        $stmt->bindValue(":parking_id", $parking_id);
                                        $stmt->bindValue(":parking_place_id", $parking_place['parking_place_id']);
                                        $stmt->bindValue(":parking_place_name", $parking_place['parking_place_name']);
                                        $stmt->bindValue(":size", $parking_place['size']);
                                        $stmt->bindValue(":price_days", $parking_place['price_days']);
                                        $stmt->bindValue(":price_hours", $parking_place['price_hours']);
                                        $stmt->bindValue(":length_", $parking_place['length_']);
                                        $stmt->bindValue(":width", $parking_place['width']);
                                        $stmt->bindValue(":height", $parking_place['height']);
                                        $stmt->bindValue(":height_not_limited",(int) $parking_place['height_not_limited']);
                                        $affectedRowsNumber=$stmt->execute();
                                }catch (PDOException $e) {}
                        }
                }

                public function deleteAllParkingPlacesRequest($parking_id)
                {
                        $db=$this->connectDataBase();

                        try {
                                $sql = "DELETE FROM parking_places WHERE 
                                parking_id = :parking_id";
                                $stmt=$db->prepare($sql);
                                $stmt->bindValue(":parking_id", $parking_id);
                                $affectedRowsNumber=$stmt->execute();
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                                return(false);
                        }catch (PDOException $e) {}
                }

                public function getParkingPlaceDataByIdRequest($id) //Запрос данных конкретного места по id
                {
                        $db=$this->connectDataBase();

                        $array=false;
                        try 
                        {
                                $sql="SELECT * FROM `parking_places` 
                                WHERE
                                id = :id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":id", $id);
                                $stmt->execute();
                                $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {}
                        return($array);
                }


                //Запросы по бронированию
                public function rentParkingPlaceRequest($user_data,$rent_data)
                {
                        $db=$this->connectDataBase();

                        try {
                                $sql = "INSERT INTO rent (
                                rent_number, 
                                parking_place_id,
                                rent_start_date,
                                rent_start_time,
                                rent_end_date,
                                rent_end_time,
                                result_price,
                                transport_id,
                                transport_number
                                ) VALUES (
                                :rent_number, 
                                :parking_place_id,
                                :rent_start_date,
                                :rent_start_time,
                                :rent_end_date,
                                :rent_end_time,
                                :result_price,
                                :transport_id,
                                :transport_number
                                )";
                                $stmt=$db->prepare($sql);
                                $stmt->bindValue(":rent_number", $rent_data['rent_number']);
                                $stmt->bindValue(":parking_place_id", $rent_data['parking_place_id']);
                                $stmt->bindValue(":rent_start_date", $rent_data['date_start']);
                                $stmt->bindValue(":rent_start_time", $rent_data['time_start']);
                                $stmt->bindValue(":rent_end_date", $rent_data['date_end']);
                                $stmt->bindValue(":rent_end_time", $rent_data['time_end']);
                                $stmt->bindValue(":result_price", $rent_data['result_price']);
                                $stmt->bindValue(":transport_id", $rent_data['transport_id']);
                                $stmt->bindValue(":transport_number", $rent_data['transport_number']);
                                $affectedRowsNumber=$stmt->execute();
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                                return(false);
                        }catch (PDOException $e) {}
                }

                public function setParkingPlaceRentRequest($parking_place_id) //Запрос на отметку места занятым
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql = "UPDATE parking_places SET
                                rent='1'
                                WHERE 
                                parking_place_id=:parking_place_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":parking_place_id", $parking_place_id);
                                $stmt->execute();
                                $affectedRowsNumber=$stmt->execute(); 
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                        }catch (PDOException $e) {}
                        return(false);
                }

                public function setParkingPlaceFreeRequest($parking_place_id) //Запрос на отметку места свободным
                {
                        $db=$this->connectDataBase();

                        try 
                        {
                                $sql = "UPDATE parking_places SET
                                rent='0'
                                WHERE 
                                parking_place_id=:parking_place_id";
                                $stmt = $db->prepare($sql);
                                $stmt->bindValue(":parking_place_id", $parking_place_id);
                                $stmt->execute();
                                $affectedRowsNumber=$stmt->execute(); 
                                if($affectedRowsNumber > 0 ){
                                        return(true);
                                }
                        }catch (PDOException $e) {}
                        return(false);
                }

        }
?>