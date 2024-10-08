
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
        console.log("cancel");
        redirectTo('map.php');
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
{parkingCardFormHandler("delete",false,parking_id);}


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


//Режим бронирования
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

        //Обнуление выбора
        dropChoice();

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
        var parking_places_json=localStorage.getItem("parking_places_data");
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
        rentFormCall(parking_place_id,parking_place_array);
    });
}
rentParkingPlaceButtonHandler();

function rentFormCall(parking_place_id,parking_place_array) //Функция вызова и обнуления формы бронирования 
{
    var parking_place_rent_form=document.getElementById("parking_place_rent_form");
    parking_place_rent_form.style.display="block";

    var parking_place_name_span=document.getElementById("parking_place_name_span");
    parking_place_name_span.innerHTML="";
    parking_place_name_span.innerText=parking_place_array['parking_place_name'];

    var price_days_span=document.getElementById("price_days_span");
    var price_hours_span=document.getElementById("price_hours_span");
    price_days_span.innerHTML="";
    price_days_span.innerText=parking_place_array['price_days'];
    price_hours_span.innerHTML=""; 
    price_hours_span.innerText=parking_place_array['price_hours'];

    var result_price_span=document.getElementById("result_price_span");
    result_price_span.innerHTML="";

    var time_start_input=document.getElementById("time_start");
    var time_end_input=document.getElementById("time_end");
    time_start_input.value="";
    time_end_input.value="";

    var save_parking_place_rent_button=document.getElementById("save_parking_place_rent_button");
    save_parking_place_rent_button.setAttribute('onclick','parkingPlaceRentFormHandler(`'+parking_place_id+'`)');

    var transport_number_input=document.getElementById("transport_number");
    transport_number_input.value="";
}


//Режим бронирования - функции формы бронирования парковочного места
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
        max_date.setDate(max_date.getDate() + 7);
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
        var price_days_span=document.getElementById("price_days_span");
        var price_hours_span=document.getElementById("price_hours_span");

        var result_price_value=document.getElementById("result_price_value");

        var price_days=price_days_span.textContent;
        var price_hours=price_hours_span.textContent;

        var date_start = date_start_input.value;
        var date_end = date_end_input.value;
        var time_start = time_start_input.value;
        var time_end = time_end_input.value;

        //Расчёт в часах
        var hours_diff=moment(date_end+" "+time_end).diff(moment(date_start+" "+time_start), 'hours');

        if(hours_diff<24) //Расчёт по часовому тарифу
        {
            result_price=hours_diff * parseInt(price_hours);
        }
        else //Расчёт по суточному тарифу
        {
            result_price=parseInt((hours_diff/24)) * parseInt(price_days);
        }

        if(result_price>0)
        {
            result_price_span.innerText=result_price+" руб";
            result_price_value.innerText=result_price;
        }
        else
        {
            result_price_span.innerText="";
            result_price_value.innerText="";
        }
    }
}
defineResultPrice();

function rentInfoModalWindow(rent_data) //Функция вызова модального окна с номером (информацией) брони
{

    function ModalDisplay(rent_data)
    {
        var modal_window_div = document.createElement("div");
        modal_window_div.id="modal_window_div";
        modal_window_div.innerHTML="\
        <div class='modal_window_div interface_block'>\
            <h3 class='modal_window_h3'>Бронирование №: "+rent_data["rent_number"]+"</h3>\
            <div class='modal_window_info_div'>\
                Срок бронирования: "+rent_data["rent_start_date"]+" "+rent_data["rent_start_time"]+" - "+rent_data["rent_end_date"]+" "+rent_data["rent_end_time"]+"   \
            </div>\
            <div class='modal_window_info_div'>\
                Госномер ТС: "+rent_data["transport_number"]+"   \
            </div>\
            <div class='modal_window_info_div'>\
                Итоговая стоимость: "+rent_data["result_price"]+"   \
            </div>\
            <div class='modal_window_buttons_block'>\
                <button class='main_button modal_window_button' onclick='createRouteByParkingId(`"+rent_data["parking_id"]+"`)'>Построить маршрут</button>\
                <button class='secondary_button modal_window_button' onclick='endRentButtonHandler()'>Завершить</button>\
            </div>\
        </div>\
        ";
        
        //Выводим модальное окно
        var body = document.querySelector("body");
        body.appendChild(modal_window_div);
    }
    
    ModalDisplay(rent_data);
}

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

function endRentButtonHandler() //Обработчик кнопки завершения бронирования в форме бронирования парковочного места
{
    document.getElementById(`modal_window_div`).remove()
    location.reload();
}

function createRouteByParkingId(parking_id) //Обработчик кнопки построения маршрута в форме бронирования парковочного места
{
    location.reload();
}



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
        var month=date.getMonth();
        if (date.getMonth()+1 < 10) {month='0' + (date.getMonth()+1);}
       
        //День      
        var day=date.getDate();
        if (date.getDate()+1 < 10) {day='0' + date.getDate();}

        //Год      
        var year=date.getFullYear();

        date=day+"."+month;
        date_block.innerHTML=date;

        date=year+"-"+month+"-"+day;

        //Указание времени начала и конца дня на шкале
        time_day_beggining_span = document.createElement("span");
        time_day_beggining_span.classList.add("timeline_span");
        time_day_beggining_span.innerHTML="00:00";
        timeline_div.append(time_day_beggining_span);

        time_day_ending_span = document.createElement("span");
        time_day_ending_span.classList.add("timeline_span");
        time_day_ending_span.style.left=parseInt(window.getComputedStyle(timeline_div).getPropertyValue("width")) - 175;
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

    //Успешное удаление бронирования
    if(response==="stop_rent_complete")
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

    //Ошибка сервера
    if(response==="request_error")
    {
        error_message.innerHTML="Ошибка сервера";
        return(false);
    }

    //Неверные данные бронирования
    if(response==="invalid_rent_data")
    {
        error_message.innerHTML="Неверные данные бронирования";
        return(false);
    }

    //Указанное время уже занято
    if(response==="time_already_rent")
    {
        error_message.innerHTML="Указанное время уже занято";
        return(false);
    }

    //Успешное бронирование места
    if(response==="rent_complete")
    {
        let response_content=rent_data['response_content'];

        let rent_number=response_content["rent_number"];

        var parking_place_rent_form=document.getElementById("parking_place_rent_form");
        parking_place_rent_form.style.display="none";

        rentInfoModalWindow(response_content);


        //window.location.reload();
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