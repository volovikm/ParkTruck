
function setAdressFromCookie(action) //Функция определния адреса выбранной парковки
{
    if(action=="create_new")
    {
        let adress=readCookie("selection_marker_adress");
        let adress_line=document.getElementById("adress_line");
        adress_line.innerHTML=adress;

        let adress_input=document.getElementById("adress");
        adress_input.value=adress;
    }
}

function saveParkingPlacesDataToCookie() //Функция сохранения в куки данных парковочных мест
{
    var list_data_json=readCookie("list_data");
    var list_data = JSON.parse(list_data_json);
    var list_array=objectToArray(list_data);
    var clear_list_array=list_array["clear_data"];
    var parking_places_data = JSON.stringify(clear_list_array); //Вывод на отправку на сервер
    writeCookie("parking_places_data", parking_places_data, 30); 
}
saveParkingPlacesDataToCookie();


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

function addParkingPlaceButtonHandler() //Обработчик кнопки добавления нового парковочного места
{
    let add_parking_place_button=document.getElementById("add_parking_place_button");
    if(add_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    add_parking_place_button.addEventListener("click", (event) => {

        let parking_place_form=document.getElementById("parking_place_form");
        parking_place_form.style.display="block";

        //Обнуление input формы
        let inputs = parking_place_form.querySelectorAll('input');
        for (let i = 0; i < inputs.length; i++) 
        {inputs[i].value="";}

        //Изменение onclick кнопки сохранить парковочное место
        var save_parking_place_button=document.getElementById("save_parking_place_button");
        save_parking_place_button.setAttribute("onclick","parkingPlaceFormHandler(`create_new`)");
    });
}
addParkingPlaceButtonHandler();

function editButtonHandler(parking_id) //Обработчик кнопки редактирования
{
    let edit_button=document.getElementById("edit_button");
    if(edit_button===null)
    {return(false);}

    //click listener на кнопку
    edit_button.addEventListener("click", (event) => {

        redirectTo('parking_card.php?edit=true&parking_id='+parking_id);
    });
}

function cancelEditButtonHandler(parking_id) //Обработчик кнопки отмены редактирования
{
    let cancel_edit_button=document.getElementById("cancel_edit_button");
    if(cancel_edit_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_edit_button.addEventListener("click", (event) => {

        redirectTo('parking_card.php?parking_id='+parking_id);
    });
}

function copyParkingPlaceButtonHandler() //Обработчик кнопки копирования парковочного места
{
    let copy_parking_place_button=document.getElementById("copy_parking_place_button");
    if(copy_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    copy_parking_place_button.addEventListener("click", (event) => {

        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);
    
        var parking_place_id=choice_arr[0];

        //Массив отображения
        var list_data_json=readCookie("list_data");
        var list_data = JSON.parse(list_data_json);
        var list_array=objectToArray(list_data);

        //Массив отправки на сервер
        var list_server_data_json=readCookie("parking_places_data");
        var list_server_data = JSON.parse(list_server_data_json);
        var list_server_array=objectToArray(list_server_data);

        var existing_par=false;
        if(list_server_array[parking_place_id]===undefined) //Разрешение конфликта id существующих мест и новых
        {
            for (let i = 0; i < list_server_array.length; i++) 
            {
                if(list_server_array[i]["id"]==parking_place_id)
                {
                    parking_place_id=i;
                    existing_par=true;
                    break;
                }
            }
        }

        //Изменение id для копирования существующих записей
        if(existing_par)
        {
            //Для массива отображения
            var parking_place_server_array=objectToArray(parking_place_server_data);
            parking_place_server_array["id"]=list_server_array.length+1;
            parking_place_server_data=arrayToObject(parking_place_server_array);

            //Для массива отправки
            var parking_place_server_array=objectToArray(parking_place_server_data);
            parking_place_server_array["id"]=list_server_array.length+1;
            parking_place_server_data=arrayToObject(parking_place_server_array);
        }

        //Добавление парковочного места в массив отображения
        var parking_place_data=list_array[parking_place_id];
        var parking_place_array=objectToArray(parking_place_data);
        parking_place_array["rent"]="";
        if(existing_par)
        {
            parking_place_array["id"]=list_array.length+1;
            parking_place_data=arrayToObject(parking_place_array);
        }
        list_array.push(parking_place_data);
        listDisplay(list_array);

        //Добавление парковочного места в массив отправки на сервер
        var parking_place_server_data=list_server_array[parking_place_id];
        if(existing_par)
        {
            var parking_place_server_array=objectToArray(parking_place_server_data);
            parking_place_server_array["id"]=list_server_array.length+1;
            parking_place_server_data=arrayToObject(parking_place_server_array);
        }
        list_server_array.push(parking_place_server_data);
        var parking_places_server_data = JSON.stringify(list_server_array);
        writeCookie("parking_places_data", parking_places_server_data, 30);
    });
}
copyParkingPlaceButtonHandler();

function editParkingPlaceButtonHandler() //Обработчик кнопки редактирования парковочного места
{
    let edit_parking_place_button=document.getElementById("edit_parking_place_button");
    if(edit_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    edit_parking_place_button.addEventListener("click", (event) => {

        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);

        var parking_place_id=choice_arr[0];

        //Массив отображения
        var list_data_json=readCookie("list_data");
        var list_data = JSON.parse(list_data_json);
        var list_array=objectToArray(list_data);

        //Массив отправки
        var list_server_data_json=readCookie("parking_places_data");
        var list_server_data = JSON.parse(list_server_data_json);
        var list_server_array=objectToArray(list_server_data);

        //Получение данных выбранного парковочного места
        var parking_place_data=list_array[parking_place_id];
        var parking_place_server_data=list_server_array[parking_place_id];
        if(parking_place_server_data===undefined) //Разрешение конфликта id существующих мест и новых
        {
            for (let i = 0; i < list_server_array.length; i++) 
            {
                if(list_server_array[i]["id"]==parking_place_id)
                {parking_place_server_data=list_server_array[i];}
            }
        }

        //Вызов формы парковочного места
        var parking_place_form=document.getElementById("parking_place_form");
        var save_parking_place_button=document.getElementById("save_parking_place_button");
        save_parking_place_button.setAttribute("onclick","parkingPlaceFormHandler(`edit`,"+parking_place_id+")");
        parking_place_form.style.display="block";
        
        //Заполнение формы парковочного места
        let inputs = parking_place_form.querySelectorAll('input');
        let selects = parking_place_form.querySelectorAll('select');
        for (let i = 0; i < inputs.length; i++) 
        {
            let input=inputs[i];

            //Поля ввода
            input.value=parking_place_server_data[input.id];

            //Чекбокс неограниченной высоты
            if(input.id=="height_not_limited")
            {
                input.checked=parking_place_server_data["height_not_limited"];
            }
        }
        for (let i = 0; i < selects.length; i++) 
        {
            let select=selects[i];
        
            select.value=parking_place_server_data[select.id];
        }
    });
}
editParkingPlaceButtonHandler();

function deleteParkingPlaceButtonHandler() //Обработчик кнопки удаления парковочного места
{
    let delete_parking_place_button=document.getElementById("delete_parking_place_button");
    if(delete_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    delete_parking_place_button.addEventListener("click", (event) => {

        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);

        var parking_place_id="";
        var list_data="";
        var list_array="";
        var list_server_data="";
        var list_server_array="";
        var parking_places_server_data="";

        //Массив отображения
        var list_data_json=readCookie("list_data");
        list_data = JSON.parse(list_data_json);
        list_array=objectToArray(list_data);

        //Массив отправки
        var list_server_data_json=readCookie("parking_places_data");
        list_server_data = JSON.parse(list_server_data_json);
        list_server_array=objectToArray(list_server_data);

        //Определение удаляемых элементов
        for(let i=0;i<choice_arr.length;i++)
        {
            parking_place_id=choice_arr[i];

            //Определение id существующих мест
            if(list_array[parking_place_id]===undefined)
            {
                for (let i = 0; i < list_array.length; i++) 
                {
                    if(list_array[i]["id"]==parking_place_id)
                    {parking_place_id=i;}
                }
            }
    
            //Удаление парковочного места из массива  отображения
            list_array.splice(parking_place_id, 1,"removed");
            
            //Удаление парковочного места из массива отправки на сервер
            list_server_array.splice(parking_place_id, 1,"removed");
        }

        //Удаление всех элементов по маркеру "removed"
        while(list_array.indexOf("removed")!=-1)
        {
            list_array.splice(list_array.indexOf("removed"), 1);
        }
        while(list_server_array.indexOf("removed")!=-1)
        {
            list_server_array.splice(list_server_array.indexOf("removed"), 1);
        }

        listDisplay(list_array); //Вывод на отображение 
        parking_places_server_data = JSON.stringify(list_server_array); //Вывод на отправку 
        writeCookie("parking_places_data", parking_places_server_data, 30); 
    });
}
deleteParkingPlaceButtonHandler();

function rentParkingPlaceButtonHandler() //Обработчик кнопки бронирования парковочного места
{
    let rent_parking_place_button=document.getElementById("rent_parking_place_button");
    if(rent_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    rent_parking_place_button.addEventListener("click", (event) => {

        var choice_input = document.getElementById("choice_input");
        var choice_arr=choice_input.value.split(["_"]);
        choice_arr.splice(0, 1);

        let error_message=document.getElementById("error_message");

        //Проверка ошибок
        error_message.innerHTML="";
        if(choice_arr.length!=1)
        {
            error_message.innerHTML="Выберите одно парковочное место";
            return(false);
        }

        //Определение записи о парковочном месте
        var parking_place_id="";
        parking_place_id=choice_arr[0];
        var parking_places_json=readCookie("parking_places_data");
        var parking_places_data = JSON.parse(parking_places_json);
        var parking_places_array=objectToArray(parking_places_data);
        var parking_place_array=[];
        for(let i=0;i<parking_places_array.length;i++)
        {
            if(parking_places_array[i]["id"]==parking_place_id)
            {
                parking_place_array=parking_places_array[i];
            }
        }

        //Вызов формы бронирования
        var parking_place_rent_form=document.getElementById("parking_place_rent_form");
        parking_place_rent_form.style.display="block";

        var parking_place_name_span=document.getElementById("parking_place_name_span");
        parking_place_name_span.innerHTML="";
        parking_place_name_span.innerText=parking_place_array['parking_place_name'];

        var price_span=document.getElementById("price_span");
        var price_units_span=document.getElementById("price_units_span");
        if(parking_place_array['price_units']=="hours")
        {var price_units="руб/час";}
        if(parking_place_array['price_units']=="days")
        {var price_units="руб/сутки";}
        price_span.innerHTML="";
        price_units_span.innerHTML="";
        price_span.innerText=parking_place_array['price']+" "+price_units;
        price_units_span.innerText=parking_place_array['price_units'];

        var result_price_span=document.getElementById("result_price_span");
        result_price_span.innerHTML="";

        var time_start_input=document.getElementById("time_start");
        var time_end_input=document.getElementById("time_end");
        time_start_input.value="";
        time_end_input.value="";

        var save_parking_place_rent_button=document.getElementById("save_parking_place_rent_button");
        save_parking_place_rent_button.setAttribute('onclick','parkingPlaceRentFormHandler(`'+parking_place_id+'`)');
    });
}
rentParkingPlaceButtonHandler();

function deleteParkingButtonHandler(parking_id) //Обработчик кнопки удаления парковки
{
    let delete_parking_button=document.getElementById("delete_parking_button");
    if(delete_parking_button===null)
    {return(false);}

    //click listener на кнопку
    delete_parking_button.addEventListener("click", (event) => {

        var script="deleteParkingFunction(`"+parking_id+"`);";

        ConfirmDelete(script);
    });
}
function deleteParkingFunction(parking_id) //Функция отправки запроса на удаление парковки
{
    parkingCardFormHandler("delete",false,parking_id);
}


//Функции формы бронирования парковочного места
function SetDateStart() //Функции определения дат бронирования
{
	var date_start_input=document.getElementById("date_start");

    if(date_start_input===null)
    {return(false);}

    var today=new Date();
    
    //Год клиента
    var today_year=today.getFullYear();
    
    //Месяц клиента
    var today_month=today.getMonth();
    if (today.getMonth()+1 < 10) {today_month='0' + (today.getMonth()+1);}
       
    //День клиента      
    var today_day=today.getDate();
    if (today.getDate()+1 < 10) {today_day='0' + today.getDate();}
    
    //Полная дата
    var today_date=today_year+'-'+today_month+'-'+today_day;
    date_start_input.valueAsDate = new Date(today_date);
    
    //Минимальная дата начала бронирования - сегодня
    date_start_input.setAttribute('min',today_date);
}

function SetDateEnd()
{
	var date_start_input=document.getElementById("date_start");
	var date_end_input=document.getElementById("date_end");

    if(date_start_input===null || date_end_input===null)
    {return(false);}

    var today=new Date();
    
    //Год клиента
    var today_year=today.getFullYear();
    
    //Месяц клиента
    var today_month=today.getMonth();
    if (today.getMonth()+1 < 10) {today_month='0' + (today.getMonth()+1);}
       
    //День клиента      
    var today_day=today.getDate();
    if (today.getDate()+1 < 10) {today_day='0' + today.getDate();}
    
    //Полная дата
    var today_date=today_year+'-'+today_month+'-'+today_day;
    date_end_input.valueAsDate = new Date(today_date);
    
    date_start_input.addEventListener('change', () => {
        date_end_input.setAttribute('min',date_start_input.value);
        
        var max_date = new Date(date_start_input.value);
        max_date.setDate(max_date.getDate() + 30);
        var max_period=max_date.getFullYear()+"-"+(max_date.getMonth()+1)+"-"+max_date.getDate();
        var day=max_date.getDate().toString().padStart(2, "0");
        var month=(max_date.getMonth()+1).toString().padStart(2, "0");
        var year=max_date.getFullYear();
        max_period=year+"-"+month+"-"+day;
        date_end_input.setAttribute('max',max_period); 
    });
}
SetDateStart();
SetDateEnd();

function defineResultPrice() //Функция определения итоговой стоимости бронирования
{
    var result_price_span=document.getElementById("result_price_span");

    var date_start_input=document.getElementById("date_start");
    var date_end_input=document.getElementById("date_end");
    var time_start_input=document.getElementById("time_start");
    var time_end_input=document.getElementById("time_end");

    if(date_start_input===null || date_end_input===null)
    {return(false);}

    date_start_input.addEventListener('change', () => {
        countResultPrice(date_start_input,date_end_input,time_start_input,time_end_input,result_price_span);
    });

    date_end_input.addEventListener('change', () => {
        countResultPrice(date_start_input,date_end_input,time_start_input,time_end_input,result_price_span);
    });

    time_start_input.addEventListener('change', () => {
        countResultPrice(date_start_input,date_end_input,time_start_input,time_end_input,result_price_span);
    });

    time_end_input.addEventListener('change', () => {
        countResultPrice(date_start_input,date_end_input,time_start_input,time_end_input,result_price_span);
    });

    function countResultPrice(date_start_input,date_end_input,time_start_input,time_end_input,result_price_span) //Функция счёта итоговой стоимости по дням
    {
        var price_span=document.getElementById("price_span");
        var price_units_span=document.getElementById("price_units_span");

        var price=price_span.textContent;
        var price_units=price_units_span.textContent;

        var date_start = date_start_input.value;
        var date_end = date_end_input.value;
        var time_start = time_start_input.value;
        var time_end = time_end_input.value;

        if(price_units=="days")
        {
            var days_diff=moment(date_end+" "+time_end).diff(moment(date_start+" "+time_start), 'days');
            result_price=days_diff * parseInt(price);
        }
        if(price_units=="hours")
        {
            var hours_diff=moment(date_end+" "+time_end).diff(moment(date_start+" "+time_start), 'hours');
            result_price=hours_diff * parseInt(price);
        }

        if(result_price>0)
        {
            result_price_span.innerText=result_price+" руб";
        }
        else
        {
            result_price_span.innerText="";
        }
    }
}
defineResultPrice();


//Обработчики кнопок формы парковочного места
function cancelParkingPlaceButtonHandler() //Обработчик кнопки выхода из парковочного места
{
    let cancel_parking_place_button=document.getElementById("cancel_parking_place_button");
    if(cancel_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_parking_place_button.addEventListener("click", (event) => {
    
        let parking_place_form=document.getElementById("parking_place_form");
        parking_place_form.style.display="none";
    });
}
cancelParkingPlaceButtonHandler();

function cancelParkingPlaceRentButtonHandler() //Обработчик кнопки выхода из формы бронирования парковочного места
{
    let cancel_parking_place_rent_button=document.getElementById("cancel_parking_place_rent_button");
    if(cancel_parking_place_rent_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_parking_place_rent_button.addEventListener("click", (event) => {
    
        let parking_place_rent_form=document.getElementById("parking_place_rent_form");
        parking_place_rent_form.style.display="none";
    });
}
cancelParkingPlaceRentButtonHandler();


//Функция сброса данных парковочных мест в куки
function dropParkingPlacesData()
{
    deleteCookie("parking_places_data");
}


//Обработчик ответов сервера
function parkingCardDataHandler(parking_card_data_json)
{
    parking_card_data_json=parking_card_data_json.replace("/", '');
    let parking_card_data = JSON.parse(parking_card_data_json);
    parking_card_data = JSON.parse(parking_card_data);
    let response=parking_card_data['response'];
    let error_message=document.getElementById("error_message");

    //Координаты не по формату
    if(response==="invalid_coordinates")
    {
        error_message.innerHTML="Введите координаты или выберите точку на карте";
        return(false);
    }

    //Парковка с данными координатами существует
    if(response==="parking_coordinates_exist")
    {
        error_message.innerHTML="Парковка с данными координатами уже существует";
        return(false);
    }

    //Ошибка сервера
    if(response==="request_error")
    {
        error_message.innerHTML="Ошибка сервера";
        return(false);
    }

    //Отсутствует хотя бы одно парковочное место
    if(response==="no_parking_places")
    {
        error_message.innerHTML="Добавьте хотя бы одно парковочное место";
        return(false);
    }

    //Неверные данные парковочных мест
    if(response==="invalid_parking_places")
    {
        error_message.innerHTML="Неверные данные парковочных мест";
        return(false);
    }

    //Успешное добавление карточки
    if(response==="parking_card_add_complete")
    {
        window.location.href="../index.php";
    }

    //Успешное добавление карточки (с черновиком)
    if(response==="parking_card_add_draft_complete")
    {
        window.location.reload();
    }

    //Успешное редактирование карточки
    if(response==="parking_card_edit_complete")
    {
        let params = (new URL(document.location)).searchParams; 
        var parking_id=params.get("parking_id");
        window.location.href="../client/parking_card.php?parking_id="+parking_id;
    }

    //Успешное удаление парковки
    if(response==="delete_complete")
    {
        window.location.reload();
    }
}

function rentDataHandler(rent_data_json)
{
    rent_data_json=rent_data_json.replace("/", '');
    let rent_data = JSON.parse(rent_data_json);
    rent_data = JSON.parse(rent_data);
    let response=rent_data['response'];
    let error_message=document.getElementById("error_message_rent_parking_place");
}