function regDataHandler(reg_data_json)
{
    reg_data_json=reg_data_json.replace("/", '');
    let reg_data = JSON.parse(reg_data_json);
    reg_data = JSON.parse(reg_data);
    let response=reg_data['response'];
    let error_message=document.getElementById("error_message");

    //Аккаунт уже существует
    if(response==="account_exists")
    {
        error_message.innerHTML="Аккаунт с данным номером телефона уже существует";
        return(false);
    }

    //Ошибка сервера
    if(response==="reg_request_error")
    {
        error_message.innerHTML="Ошибка сервера - попробуйте позднее";
        return(false);
    }

    //Не пройдена валидация данных
    if(response==="invalid_telephone")
    {
        error_message.innerHTML="Номер телефона должен начинаться на 7, +7 или 8, и содержать 11 цифр";
        return(false);
    }
    if(response==="invalid_role")
    {
        error_message.innerHTML="Ошибка сервера - попробуйте позднее";
        return(false);
    }

    //Успешная регистрация
    if(response==="reg_complete")
    {
        top.location.href="../../index.php";
    }
}