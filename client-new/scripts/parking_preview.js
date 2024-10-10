function closePreview()
{
    var parking_preview_div=document.getElementById("parking_preview_div");
    parking_preview_div.style.display="none";

}

function openParkingCardButtonHandler() //Обработчик кнопки открытия карточки парковки
{
    let button=document.getElementById("open_parking_card_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

        console.log("open_parking_card");

        var parking_card_div=document.getElementById("parking_card_div");
        var parking_card_frame=document.getElementById("parking_card_frame");
        var parking_id_input=document.getElementById("parking_id");

        parking_card_div.style.display="block";
        var parking_id=parking_id_input.value;
        parking_card_frame.setAttribute("src","parking_card.php?parking_id="+parking_id);

        closePreview();
        
    });
}
openParkingCardButtonHandler();

function closePreviewButtonHandler() //Обработчик кнопки закрытия превью парковки
{
    let button=document.getElementById("close_preview_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

        console.log("close_preview");
        closePreview();

    });
}
closePreviewButtonHandler();

function rentParkingButtonHandler() //Обработчик кнопки бронирования парковки
{
    let button=document.getElementById("rent_parking_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

        var parking_card_div=document.getElementById("parking_card_div");
        var parking_card_frame=document.getElementById("parking_card_frame");
        var parking_id_input=document.getElementById("parking_id");

        parking_card_div.style.display="block";
        var parking_id=parking_id_input.value;
        parking_card_frame.setAttribute("src","parking_place_rent_form.php?parking_id="+parking_id);

        closePreview();
    });
}
rentParkingButtonHandler();