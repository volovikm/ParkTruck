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
    let parking_places="";

    //Сбор данных input, select
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

    //Сбор данных парковочных мест
    var parking_places_json=readCookie("parking_places_data");
    if(parking_places_json !== undefined)
    {
        var parking_places_data = JSON.parse(parking_places_json);
        parking_places=objectToArray(parking_places_data);
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
        if(parking_places==="")
        {
            error_message.innerHTML="Введите парковочные места";
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
        parking_places: parking_places
    };
    var data_json = JSON.stringify(data);
    console.log(data_json);
    //requestTo(parkingCardDataHandler,data_json,url);
}

//Обработчик формы парковочного места
function parkingPlaceFormHandler(action)
{
    var parking_place_form = document.getElementById("parking_place_form");
    let inputs = parking_place_form.querySelectorAll('input');
    let selects = parking_place_form.querySelectorAll('select');

    let size="";
    let length="";
    let width="";
    let height="";
    let height_not_limited=false;
    let price="";
    let price_units="";

    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];

        //Поле ввода длины
        if(input.id=="length")
        {
            length=input.value;
        }

        //Поле ввода ширины
        if(input.id=="width")
        {
            width=input.value;
        }

        //Поле ввода высоты
        if(input.id=="height")
        {
            height=input.value;
        }

        //Чекбокс неограниченной высоты
        if(input.id=="height_not_limited" && input.checked)
        {
            height_not_limited=true;
        }

        //Поле ввода стоимости
        if(input.id=="price")
        {
            price=input.value;
        }
    }

    for (let i = 0; i < selects.length; i++) 
    {
        let select=selects[i];

        //Поле ввода типового размера
        if(select.id=="size")
        {
            size=select.value;
        }
    
        //Поле ввода единиц измерения стоимости
        if(select.id=="price_units")
        {
            price_units=select.value;
        }
    }

    //Проверки формы
    let error_message=document.getElementById("error_message_parking_place");

    //Проверка пустой формы
    if(action=="create_new")
    {
        if(price==="")
        {
            error_message.innerHTML="Заполните стоимость парковки";
            return(false);
        }
    }

    //Сбор общего массива с данными формы, преобразование для отображения в списке
    var parking_place_clear_data = {  //Массив с чистыми данными для дальнейшей отправки на сервер
        "size": size,
        "length": length,
        "width": width,
        "height": height,
        "height_not_limited": height_not_limited,
        "price": price,
        "price_units": price_units,
    }
    var parking_places_json=readCookie("parking_places_data");
    if(parking_places_json !== undefined)
    {
        var parking_places_data = JSON.parse(parking_places_json);
        var parking_places_array=objectToArray(parking_places_data);
    }
    else
    {
        parking_places_array=[];
    }
    parking_places_array.push(parking_place_clear_data);
    parking_places_data = JSON.stringify(parking_places_array);
    writeCookie("parking_places_data", parking_places_data, 30);

    //Преобразование данных
    if(price_units=="days")
    {price=price+" руб/сутки";}
    if(price_units=="hours")
    {price=price+" руб/час"; }

    if(size=='C')
    {size="Грузовой";}
    if(size=='CE')
    {size="Грузовой с прицепом";}
    if(size=='C1')
    {size="Малый грузовой";}
    if(size=='B')
    {size="Легковой";}

    if(height_not_limited)
    {height="Не ограничена";}
    //Преобразование данных

    var parking_place_data = {  //Массив с преобразованными данными для вывода в список
    "size": size,
    "length": length,
    "width": width,
    "height": height,
    "height_not_limited": height_not_limited,
    "price": price,
    "price_units": price_units,
    }

    //Сохранение данных формы в списке
    var list_data_json=readCookie("list_data");
    list_data_json=list_data_json.replace("/", '');
    var list_data = JSON.parse(list_data_json);
    var list_array=objectToArray(list_data);
    list_array.push(parking_place_data);
    listDisplay(list_array);

    //Закрытие формы
    parking_place_form.style.display="none";
    var parking_place_form_inputs=parking_place_form.querySelectorAll("input");
    for(i=0; i<parking_place_form_inputs.length;i++)
    {parking_place_form_inputs[i].value="";}
}