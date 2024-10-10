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

    var list_container=document.getElementById("list_container");
    var list_row_pattern_2=document.getElementById("list_row_pattern_2");
    var list_row_pattern_1=document.getElementById("list_row_pattern_1");
    var choice_checkbox_pattern=document.getElementById("choice_checkbox_pattern");
    var choice_input = document.getElementById("choice_input");

    //Обнуление list_container
    list_container.innerHTML="";

    //Сохранение массива списка в localstorage
    var list_data=arrayToObject(list_array);
    var list_data_json = JSON.stringify(list_data);
    localStorage.setItem("list_data",list_data_json);

    //Определение заголовка таблицы
    var header_keys=[];
    var header_info=list_array["header"];
    header_row=list_row_pattern_1.cloneNode(false);
    header_row.style.display="block";
    for(var key in header_info)
    {
        //header_info[key] - текст блока заголовка
        //key - ключ типа заголовка

        header_keys.push(key);

        //Столбцы заголовка
        column = document.createElement("div");
        column.id="column_"+key;
        column.classList="list_column";
        column.setAttribute("header_key",key);
        column.innerHTML=header_info[key];
        
        //Столбец чекбокса
        if(key=="choice_checkbox")
        {
            column.classList.add("choice_checkbox_column");
        }

        header_row.append(column);
    }
    list_container.append(header_row);

    let i=0;
    for(var key in list_array)
    {
        //list_array[key] - содержимое строки (массив столбцов)
        //key - id строки

        if(key=="header" || key=="clear_data")
        {continue;}

        //Строки таблицы
        if(Math.floor(i / 2) == (i / 2))
        {row=list_row_pattern_2.cloneNode(false);}
        else
        {row=list_row_pattern_1.cloneNode(false);}
        row.style.display="block";

        //Столбцы таблицы
        var value="";
        var list_row=list_array[key];
        for(j in header_keys)
        {
            header_key=header_keys[j];

            //Определение наличия дополнительных параметров ячейки
            additional_info="";
            value=list_array[key][header_keys[j]];
            if(value!==undefined)
            {
                if(Object.keys(value).indexOf("content")!=-1)
                {
                    additional_info=value["additional_info"];
                    value=value["content"];
                }
            }

            column = document.createElement("div");
            column.id="column_"+key+"_"+header_key;
            column.classList="list_column";
            column.setAttribute("header_key",header_key);
            column.innerHTML=list_row[header_key];

            //Добавление чекбоксов
            if(header_key=="choice_checkbox")
            {
                choice_checkbox=choice_checkbox_pattern.cloneNode(false);
                choice_checkbox.style.display="block";
                choice_checkbox.setAttribute("onclick", "choiceCheckbox('"+key+"')");
                choice_checkbox.checked=false;
                choice_checkbox.id="choice_checkbox_"+key;
                choice_input.value="";
                choice_input.click();

                column.innerHTML="";
                column.append(choice_checkbox);
            }

            //Применение дополнительных параметров ячейки 
            if(additional_info!=="")
            {
                //Цветовая маркировка
                if(Object.keys(additional_info).indexOf("style")!=-1)
                {
                    
                    value_style=additional_info["style"];
                    column.classList.add("text_"+value_style);

                    column.innerHTML=list_row[header_key]["content"];
                }
    
                //Кнопка в форме ссылки
                if(Object.keys(additional_info).indexOf("link_button")!=-1)
                {
                    column.innerHTML="";

                    var link_button=document.createElement("button");
    
                    link_button.classList.add("link_button");
                    link_button.setAttribute("type","button");
    
                    link_button.innerText=additional_info["link_button"]["text"];
    
                    if(additional_info["link_button"]["action"]=="show_modal_window") //Открытие модального окна по кнопке
                    {
                        link_button.setAttribute("onclick","showModalWindowFromList('"+additional_info["link_button"]["action_info"]["block_id"]+"','"+additional_info["link_button"]["action_info"]["item_id"]+"')");
                    }
    
                    if(additional_info["link_button"]["action"]=="redirect") //Переход на другую страницу по кнопке
                    {
                        link_button.setAttribute("onclick","location.href='"+additional_info["link_button"]["action_info"]["link"]+"'");
                    }
                        
                    column.append(link_button);
                }
            }

            row.append(column);
        }

        list_container.append(row);
        i++;
    }

    //Установка ширина столбцов
    for(var k in header_keys)
    {
        //header_keys[k] - ключ заголовка
        var elements=document.querySelectorAll('[header_key="'+header_keys[k]+'"]')

        var width_array=[];
        for(let j=0;j<elements.length;j++)
        {
            var width=window.getComputedStyle(elements[j]).width;
            width=parseInt(width);
            width_array.push(width);
        }
        
        var max_width = Math.max(...width_array);
        for(let j=0;j<elements.length;j++)
        {
            elements[j].style.width=max_width;
        }

    }
}