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

function addTransportButtonHandler() //Обработчик кнопки добавления нового ТС
{
    let add_transport_button=document.getElementById("add_transport_button");
    if(add_transport_button===null)
    {return(false);}

    //click listener на кнопку
    add_transport_button.addEventListener("click", (event) => {
        
        let transport_form=document.getElementById("edit_transport_form");
        transport_form.style.display="block";

        //Очистка формы
        clearForm(transport_form);

        let save_transport_button=document.getElementById("save_transport_button");
        save_transport_button.setAttribute("onclick","editTransportFormHandler(`add`)");

    });
}
addTransportButtonHandler();

function editTransportButtonHandler() //Обработчик кнопки редактирования ТС
{
    let edit_transport_button=document.getElementById("edit_button");
    if(edit_transport_button===null)
    {return(false);}

    //click listener на кнопку
    edit_transport_button.addEventListener("click", (event) => {
        
        let transport_form=document.getElementById("edit_transport_form");
        transport_form.style.display="block";

        //Очистка формы
        clearForm(transport_form);

        //Заполнение формы данными ТС
        let choice_input=document.getElementById("choice_input");

        transport_id=choice_input.value.replace("_","");

        //Запрос данных ТС
        transportDataRequest(transport_id);

        let save_transport_button=document.getElementById("save_transport_button");
        save_transport_button.setAttribute("onclick","editTransportFormHandler(`edit`)");

    });
}
editTransportButtonHandler();

function transportDataRequest(transport_id) //Функция запроса данных конкретного ТС с сервера
{
    let url="../request_handler.php";

    //Отправка данных формы
    var data = {
        get_transport_data: true,
        transport_id: transport_id,
    };
    var data_json = JSON.stringify(data);
    requestTo(transportEditDataHandler,data_json,url);
}

function deleteTransportButtonHandler() //Обработчик кнопки удаления ТС
{
    let delete_transport_button=document.getElementById("delete_button");
    if(delete_transport_button===null)
    {return(false);}

    //click listener на кнопку
    delete_transport_button.addEventListener("click", (event) => {
        
        let transport_form=document.getElementById("edit_transport_form");
        transport_form.style.display="block";

        //Заполнение формы данными ТС
        let choice_input=document.getElementById("choice_input");

        let transport_id_input=document.getElementById("transport_id");
        transport_id_input.value=choice_input.value;

        let save_transport_button=document.getElementById("save_transport_button");
        save_transport_button.setAttribute("onclick","editTransportFormHandler(`delete`)");
        save_transport_button.click();

    });
}
deleteTransportButtonHandler();


//Форма добавления/редактирования ТС
function cancelTransportButtonHandler() //Обработчик кнопки отменить в форме добавления/редактирования ТС
{
    let cancel_transport_button=document.getElementById("cancel_transport_button");
    if(cancel_transport_button===null)
    {return(false);}

    //click listener на кнопку
    cancel_transport_button.addEventListener("click", (event) => {
        
        var edit_transport_form = document.getElementById("edit_transport_form");
        edit_transport_form.style.display="none";

    });
}
cancelTransportButtonHandler();



//Обработчик ответов сервера
function transportDataHandler(transport_data_json)
{
    transport_data_json=transport_data_json.replace("/", '');
    let transport_data = JSON.parse(transport_data_json);
    transport_data = JSON.parse(transport_data);
    let response=transport_data["response"];
    let error_message=document.getElementById("error_message_transport");

    //Ошибка сервера
    if(response==="request_error")
    {
        error_message.innerHTML="Ошибка сервера";
        return(false);
    }

    //Успешное действие с ТС
    if(response==="transport_action_complete")
    {
        window.location.reload();
    }
}

function transportEditDataHandler(transport_data_json) //Обработка ответа на запрос данных конкретного ТС
{
    transport_data_json=transport_data_json.replace("/", '');
    let transport_data = JSON.parse(transport_data_json);
    transport_data = JSON.parse(transport_data);
    let response_content=transport_data["response_content"];

    transport_data=response_content["transport_data"][0];

    //Заполнение формы редактирования ТС
    let inputs = edit_transport_form.querySelectorAll('input');
    let selects = edit_transport_form.querySelectorAll('select');

    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];
    
        //id ТС
        if(input.id=="transport_id")
        {
            input.value=transport_data["id"];
        }
    
        //Поле ввода госномера
        if(input.id=="transport_number")
        {
            input.value=transport_data["transport_number"];
        }
    
        //Поле ввода названия ТС
        if(input.id=="transport_name")
        {
            input.value=transport_data["transport_name"];
        }
    
        //Чекбокс рефрежиратор
        if(input.id=="refrigerator" && transport_data["properties"].includes("refrigerator"))
        {
            input.checked=true;
        }
    
        //Чекбокс негабарит
        if(input.id=="oversized" && transport_data["properties"].includes("oversized"))
        {
            input.checked=true;
        }
    
        //Чекбокс электромобиль
        if(input.id=="electrocar" && transport_data["properties"].includes("electrocar"))
        {
            input.checked=true;
        }
    }

    for (let i = 0; i < selects.length; i++) 
    {
        let select=selects[i];
    
        //Поле ввода типового размера
        if(select.id=="size")
        {
            select.value=transport_data["transport_size"];
        }
    }

}