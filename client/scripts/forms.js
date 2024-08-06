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
function parkingCardFormHandler(action,draft=false,parking_id=false)
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

    //Определение черновика
    if(draft){draft='1';}

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
        if(parking_places==="" || parking_places.length<1)
        {
            error_message.innerHTML="Введите парковочные места";
            return(false);
        }
    }

    if(action=="edit")
    {
        if(name_==="")
        {
            error_message.innerHTML="Заполните название парковки";
            return(false);
        }
        if(parking_places==="" || parking_places.length<1)
        {
            error_message.innerHTML="Введите парковочные места";
            return(false);
        }
    }

    if(action=="delete")
    {

    }

    //Отправка данных формы
    var data = {
        parking_card_action: action,
        parking_id: parking_id,
        name: name_,
        latitude: latitude,
        longitude: longitude,
        adress: adress,
        draft: draft,
        parking_places: parking_places
    };
    var data_json = JSON.stringify(data);
    requestTo(parkingCardDataHandler,data_json,url);
}

//Обработчик формы парковочного места
function parkingPlaceFormHandler(action,parking_place_id=false)
{
    var parking_place_form = document.getElementById("parking_place_form");
    let inputs = parking_place_form.querySelectorAll('input');
    let selects = parking_place_form.querySelectorAll('select');

    let parking_place_name="";
    let size="";
    let length="";
    let width="";
    let height="";
    let height_not_limited=false;
    let price="";
    let price_units="";
    let rent="";

    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];

        //Поле ввода внутреннего номера
        if(input.id=="parking_place_name")
        {
            parking_place_name=input.value;
        }

        //Поле ввода длины
        if(input.id=="length_")
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
    if(price==="")
    {
        error_message.innerHTML="Заполните стоимость парковки";
        return(false);
    }

    //Сбор общего массива с данными формы, преобразование для отображения в списке
    var parking_place_clear_data = {  //Массив с чистыми данными для дальнейшей отправки на сервер
        "parking_place_name": parking_place_name,
        "size": size,
        "length_": length,
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

    //Преобразование данных для вывода в список
    if(price_units=="days")
    {price=price+" руб\\сутки";}
    if(price_units=="hours")
    {price=price+" руб\\час"; }

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

    var rent_array=[]
    rent_array["additional_info"]=[];
    if(rent=="1")
    {
        rent_array["content"]="Забронировано";
        rent_array["additional_info"]["style"]="negative";
    }
    else
    {
        rent_array["content"]="Свободно";
        rent_array["additional_info"]["style"]="positive";
    }
    //Преобразование данных

    var parking_place_data = {  //Массив с преобразованными данными для вывода в список
        "parking_place_name": parking_place_name,
        "size": size,
        "length_": length,
        "width": width,
        "height": height,
        "height_not_limited": height_not_limited,
        "price": price,
        "price_units": price_units,
        "rent": rent_array
    }

    //Подготовка для отображения списка
    var list_data_json=readCookie("list_data");
    var list_data = JSON.parse(list_data_json);
    var list_array=objectToArray(list_data);

    if(action=="create_new")
    {
        parking_place_data["rent"]="";
        list_array.push(parking_place_data);
        parking_places_array.push(parking_place_clear_data);
    }

    if(action=="edit")
    {
        if(list_array[parking_place_id]===undefined) //Для существующих мест
        {
            for (let i = 0; i < list_array.length; i++) 
            {
                if(list_array[i]["id"]==parking_place_id)
                {
                    parking_place_id=i;
                    break;
                }
            }
        }

        parking_place_data["rent"]="";

        list_array[parking_place_id]=parking_place_data;
        parking_places_array[parking_place_id]=parking_place_clear_data;
    }
    
    listDisplay(list_array); //Вывод на отображение
    parking_places_data = JSON.stringify(parking_places_array); //Вывод на отправку на сервер
    writeCookie("parking_places_data", parking_places_data, 30); 

    //Закрытие формы
    parking_place_form.style.display="none";
    var parking_place_form_inputs=parking_place_form.querySelectorAll("input");
    for(i=0; i<parking_place_form_inputs.length;i++)
    {parking_place_form_inputs[i].value="";}
}

//Обработчик формы бронирования парковочного места
function parkingPlaceRentFormHandler(parking_place_id)
{
    var parking_place_rent_form = document.getElementById("parking_place_rent_form");
    let inputs = parking_place_rent_form.querySelectorAll('input');
    let selects = parking_place_rent_form.querySelectorAll('select');

    let transport_number="";
    let transport_id="";
    let date_start="";
    let time_start="";
    let date_end="";
    let time_end="";
    let result_price="";

    let action="rent_start";

    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];

        //Поле ввода госномера
        if(input.id=="transport_number")
        {
            transport_number=input.value;
        }

        //Поле ввода даты начала бронирования
        if(input.id=="date_start")
        {
            date_start=input.value;
        }

        //Поле ввода времени начала бронирования
        if(input.id=="time_start")
        {
            time_start=input.value;
        }

        //Поле ввода даты окончания бронирования
        if(input.id=="date_end")
        {
            date_end=input.value;
        }

        //Поле ввода времени окончания бронирования
        if(input.id=="time_end")
        {
            time_end=input.value;
        }
    }

    for (let i = 0; i < selects.length; i++) 
    {
        let select=selects[i];

        //Поле ввода ТС
        if(select.id=="transport_id")
        {
            transport_id=select.value;
        }
    }

    var result_price_span=document.getElementById("result_price_span");
    result_price=result_price_span.textContent;

    //Проверки формы
    let error_message=document.getElementById("error_message_rent_parking_place");

    //Проверка пустой формы
    if(transport_number==="" && transport_id==="")
    {
        error_message.innerHTML="Заполните данные ТС";
        return(false);
    }
    if(time_start==="" || time_end==="")
    {
        error_message.innerHTML="Заполните время начала и окончания бронирования";
        return(false);
    }
    if(result_price==="")
    {
        error_message.innerHTML="Дата и время окончания бронирования должны быть больше даты и времени начала бронирования";
        return(false);
    }

    //Отправка данных формы
    var data = {
        rent: "true",
        action: action,
        transport_number: transport_number,
        transport_id: transport_id,
        date_start: date_start,
        time_start: time_start,
        date_end: date_end,
        time_end: time_end,
        result_price: result_price
    };
    var data_json = JSON.stringify(data);
    requestTo(rentDataHandler,data_json,url);
}