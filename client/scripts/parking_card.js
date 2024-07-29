
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

        var list_data_json=readCookie("list_data");
        var list_data = JSON.parse(list_data_json);
        var list_array=objectToArray(list_data);

        //Добавление парковочного места в массив отображения
        var parking_place_data=list_array[parking_place_id];
        list_array.push(parking_place_data);
        listDisplay(list_array);

        //Добавление парковочного места в массив отправки на сервер
        var list_server_data_json=readCookie("parking_places_data");
        var list_server_data = JSON.parse(list_server_data_json);
        var list_server_array=objectToArray(list_server_data);
        var parking_place_server_data=list_server_array[parking_place_id];
        list_server_array.push(parking_place_server_data);
        var parking_places_server_data = JSON.stringify(list_server_array);
        writeCookie("parking_places_data", parking_places_server_data, 30);
    });
}
copyParkingPlaceButtonHandler();

function changeParkingPlaceButtonHandler() //Обработчик кнопки редактирования парковочного места
{
    let change_parking_place_button=document.getElementById("change_parking_place_button");
    if(change_parking_place_button===null)
    {return(false);}

    //click listener на кнопку
    change_parking_place_button.addEventListener("click", (event) => {

    });
}
changeParkingPlaceButtonHandler();

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
    
            //Удаление парковочного места из массива  отображения
            list_array.splice(parking_place_id, 1,"removed");
            
            //Удаление парковочного места из массива отправки на сервер
            list_server_array.splice(parking_place_id, 1,"removed");
        }

        //Удаление всех элементов по маркеру "removed"
        for(let i=0;i<list_array.length;i++)
        {
            if(list_array[i]==="removed")
            {
                list_array.splice(i, 1);
            }
        }
        for(let i=0;i<list_server_array.length;i++)
        {
            if(list_server_array[i]==="removed")
            {
                list_server_array.splice(i, 1);
            }
        }

        listDisplay(list_array); //Вывод на отображение 
        parking_places_server_data = JSON.stringify(list_server_array); //Вывод на отправку 
        writeCookie("parking_places_data", parking_places_server_data, 30); 
    });
}
deleteParkingPlaceButtonHandler();

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


//Функция сброса данных парковочных мест в куки
function dropParkingPlacesData()
{
    deleteCookie("parking_places_data");
}
dropParkingPlacesData();


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

    //Ошибка сервера
    if(response==="no_parking_places")
    {
        error_message.innerHTML="Добавьте хотя бы одно парковочное место";
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
}