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

    });
}
addTransportButtonHandler();