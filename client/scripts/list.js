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
    var list_array=objectToArray(list_data);
    listDisplay(list_array);
}

//Обработчик отображения списков
function listDisplay(list_array) 
{   
    //console.log(list_array);

    //Сохранение массива списка в куки
    var list_data=arrayToObject(list_array);
    var list_data_json = JSON.stringify(list_data);
    writeCookie("list_data", list_data_json, 30);

    let list_content=document.getElementById("list_content");
    let list_rows=document.getElementById("list_rows");

    var column="";
    var row="";
    var column_block="";
    var column_header_block="";
    var choice_checkbox_pattern=document.getElementById("choice_checkbox_pattern");
    var choice_checkbox="";
    var row_id="";

    var header_info=list_array["header"];

    //Обнуление list_content, list_rows
    list_content.innerHTML="";
    list_rows.innerHTML="";

    //Разделение строк по цветам
    var list_row_pattern_2=document.getElementById("list_row_pattern_2");
    var list_row_pattern_1=document.getElementById("list_row_pattern_1");
    for(let i=0; i<list_array.length; i++)
    {
        if(i % 2 === 0)
        {row=list_row_pattern_1.cloneNode(false);}
        else
        {row=list_row_pattern_2.cloneNode(false);}
        row.style.display="block";
        list_rows.append(row);
    }
    if(list_array.length==0) //Заголовок для пустой таблицы
    {
        row=list_row_pattern_1.cloneNode(false);
        row.style.display="block";
        list_rows.append(row);
    }

    //Формирование отображения списка
    for(var key in header_info)
    {
        //header_info[key] - текст блока заголовка
        //key - ключ типа заголовка

        //Создание столбца
        column = document.createElement("div");
        column.id="column_"+key;
        column.classList="list_column";
        list_content.append(column);

        //Заголовок столбца
        column_header_block = document.createElement("div");
        column_header_block.innerHTML=header_info[key];
        column_header_block.classList="list_header_block list_row_1";
        column.append(column_header_block);

        //Ячейки столбцов
        for(let i=0; i<list_array.length; i++)
        {
            //list_array[i][key] - значение каждой ячейки списка

            column_block = document.createElement("div");
            column_block.innerHTML=list_array[i][key];
            column_block.classList="list_column_block";

            //Добавление чекбоксов
            if(key=="choice_checkbox")
            {
                row_id=list_array[i]["id"];
                if(row_id===undefined){row_id=i;}

                choice_checkbox=choice_checkbox_pattern.cloneNode(false);
                choice_checkbox.style.display="block";
                choice_checkbox.setAttribute("onclick", "choiceCheckbox('"+row_id+"')");
                column_block.innerHTML="";
                column_block.append(choice_checkbox);
            }

            column.append(column_block);
        }
    }
}

