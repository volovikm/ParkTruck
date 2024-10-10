
function fillFiltersForm() //Функция заполнения формы фильтров ранее введёнными фильтрами
{
    var filters=localStorage.getItem("filter_value");
    filters=JSON.parse(filters);

    var filters_form = document.getElementById("filters_form");

    for(let i=0;i<filters.length;i++)
    {
        filter_id=filters[i][0];
        filter_value=filters[i][1];

        var input=document.getElementById(filter_id);
        if(input==null){continue;}

        input.checked=true;

        if(input.getAttribute("type")!="checkbox")
        {
            input.value=filter_value;
        }

        var select=document.getElementById(filter_id);
        if(select==null){continue;}

        select.value=filter_value;
    }

    
}
fillFiltersForm();


function closeFilters()
{
    var filter_menu_div=document.getElementById("filter_menu_div");
    filter_menu_div.style.display="none";
}

function closeFiltersButtonHandler() //Обработчик кнопки закрытия превью парковки
{
    let button=document.getElementById("close_filters_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

        console.log("close_filters");
        closeFilters();

    });
}
closeFiltersButtonHandler();

function clearFiltersButtonHandler() //Обработчик кнопки сброса фильтров
{
    let button=document.getElementById("clear_filters_button");
    if(button===null)
    {return(false);}

    //click listener на кнопку
    button.addEventListener("click", (event) => {

        console.log("clear_filters");
        
        var filters_form = document.getElementById("filters_form");
        clearForm(filters_form);

        

        let apply_filters_button=document.getElementById("apply_filters_button");
        apply_filters_button.click();

    });
}
clearFiltersButtonHandler();
