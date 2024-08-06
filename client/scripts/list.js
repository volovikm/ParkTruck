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
    //Сохранение массива списка в куки
    var list_data=arrayToObject(list_array);
    var list_data_json = JSON.stringify(list_data);
    writeCookie("list_data", list_data_json, 30);

    let list_content=document.getElementById("list_content");
    let list_rows=document.getElementById("list_rows");

    var choice_input = document.getElementById("choice_input");

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
        if(Math.floor(i / 2) == (i / 2))
        {row=list_row_pattern_2.cloneNode(false);}
        else
        {row=list_row_pattern_1.cloneNode(false);}

        if(i==0)
        {
            row.classList.add("list_row_first"); 
        }
        
        row.style.display="block";
        list_rows.append(row);

        console.log(row);
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
        var value="";
        var additional_info="";
        var value_style="";
        var block_choice=false;
        for(let i=0; i<list_array.length; i++)
        {
            //list_array[i][key] - значение каждой ячейки списка

            value=list_array[i][key];

            //Опредление наличия дополнительных параметров ячейки
            additional_info="";
            if(value!==undefined)
            {
                if(Object.keys(value).indexOf("content")!=-1)
                {
                    additional_info=value["additional_info"];
                    value=value["content"];
                }
            }

            column_block = document.createElement("div");
            column_block.innerHTML=value;
            column_block.classList="list_column_block";

            row_id=list_array[i]["id"];
            if(row_id===undefined){row_id=i;}

            //Добавление чекбоксов
            if(key=="choice_checkbox")
            {
                choice_checkbox=choice_checkbox_pattern.cloneNode(false);
                choice_checkbox.style.display="block";
                choice_checkbox.setAttribute("onclick", "choiceCheckbox('"+row_id+"')");
                choice_checkbox.checked=false;
                choice_checkbox.id="choice_checkbox_"+row_id;
                choice_input.value="";
                choice_input.click();
                column_block.innerHTML="";
                column_block.append(choice_checkbox);
            }

            //Применение дополнительных параметров ячейки 
            if(additional_info!=="")
            {
                //Цветовая маркировка
                if(Object.keys(additional_info).indexOf("style")!=-1)
                {
                    value_style=additional_info["style"];
                    column_block.classList.add("text_"+value_style);
                }
            }

            column.append(column_block);
        }
    }

    //console.log(list_array);
}

