function regConfirmDataHandler(reg_confirm_data_json)
{
    reg_confirm_data_json=reg_confirm_data_json.replace("/", '');
    let reg_confirm_data = JSON.parse(reg_confirm_data_json);
    reg_confirm_data = JSON.parse(reg_confirm_data);
    let response=reg_confirm_data['response'];
    let error_message=document.getElementById("error_message");

    //Неправильный код подтверждения
    if(response==="wrong_reg_confirm_code")
    {
        error_message.innerHTML="Неверный код подтверждения";
        return(false);
    }

    //Успешное подтверждение регистрации
    if(response==="reg_confirm_complete")
    {
        window.location.href="../../index.php";
    }
}

//Обработчики кнопки повторной отправки кода
function disableSendCodeButton(send_code_button)
{
    let timer=readCookie("timer");

    send_code_button.classList.remove("secondary_button");
    send_code_button.classList.add("disabled_button");
    send_code_button.innerHTML="Отправить заново через <span id='timer_span'>"+timer+"</span> секунд";
}
function enableSendCodeButton(send_code_button)
{
    send_code_button.classList.add("secondary_button");
    send_code_button.classList.remove("disabled_button");
    send_code_button.innerHTML="Отправить";
}
function getSendCodeButtonState(send_code_button)
{
    let state="enabled";

    if(send_code_button.classList.contains("disabled_button"))
    {
        state="disabled";
    }

    return(state);
}
function sendCodeButtonHandler()
{
    let send_code_button=document.getElementById("send_code_button");

    let timer=readCookie("timer");

    //Обновление таймера
    var interval=setInterval(function() {
        let timer_span=document.getElementById("timer_span");
        if(timer_span!==null)
        {
            timer=readCookie("timer");

            if(timer<=0)
            {
                enableSendCodeButton(send_code_button);
                clearInterval(interval);
                
            }

            timer=timer-1;
            timer_span.innerHTML=timer;
            writeCookie("timer", timer, 30);
        }
    }, 1000);

    //click listener на кнопку
    send_code_button.addEventListener("click", (event) => {

        if(getSendCodeButtonState(send_code_button)=="enabled"){

            writeCookie("timer", 59, 30);
            disableSendCodeButton(send_code_button);

            //Отправка запроса на СМС
            let url="../../request_handler.php";
            var data = {
                send_sms_reg_confirm_code: 'true',
            };
            var data_json = JSON.stringify(data);
            requestTo(null,data_json,url);
        
        }else{

            enableSendCodeButton(send_code_button);
            
        }
    });
}

sendCodeButtonHandler();