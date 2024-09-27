function authDataHandler(auth_data_json)
{
    auth_data_json=auth_data_json.replace("/", '');
    let auth_data = JSON.parse(auth_data_json);
    auth_data = JSON.parse(auth_data);
    let response=auth_data['response'];
    let error_message=document.getElementById("error_message");

    //Неправильный номер телефона или пароль
    if(response==="account_not_exist")
    {
        error_message.innerHTML="Неверный номер телефона или пароль";
        return(false);
    }

    //Успешная авторизация
    if(response==="auth_complete")
    {
        top.location.href="../../index.php";
    }
}