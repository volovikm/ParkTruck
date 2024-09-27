//Обработчики кнопок сайдбара

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

function deleteUserButtonHandler() //Обработчик кнопки удаления пользователя
{
    let delete_user_button=document.getElementById("delete_user_button");
    if(delete_user_button===null)
    {return(false);}

    //click listener на кнопку
    delete_user_button.addEventListener("click", (event) => {

        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);

        //Отправка данных формы
        let url="../request_handler.php";
        var data = {
            user_action: true,
            action_type: "delete",
            user_id: choice_input.value,
        };
        var data_json = JSON.stringify(data);
        requestTo(userActionDataHandler,data_json,url);


    });
}
deleteUserButtonHandler();

function blockUserButtonHandler() //Обработчик кнопки блокировки пользователя
{
    let block_user_button=document.getElementById("block_user_button");
    if(block_user_button===null)
    {return(false);}

    //click listener на кнопку
    block_user_button.addEventListener("click", (event) => {

        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);

        //Отправка данных формы
        let url="../request_handler.php";
        var data = {
            user_action: true,
            action_type: "block",
            user_id: choice_input.value,
        };
        var data_json = JSON.stringify(data);
        requestTo(userActionDataHandler,data_json,url);


    });
}
blockUserButtonHandler();


//Обработчик ответов сервера
function userActionDataHandler(user_action_data_json)
{
    user_action_data_json=user_action_data_json.replace("/", '');
    let user_action_data = JSON.parse(user_action_data_json);
    user_action_data = JSON.parse(user_action_data);
    let response=user_action_data["response"];
    let error_message=document.getElementById("error_message");

    //Ошибка сервера
    if(response==="request_error")
    {
        error_message.innerHTML="Ошибка сервера";
        return(false);
    }

    //Успешное действие с пользователем
    if(response==="user_action_complete")
    {
        window.location.reload();
    }
}