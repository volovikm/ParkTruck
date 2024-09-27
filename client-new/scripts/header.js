
function accountButtonHandler() //Обработчик кнопки аккаунта
{
	let button=document.getElementById("account_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

		var account_block=document.getElementById("account_block");

        if(window.getComputedStyle(account_block).display=="none")
        {
            account_block.style.display="block";
        }
        else
        {
            account_block.style.display="none";
        }
        
    });
}


function searchButtonHandler() //Обработчик кнопки поиска
{
	let button=document.getElementById("search_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

		console.log(1);
        
    });
}
;

function filterButtonHandler() //Обработчик кнопки фильтра
{
	let button=document.getElementById("filter_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

		console.log(1);
        
    });
}

function logoutButtonHandler() //Обработчик кнопки выхода из аккаунта
{
	let button=document.getElementById("logout_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

        redirectTo('account/logout.php');
        
    });
}

accountButtonHandler();
searchButtonHandler();
filterButtonHandler();
logoutButtonHandler();