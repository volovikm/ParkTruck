//Функция перенаправления на страницу по нажатия кнопки
function redirectToButton(target,url) {
    target.addEventListener(
        "click",
        function () {
            window.location.href=url;
        },
        false,
      );
}

//Функция перенаправления на страницу по нажатия кнопки
function redirectTo(url) {
	window.location.href=url;
}


//Функция показа пароля по кнопке
function show_hide_password(target,id)
{
	var input = document.getElementById(id);
		if (input.getAttribute('type') == 'password') {
			target.classList.add('view');
			input.setAttribute('type', 'text');
		} else {
			target.classList.remove('view');
			input.setAttribute('type', 'password');
		}
		return false;
}

//Функция записи куки
function writeCookie(name, val, expires) {
  var date = new Date;
  date.setDate(date.getDate() + expires);
  document.cookie = name+"="+val+"; path=/; expires=" + date.toUTCString();

  console.log("Write "+name);
}

//Функция чтения куки
function readCookie(name) {
	var matches = document.cookie.match(new RegExp(
	  "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));

	console.log("Read "+name);

	return matches ? decodeURIComponent(matches[1]) : undefined;
}

//Функция удаления куки
function deleteCookie(name) {
	var date = new Date;
	date.setDate(date.getDate() + -1);
	document.cookie = name+"="+1+"; path=/; expires=" + date.toUTCString();

	console.log("Delete "+name);
}

//Функция преобразования объекта в массив
function objectToArray(obj)
{
	arr=[];
	for (var key in obj) {
        arr[key]=obj[key];
    }

	return(arr);
}

//Функция преобразования массива в объект
function arrayToObject(arr)
{
	var obj=Object.assign({}, arr);

	return(obj);
}



//Функции списков

//Функция обработки чекбокса выбора в списке
function choiceCheckbox(id)
{
	var choice_input = document.getElementById("choice_input");
	
	var choice_value=choice_input.value;
	var choice_arr=choice_value.split(["_"]);

	if(choice_arr.indexOf(id)!=-1)
	{
		choice_input.value=choice_input.value.replace("_"+id, '');
		choice_input.click();
	}
	else
	{
		choice_input.value=choice_input.value+"_"+id;
		choice_input.click();
	}
}

//Функция сброса выбора в списке
function dropChoice()
{
	var choice_input = document.getElementById("choice_input");
	choice_input.value="";
    choice_input.click();

	var list_content=document.getElementById("list_content");
    var choice_checkbox_array = list_content.querySelectorAll("input[type='checkbox']");
	for(let i=0;i<choice_checkbox_array.length;i++)
	{
		choice_checkbox_array[i].checked=false;
	}
}

//Обработчик включения/выключения зависимых кнопок в списках
function enableListButtons(button_id,class_name,choice_amount)
{
	var choice_input=document.getElementById("choice_input");
	var button=document.getElementById(button_id);

	if(button===null)
	{return(false);}

	choice_input.addEventListener("click", () => {

		var choice_arr=choice_input.value.split(["_"]);
		choice_arr.splice(0, 1);
			
		if(choice_arr.length==choice_amount || (choice_arr.length>=1 && choice_amount==Infinity))
		{
			button.classList.add(class_name);
			button.classList.remove("disabled_button");
		}
		else
		{
			button.classList.add("disabled_button");
			button.classList.remove(class_name);
		}

	});
}

//Функция вызова окна из кнопки в списке
function showModalWindowFromList(modal_window_id,modal_window_info)
{
	var modal_window=document.getElementById(modal_window_id);
	modal_window.style.display="block";
	modal_window.setAttribute("modal_window_info",modal_window_info);
}



//Функция вызова модального окна с подтверждением удаления
function ConfirmDelete(action){

		function ModalDisplay(action)
		{
			var modal_window_div = document.createElement("div");
			modal_window_div.id="modal_window_div";
			modal_window_div.innerHTML="\
			<div class='modal_window_div interface_block'>\
				<h3 class='modal_window_h3'>Подтвердите удаление</h3>\
				<div class='modal_window_buttons_block'>\
					<button class='main_button modal_window_button' onclick='"+action+"'>Удалить</button>\
					<button class='secondary_button modal_window_button' onclick='document.getElementById(`modal_window_div`).remove()'>Отмена</button>\
				</div>\
			</div>\
			";
			
			//Выводим модальное окно
			var body = document.querySelector("body");
			body.appendChild(modal_window_div);
		}
		
		ModalDisplay(action);
}


//Функция определения текущей даты
function setTodayDate(date_input_id=null,set_today_min=null)
{
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

	if(date_input_id!==null)
	{
		var date_input=document.getElementById(date_input_id);
		date_input.valueAsDate = new Date(today_date);
	}

	if(set_today_min!==null)
	{
		var date_input=document.getElementById(date_input_id);
		date_input.setAttribute("min",today_date);
	}

	return(today_date);
}

//Функция преобразования даты по формату
function convertDate(date)
{
	date=moment(date); 
	var string = date.format("DD.MM.YYYY");

	return(string);
}

//Функция очистки формы
function clearForm(form)
{
    let inputs = form.querySelectorAll('input');

    for (let i = 0; i < inputs.length; i++) 
    {
        let input=inputs[i];
        input.value="";
		input.checked=false;
    }
}

//Функция смены видимости двух блоков
function switchVisibility(show_block_id,hide_block_id)
{
	var show_block=document.getElementById(show_block_id);
	var hide_block=document.getElementById(hide_block_id);

	show_block.style.display="block";
	hide_block.style.display="none";
}







	