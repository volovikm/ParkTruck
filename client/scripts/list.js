//Запрос на данные списка
function listRequest(type,list_info)
{
    let url="../request_handler.php";

    //Отправка данных формы
    var data = {
        list: true,
        list_type: type,
        list_info: list_info,
    };
    var data_json = JSON.stringify(data);
    requestTo(listResponse,data_json,url);
}

function listResponse(list_data_json) 
{
    list_data_json=list_data_json.replace("/", '');
    let list_data = JSON.parse(list_data_json);
    listDisplay(list_data);
}

//Обработчик отображения списков
function listDisplay(list_data) 
{   
    //Сохранение массива списка в куки
    var list_data_json = JSON.stringify(list_data);
    writeCookie("list_data", list_data_json, 30);

    let list_container=document.getElementById("list_container");
    let list_row_pattern_1=document.getElementById("list_row_pattern_1");
    let list_row_pattern_2=document.getElementById("list_row_pattern_2");

    for(let i=0; i<list_data.length; i++)
    {
        
        
    }
}

