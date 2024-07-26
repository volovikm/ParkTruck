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

    var list_array=objectToArray(list_data);

    let list_container=document.getElementById("list_container");
    let list_row_pattern_1=document.getElementById("list_row_pattern_1");
    let list_row_pattern_2=document.getElementById("list_row_pattern_2");

    //Определение необходимых значений для вывода
    var required_info=list_array["required_info"];

    //Формирования отображения списка
    var row="";
    var column_block="";
    var column_data="";
    for(let i=0; i<list_array.length; i++)
    {
        //Строка
        if(i % 2 === 0)
        {row=list_row_pattern_1.cloneNode(false);}
        else
        {row=list_row_pattern_2.cloneNode(false);}

        list_container.append(row);
        row.style.display="block";
        row.id="list_row_"+i;

        //Добавление чекбокса выбора в строку
        if(required_info.includes("choice_checkbox"))
        {
            column_block = document.createElement("div");
            column_block.innerHTML="choice_checkbox";
            column_block.classList="list_column_block";
            row.append(column_block);
        }

        //Столбцы одной строки
        for (var key in list_array[i]) 
        {
            column_data=list_array[i][key];

            if(required_info.includes(key))
            {
                column_block = document.createElement("div");
                column_block.innerHTML=column_data;
                column_block.classList="list_column_block";
                row.append(column_block);
            }
        }
    }
}

