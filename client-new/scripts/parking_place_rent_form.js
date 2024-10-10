function cancelParkingPlaceRentButtonHandler() //Обработчик кнопки выхода из формы бронирования парковочного места
{
    let button=document.getElementById("cancel_parking_place_rent_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {
    
        var close_parking_card_button=window.parent.document.getElementById("close_parking_card_button");
        close_parking_card_button.click();
    });
}
cancelParkingPlaceRentButtonHandler();

//Функция заполнения формы в зависимости от фильтров
function fillRentForm()
{
    let filter_value=localStorage.getItem("filter_value");

    let filter_input=document.getElementById("filters");

    if(filter_value!=undefined)
    {
        filter_input.value="filter_value";

        
    }
    else
    {
        SetDateStart();
        SetDateEnd();
    }
}
fillRentForm();


//Функции определения дат бронирования
function SetDateStart() 
{
	var date_start_input=document.getElementById("date_start");

    if(date_start_input===null)
    {return(false);}

    var today=new Date();
    
    //Год клиента
    var today_year=today.getFullYear();
    
    //Месяц клиента
    var today_month=today.getMonth()+1;
    if (today_month < 10) {today_month='0' + (today_month+1);}
       
    //День клиента      
    var today_day=today.getDate();
    if (today_day < 10) {today_day='0' + today_day;}
    
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
    var today_month=today.getMonth()+1;
    if (today_month < 10) {today_month='0' + (today_month+1);}
       
    //День клиента      
    var today_day=today.getDate();
    if (today_day < 10) {today_day='0' + today_day;}

    //Полная дата
    var today_date=today_year+'-'+today_month+'-'+today_day;

    date_end_input.valueAsDate = new Date(today_date);

    //Минимальная дата конца бронирования - через неделю
    var max_date = new Date(date_start_input.value);
    max_date.setDate(max_date.getDate() + 7);
    var max_period=max_date.getFullYear()+"-"+(max_date.getMonth()+1)+"-"+max_date.getDate();
    var day=max_date.getDate().toString().padStart(2, "0");
    var month=(max_date.getMonth()+1).toString().padStart(2, "0");
    var year=max_date.getFullYear();
    max_period=year+"-"+month+"-"+day;
    date_end_input.setAttribute('max',max_period); 
    
    //Пересчёт максимальной даты бронирования в зависимости от значения даты начала
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



//Обработчик ответов сервера
function findRentDataHandler(data_json)
{
    data_json=data_json.replace("/", '');
    let rent_data = JSON.parse(data_json);
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

    //Несоответствие интервала времени
    if(response==="invalid_interval")
    {
        error_message.innerHTML="Дата и время окончания бронирования должны быть позже даты и времени начала бронирования";
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

/*
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
    */

/*

*/
/*
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
*/
/*
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
    */

/*
function endRentButtonHandler() //Обработчик кнопки завершения бронирования в форме бронирования парковочного места
{
    document.getElementById(`modal_window_div`).remove()
    location.reload();
}

function createRouteByParkingId(parking_id) //Обработчик кнопки построения маршрута в форме бронирования парковочного места
{
    location.reload();
}
    */