
function setAdressFromCookie(action) //Функция определения адреса выбранной парковки
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

function saveParkingPlacesData() //Функция сохранения данных парковочных мест в localstorage
{
    const intervalId =setInterval(() => {

        if(localStorage.getItem("list_data")!==undefined)
        {

            var list_data_json=localStorage.getItem("list_data");
            var list_data = JSON.parse(list_data_json);
            var list_array=objectToArray(list_data);
            var clear_list_array=list_array["clear_data"];
            var parking_places_data = JSON.stringify(clear_list_array); //Вывод на отправку на сервер

            localStorage.setItem("parking_places_data",parking_places_data);

            clearInterval(intervalId);
        }
    }, 1000);

}
saveParkingPlacesData();

function dropParkingPlacesData() //Функция сброса данных парковочных мест в localstorage
{
    localStorage.removeItem("list_data")
    localStorage.removeItem("parking_places_data")
}

//Обработчики кнопок сайдбара

//Для всех режимов
function cancelButtonHandler() //Обработчик кнопки возврата на главную
{
    let cancel_button=document.getElementById("cancel_button");
    if(cancel_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_button.addEventListener("click", (event) => {

        var close_parking_card_button=window.parent.document.getElementById("close_parking_card_button");
        close_parking_card_button.click();
        
    });
}
cancelButtonHandler();


//Режим создания новой
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


//Режим редактирования - действия по парковке
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


//Режим редактирования - действия по парковочным местам
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

        //Обнуление выбора
        dropChoice();
    
        var parking_place_id=choice_arr[0];

        //Массив отображения
        var list_data_json=localStorage.getItem("list_data");
        var list_data = JSON.parse(list_data_json);
        var list_array=objectToArray(list_data);

        //Массив отправки на сервер
        var list_server_data_json=localStorage.getItem("parking_places_data");
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
        parking_place_array["parking_place_name"]=""; //Обнуление имени парковочного места для уникальности
        parking_place_array["status"]=""; //Обнуление статуса
        if(existing_par){parking_place_array["id"]=list_array.length+1;} //Указание id места, скопированного с нового
        parking_place_data=arrayToObject(parking_place_array);
        list_array.push(parking_place_data);
        listDisplay(list_array);

        //Добавление парковочного места в массив отправки на сервер
        var parking_place_server_data=list_server_array[parking_place_id];
        var parking_place_server_array=objectToArray(parking_place_server_data);
        parking_place_server_array["parking_place_name"]=""; //Обнуление имени парковочного места для уникальности
        if(existing_par){parking_place_server_array["id"]=list_server_array.length+1;} //Указание id места, скопированного с нового
        parking_place_server_data=arrayToObject(parking_place_server_array);
        list_server_array.push(parking_place_server_data);
        var parking_places_server_data = JSON.stringify(list_server_array);
        localStorage.setItem("parking_places_data",parking_places_server_data);
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

        //Обнуление выбора
        dropChoice();

        var parking_place_id=choice_arr[0];

        //Массив отображения
        var list_data_json=localStorage.getItem("list_data");
        var list_data = JSON.parse(list_data_json);
        var list_array=objectToArray(list_data);

        //Массив отправки
        var list_server_data_json=localStorage.getItem("parking_places_data");
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

        //Обнуление выбора
        dropChoice();

        var parking_place_id="";
        var list_data="";
        var list_array="";
        var list_server_data="";
        var list_server_array="";
        var parking_places_server_data="";

        //Массив отображения
        var list_data_json=localStorage.getItem("list_data");
        list_data = JSON.parse(list_data_json);
        list_array=objectToArray(list_data);

        //Массив отправки
        var list_server_data_json=localStorage.getItem("parking_places_data");
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
        localStorage.setItem("parking_places_data",parking_places_server_data);
    });
}
deleteParkingPlaceButtonHandler();


//Режим редактирования - редактирование парковочных мест
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



//Функции формы визуализации интервалов
function cancelParkingPlaceIntervalsButtonHandler() //Обработчик кнопки выхода из формы визуализации интервалов
{
    let cancel_parking_place_intervals_button=document.getElementById("cancel_parking_place_intervals_button");
    if(cancel_parking_place_intervals_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_parking_place_intervals_button.addEventListener("click", (event) => {
    
        let parking_place_intervals_form=document.getElementById("parking_place_intervals_form");
        parking_place_intervals_form.style.display="none";
    });
}
cancelParkingPlaceIntervalsButtonHandler();

function intervalsFormCall() //Функция вызова формы визуализации интервалов
{
    var parking_place_intervals_form=document.getElementById("parking_place_intervals_form");
    
    //observer за изменением атрибута
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {

            var parking_place_id=parking_place_intervals_form.getAttribute("modal_window_info");
            var date_from=document.getElementById("date_from").value;
            getIntervalsData(parking_place_id,date_from);

        });    
    });
    var config = { 
        attributes: true, 
        attributeFilter: ['modal_window_info'],
        childList: false, 
        characterData: false 
    };
    observer.observe(parking_place_intervals_form, config);
}
intervalsFormCall();

function intervalInputChangeHandler() //Функция обновления интервалов по изменению даты в поле ввода
{
    var date_from_input=document.getElementById("date_from");
    var parking_place_intervals_form=document.getElementById("parking_place_intervals_form");
    
    //change listener на кнопку
    date_from_input.addEventListener("change", (event) => {

        var parking_place_id=parking_place_intervals_form.getAttribute("modal_window_info");
        var date_from=date_from_input.value;
        getIntervalsData(parking_place_id,date_from);
        
    });
}
intervalInputChangeHandler();

function getIntervalsData(parking_place_id,date_from) //Функция запроса данных об интервалах бронирования
{
    let url="../request_handler.php";

    var data = {
        get_rent_intervals: true,
        parking_place_id: parking_place_id,
        date_from: date_from,
    };
    var data_json = JSON.stringify(data);
    requestTo(intervalsDataHandler,data_json,url);
} 

function intervalsDataHandler(intervals_data_json) //Обработчик визуализации интервалов 
{
    intervals_data_json=intervals_data_json.replace("/", '');
    let intervals_data = JSON.parse(intervals_data_json);
    intervals_data = JSON.parse(intervals_data);
    var intervals_array=objectToArray(intervals_data);

    let response_content=intervals_array['response_content'];

    //Указание названия парковочного места
    var parking_place_name_span=document.getElementById("parking_place_intervals_name_span");
    parking_place_name_span.innerHTML="";
    parking_place_name_span.innerText=response_content['parking_place_name'];

    //Распеределение дат по блокам
    for(let i=0;i<response_content['dates'].length;i++)
    {
        date_block=document.getElementById("intervals_days_column_"+i);
        interval_line=document.getElementById("intervals_display_column_"+i);
        timeline_div=document.getElementById("timeline_div_"+i);

        //Сброс формы
        interval_line.innerHTML="";
        timeline_div.innerHTML="";

        date=new Date(response_content['dates'][i]);

        //Месяц
        var month=date.getMonth()+1;
        if (month < 10) {month='0' + month;}
       
        //День      
        var day=date.getDate();
        if (day < 10) {day='0' + day;}

        //Год      
        var year=date.getFullYear();

        date=day+"."+month;
        date_block.innerHTML=date;

        date=year+"-"+month+"-"+day;

        //Указание времени начала и конца дня на шкале
        time_day_beggining_span = document.createElement("span");
        time_day_beggining_span.classList.add("timeline_span");
        time_day_beggining_span.style.left="calc(20% - 20px)";
        time_day_beggining_span.innerHTML="00:00";
        timeline_div.append(time_day_beggining_span);

        time_day_ending_span = document.createElement("span");
        time_day_ending_span.classList.add("timeline_span");
        time_day_ending_span.style.left="calc(100% - 70px)";//parseInt(window.getComputedStyle(timeline_div).getPropertyValue("width")) - 190;
        time_day_ending_span.innerHTML="23:59";
        timeline_div.append(time_day_ending_span);

        interval_line_width=parseInt(window.getComputedStyle(interval_line).getPropertyValue("width"));
        hours_units=interval_line_width / 24; //Единицы ширины на час

        for(let j=0;j<response_content['rent_intervals']["rent_times"].length;j++)
        {
            point_datetime=response_content['rent_intervals']["rent_times"][j];

            point_date=point_datetime.split(" ")[0];
            point_time=point_datetime.split(" ")[1];
            rent_id=point_datetime.split(" ")[2];

            if(date===point_date) //Определение интервала, принадлежащего данной дате
            {
                point_time_hours=point_time.split(":")[0]; //Часы начала интервала
    
                //Создание блока интервала
                interval_span = document.createElement("span");
                interval_span.classList.add("rent_interval");
                interval_span.setAttribute("datetime",point_datetime);
                interval_span.setAttribute("rent_id",rent_id);

                id="interval_span_"+i+"_"+j;
                interval_span.setAttribute("id",id);
    
                //Установка длины интервала
                interval_span.style.width=hours_units; 
    
                //Установка отступа интервала
                left=parseInt(parseInt(point_time_hours)+parseInt(1))*hours_units;

                //Отображение времени интервала по клику
                interval_span.setAttribute("onclick","intervalClickHandler('"+rent_id+"')");

                interval_span.style.left=left;
                interval_span.style.marginLeft=-hours_units;
    
                interval_line.append(interval_span);
            }
        }
    }
}

function intervalClickHandler(rent_id) //Обработчик нажатия на интервал
{
    let url="../request_handler.php";

    //Отправка данных формы
    var data = {
        get_rent_data: true,
        rent_id: rent_id,
    };
    var data_json = JSON.stringify(data);
    requestTo(intervalDataHandler,data_json,url);
}

function stopRentButtonHandler(rent_id) //Обработчик нажатия на кнопку отмены интервала
{
    let url="../request_handler.php";

    //Отправка данных формы
    var data = {
        stop_rent: true,
        rent_id: rent_id,
    };
    var data_json = JSON.stringify(data);
    requestTo(parkingCardDataHandler,data_json,url);
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
        let response_content=parking_card_data['response_content'];
        var parking_id=response_content;
        window.location.href="parking_card.php?parking_id="+parking_id;
    }

    //Успешное добавление карточки (с черновиком)
    if(response==="parking_card_add_draft_complete")
    {
        let response_content=parking_card_data['response_content'];
        var parking_id=response_content;
        window.location.href="parking_card.php?parking_id="+parking_id;
    }

    //Успешное редактирование карточки
    if(response==="parking_card_edit_complete")
    {
        let params = (new URL(document.location)).searchParams; 
        var parking_id=params.get("parking_id");
        window.location.href="../client-new/parking_card.php?parking_id="+parking_id;
    }

    //Успешное удаление парковки
    if(response==="delete_complete")
    {
        top.location.reload();
    }

    //Успешное удаление бронирования
    if(response==="stop_rent_complete")
    {
        let response_content=parking_card_data['response_content'];
        var parking_id=response_content;
        window.location.href="parking_card.php?parking_id="+parking_id;
    }
}

function intervalDataHandler(interval_data_json)
{
    interval_data_json=interval_data_json.replace("/", '');
    let interval_data = JSON.parse(interval_data_json);
    interval_data = JSON.parse(interval_data);
    let response_content=interval_data['response_content'];
    let rent_data=response_content["rent_data"];
    
    rent_data=objectToArray(rent_data);

    var interval_div=document.getElementById("interval_div");
    var interval_time_span=document.getElementById("interval_time_span");
    var interval_rent_number_span=document.getElementById("interval_rent_number_span");
    var interval_transport_number_span=document.getElementById("interval_transport_number_span");
    var stop_rent_button=document.getElementById("stop_rent_button");

    //Заполнение формы
    interval_div.style.display="block";
    interval_time_span.innerHTML=convertDate(rent_data["rent_start_date"])+" "+rent_data["rent_start_time"]+" - "+convertDate(rent_data["rent_end_date"])+" "+rent_data["rent_end_time"];
    if(interval_rent_number_span!==null)
    {
        interval_rent_number_span.innerHTML=rent_data["rent_number"];
        interval_transport_number_span.innerHTML=rent_data["transport_number"];
        stop_rent_button.setAttribute("onclick","stopRentButtonHandler('"+rent_data["rent_id"]+"')");
    }
}