<?php

class SMSsender 
{
    public function sendSMS($telephone,$text) 
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/ParkTruck/services/sms-prosto_ru_php/sms-prosto_ru.php");
    
        $sender_name="ParkTruck";
        $result=smsapi_push_msg_nologin("info@wheelcapital.ru", "caPitaL508", $telephone, $text, array("sender_name"=>$sender_name));
    }

    public function sendRegConfirmSMS($telephone,$reg_confirm_code) 
    {
        $text="Код подтверждения: ".$reg_confirm_code;
        $this->sendSMS($telephone,$text);
    }
}

?>