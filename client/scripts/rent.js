function cancelRentButtonHandler() //Обработчик кнопки отмены бронирования
{
    let cancel_rent_button=document.getElementById("cancel_rent_button");
    if(cancel_rent_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_rent_button.addEventListener("click", (event) => {
        
        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);

        //Отправка данных формы
        let url="../request_handler.php";
        var data = {
            stop_rent: true,
            rent_id: choice_input.value,
        };
        var data_json = JSON.stringify(data);
        requestTo(cancelRentDataHandler,data_json,url);

    });
}
cancelRentButtonHandler();

function cancelButtonHandler() //Обработчик кнопки возврата на главную
{
    let cancel_button=document.getElementById("cancel_button");
    if(cancel_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_button.addEventListener("click", (event) => {
        console.log("cancel");
        redirectTo('map.php');
    });
}
cancelButtonHandler();

//Обработчик ответов сервера
function cancelRentDataHandler(cancel_rent_data_json)
{
    cancel_rent_data_json=cancel_rent_data_json.replace("/", '');
    let cancel_rent_data = JSON.parse(cancel_rent_data_json);
    cancel_rent_data = JSON.parse(cancel_rent_data);
    let response=cancel_rent_data["response"];
    let error_message=document.getElementById("error_message");

    //Ошибка сервера
    if(response==="request_error")
    {
        error_message.innerHTML="Ошибка сервера";
        return(false);
    }

    //Успешная отмена бронирования
    if(response==="stop_rent_complete")
    {
        window.location.reload();
    }
}