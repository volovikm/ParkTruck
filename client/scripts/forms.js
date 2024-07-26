//Обработчик формы авторизации
function authFormHandler(auth_form)
{
    let url="../../request_handler.php";

    let telephone="";
    let password="";

    let inputs = document.querySelectorAll('input');
    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];
        
        //Поле ввода телефона
        if(input.id=="telephone")
        {
            telephone=input.value;
        }

        //Поле ввода пароля
        if(input.id=="password")
        {
            password=input.value;
        }
    }

    //Проверки формы
    let error_message=document.getElementById("error_message");

    //Проверка пустой формы
    if(telephone==="" || password==="")
    {
        error_message.innerHTML="Заполните поля данной формы";
        return(false);
    }

    //Отправка данных формы
    var data = {
        auth: 'true',
        telephone: telephone,
        password: password,
    };
    var data_json = JSON.stringify(data);
    requestTo(authDataHandler,data_json,url);
}

//Обработчик формы регистрации
function regFormHandler(reg_form){
    let url="../../request_handler.php";

    let role="";
    let telephone="";
    let password="";
    let password_repeat=false;
    let license_checkbox=false;

    let inputs = document.querySelectorAll('input');
    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];
        
        //Поле ввода телефона
        if(input.id=="telephone")
        {
            telephone=input.value;
        }

        //Поле ввода пароля
        if(input.id=="password")
        {
            password=input.value;
        }

        //Поле ввода повтора пароля
        if(input.id=="password_repeat")
        {
            password_repeat=input.value;
        }

        //Чекбокс лицензионного соглашения
        if(input.id=="license_checkbox" && input.checked)
        {
            license_checkbox=true;
        }
    }

    let selects = document.querySelectorAll('select');
    for (let i = 0; i < selects.length; i++) 
    {
        let select=selects[i];
        
        //Поле ввода роли
        if(select.id=="role")
        {
            role=select.value;
        }
    }

    //Проверки формы
    let error_message=document.getElementById("error_message");

    //Проверка пустой формы
    if(telephone==="" || 
        password==="" ||
        password_repeat===false)
    {
        
        error_message.innerHTML="Заполните все поля данной формы";
        return(false);
    }

    //Проверка совпадения паролей
    if(password !== password_repeat)
    {
        error_message.innerHTML="Повтор пароля должен совпадать с паролем";
        return(false);
    }

    //Проверка чекбокса лицензионного соглашения
    if(!license_checkbox)
    {
        error_message.innerHTML="Подтвердите согласие на обработку персональных данных";
        return(false);
    }

    //Отправка данных формы
    var data = {
        reg: 'true',
        telephone: telephone,
        password: password,
        role: role
    };
    var data_json = JSON.stringify(data);
    requestTo(regDataHandler,data_json,url);
}

//Обработчик формы подтверждения регистрации
function regConfirmFormHandler(reg_confirm_form){
    let url="../../request_handler.php";

    let reg_confirm_code="";

    let inputs = document.querySelectorAll('input');
    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];
        
        //Поле ввода кода подтверждения регистрации
        if(input.id=="reg_confirm_code")
        {
            reg_confirm_code=input.value;
        }
    }

    //Проверки формы
    let error_message=document.getElementById("error_message");

    //Проверка пустой формы
    if(reg_confirm_code==="")
    {
        error_message.innerHTML="Введите код из СМС";
        return(false);
    }

    //Отправка данных формы
    var data = {
        reg_confirm: 'true',
        reg_confirm_code: reg_confirm_code,
    };
    var data_json = JSON.stringify(data);
    requestTo(regConfirmDataHandler,data_json,url);
}

//Обработчик формы карточки парковки
function parkingCardFormHandler(action)
{
    let url="../request_handler.php";

    let name_="";
    let latitude="";
    let longitude="";
    let adress="";

    let inputs = document.querySelectorAll('input');
    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];
        
        //Поле ввода названия парковки
        if(input.id=="name")
        {
            name_=input.value;
        }

        //Поле ввода широты
        if(input.id=="latitude")
        {
            latitude=input.value;
        }

        //Поле ввода долготы
        if(input.id=="longitude")
        {
            longitude=input.value;
        }

        //Поле ввода адреса
        if(input.id=="adress")
        {
            adress=input.value;
        }
    }

    //Проверки формы
    let error_message=document.getElementById("error_message");

    //Проверка пустой формы
    if(action=="create_new")
    {
        if(latitude==="" || longitude==="")
        {
            error_message.innerHTML="Заполните координаты";
            return(false);
        }
        if(name_==="")
        {
            error_message.innerHTML="Заполните название парковки";
            return(false);
        }
        if(adress==="")
        {
            error_message.innerHTML="Неверный адрес";
            return(false);
        }
    }

    if(action=="edit")
    {

    }

    //Отправка данных формы
    var data = {
        parking_card_action: action,
        name: name_,
        latitude: latitude,
        longitude: longitude,
        adress: adress,
    };
    var data_json = JSON.stringify(data);
    requestTo(parkingCardDataHandler,data_json,url);
}

//Обработчик формы парковочного места
function parkingPlaceFormHandler(action)
{
    var parking_place_form = document.getElementById("parking_place_form");
    let inputs = parking_place_form.querySelectorAll('input');

    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];

        console.log(input);
        
        /*
        //Поле ввода названия парковки
        if(input.id=="name")
        {
            name_=input.value;
        }

        //Поле ввода широты
        if(input.id=="latitude")
        {
            latitude=input.value;
        }

        //Поле ввода долготы
        if(input.id=="longitude")
        {
            longitude=input.value;
        }

        //Поле ввода адреса
        if(input.id=="adress")
        {
            adress=input.value;
        }
            */
    }
}