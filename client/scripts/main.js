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
}

//Функция чтения куки
function readCookie(name) {
	var matches = document.cookie.match(new RegExp(
	  "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	));
	return matches ? decodeURIComponent(matches[1]) : undefined;
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