//Запрос данных меток для карты
let filter_value=readCookie("filter_value");
if(filter_value===undefined)
{filter_value="all";}

var data = {
    get_parkings_data: 'true',
    filter: filter_value,
    screen_top_left: '',
    screen_top_right: '',
    screen_bottom_left: '',
    screen_bottom_right: '',
};
var data_json = JSON.stringify(data);
var url="../request_handler.php";
requestTo(parkingsDataHandler,data_json,url);



//Функции обработчиков запросов

function parkingsDataHandler(parkings_data_json) //Обработчик получения данных о парковках
{
    var parkings_data = JSON.parse(parkings_data_json);

    //Данные для генерации карты
    var map_data={
        'parkings_data': parkings_data,
    };
    
    createMap(map_data);
}



//Функции карты

function createMap(map_data) //Функция создания карты 
{
    //Функционал карты
    ymaps.ready(init);
    function init(){

        //Определение начального состояния карты
        let last_latitude=readCookie("last_latitude");
        let last_longitude=readCookie("last_longitude");
        let last_zoom=readCookie("last_zoom");
        let filter_value=readCookie("filter_value");

        if(last_latitude === undefined || last_longitude === undefined || last_zoom === undefined)
        {
            last_latitude=55.75396; 
            last_longitude=37.620393;
            last_zoom=10;
        }
        current_center=[last_latitude,last_longitude];

        // Создание карты.
        var myMap = new ymaps.Map("map", {
            center: current_center,
            zoom: last_zoom,
            controls: ['largeMapDefaultSet']
        },{
            searchControlProvider: 'yandex#search'
        }),
        markerElement = jQuery('#selection_marker'),
        dragger = new ymaps.util.Dragger({
            autoStartElement: markerElement[0] // Драггер будет автоматически запускаться при нажатии на элемент 'marker'.
        }),
        markerOffset,
        markerPosition;

        //Обработчик смещения карты
        mapMoveHandler(myMap);

        //Маркер для перемещения парковки
        parkingMoveMarker(myMap,markerElement,dragger);

        //Метки парковок на карте
        parkingsMarks(myMap,map_data['parkings_data']);

        //Обработка нажатия на кнопку добавления парковки
        addParkingButtonHandler(myMap);

        //Обработка списка фильтров
        filterMapSelectHandler(filter_value);
    }
}

function parkingsMarks(myMap,parkings_data) //Вывод меток парковок
{
    parkings_data=objectToArray(parkings_data);

    for (let i = 0; i < parkings_data.length; i++) {

        var latitude=parkings_data[i]['latitude'];
        var longitude=parkings_data[i]['longitude'];
        let parking_id=parkings_data[i]['parking_id'];

        let preset='islands#blueParkingIcon';
        
        //Собственные метки
        if(parkings_data[i]['user_id']==parkings_data[i]['current_user_id'])
        {
            preset='islands#redParkingIcon';
        }

        //Метки черновика
        if(parkings_data[i]['user_id']==parkings_data[i]['current_user_id'] && parkings_data[i]['draft']=="1")
        {
            preset='islands#grayParkingIcon';
        }

        //Метка
        parking_mark = new ymaps.GeoObject({

            // Описание геометрии.
            geometry: {
                type: "Point",
                coordinates: [latitude, longitude]
            },
            // Свойства.
            properties: {
                parking_id: parking_id
            }
        }, {
            // Опции.
            preset: preset,
    
        });

        //Функция клика по метке
        parking_mark.events.add('click', function(e) {

            //Запрос данных конкретной парковки
            let url="../request_handler.php";
            //Отправка данных формы
            var data = {
                get_parking_preview_data: true,
                parking_id: parking_id,
            };
            var data_json = JSON.stringify(data);
            requestTo(openParkingPreview,data_json,url);
            //redirectTo('parking_card.php?parking_id='+parking_id+'&latitude='+latitude+'&longitude='+longitude);
        });
        
        //Размещение метки на карте
        myMap.geoObjects.add(parking_mark);
    }
}

function parkingMoveMarker(map,markerElement,dragger) //Маркер для перемещения парковки
{
    dragger.events
    .add('start', onDraggerStart)
    //.add('move', onDraggerMove)
    //.add('stop', onDraggerEnd);

    function onDraggerStart(event) {   
        var offset = markerElement.offset(),
            position = event.get('position');
        // Сохраняем смещение маркера относительно точки начала драга.	
        markerOffset = [
            position[0] - offset.left,
            position[1] - offset.top
        ];
        markerPosition = [
            position[0] - markerOffset[0],
            position[1] - markerOffset[1]
        ];

        applyMarkerPosition();
    }

    function onDraggerMove(event) {
        applyDelta(event);
    }

    function onDraggerEnd(event) {
        applyDelta(event);
        markerPosition[0] += markerOffset[0];
        markerPosition[1] += markerOffset[1];
        // Переводим координаты страницы в глобальные пиксельные координаты.
        var markerGlobalPosition = map.converter.pageToGlobal(markerPosition),
            // Получаем центр карты в глобальных пиксельных координатах.
            mapGlobalPixelCenter = map.getGlobalPixelCenter(),
            // Получением размер контейнера карты на странице.
            mapContainerSize = map.container.getSize(),
            mapContainerHalfSize = [mapContainerSize[0] / 2, mapContainerSize[1] / 2],
            // Вычисляем границы карты в глобальных пиксельных координатах.
            mapGlobalPixelBounds = [
                [mapGlobalPixelCenter[0] - mapContainerHalfSize[0], mapGlobalPixelCenter[1] - mapContainerHalfSize[1]],
                [mapGlobalPixelCenter[0] + mapContainerHalfSize[0], mapGlobalPixelCenter[1] + mapContainerHalfSize[1]]
            ];
        // Проверяем, что завершение работы драггера произошло в видимой области карты.
        if (containsPoint(mapGlobalPixelBounds, markerGlobalPosition)) {
            // Теперь переводим глобальные пиксельные координаты в геокоординаты с учетом текущего уровня масштабирования карты.
            var geoPosition = map.options.get('projection').fromGlobalPixels(markerGlobalPosition, map.getZoom()),
            // Получаем уровень зума карты.
            zoom = map.getZoom(),
            // Получаем координаты тайла.
            tileCoordinates = getTileCoordinate(markerGlobalPosition, zoom, 256);
            /*
            alert([
                'Координаты: ' + geoPosition,
                'Уровень зума: ' + zoom,
                'Глобальные пиксельные координаты: ' + markerGlobalPosition,
                'Координаты тайла: ' + tileCoordinates
            ]);
            */
        }
    }

    function applyDelta (event) {
        // Поле 'delta' содержит разницу между положениями текущего и предыдущего события драггера.
        var delta = event.get('delta');
        markerPosition[0] += delta[0];
        markerPosition[1] += delta[1];
        applyMarkerPosition();
    }

    function applyMarkerPosition () {
        markerElement.css({
            left: markerPosition[0],
            top: markerPosition[1]
        });
    }

    function containsPoint (bounds, point) {
        return point[0] >= bounds[0][0] && point[0] <= bounds[1][0] &&
                point[1] >= bounds[0][1] && point[1] <= bounds[1][1];
    }

    function getTileCoordinate(coords, zoom, tileSize){
        return [
            Math.floor(coords[0] * zoom / tileSize),
            Math.floor(coords[1] * zoom / tileSize)
        ];
    }
}

function mapMoveHandler(map) //Обработчик смещения карты
{
    map.events.add('boundschange', function() { 
        writeCookie("last_latitude", map.getCenter()[0], 30);
        writeCookie("last_longitude", map.getCenter()[1], 30);
        writeCookie("last_zoom", map.getZoom(), 30);
    });
}

function userLocationHandler(myMap) //Определитель местонахождения пользователя
{
    var location = ymaps.geolocation.get();
    let user_position_latitude=false;
    let user_position_longitude=false;

    location.then(
    function(result) {
        myMap.geoObjects.add(result.geoObjects);
        user_position_latitude=result.geoObjects.position[0];
        user_position_longitude=result.geoObjects.position[1];

        writeCookie("user_position_latitude", user_position_latitude, 30);
        writeCookie("user_position_longitude", user_position_longitude, 30);
    },
    function(err) {
        console.log('Ошибка: ' + err);
        writeCookie("user_position_latitude", false, 30);
        writeCookie("user_position_longitude", false, 30);
    }
    );
}

function getAddress(coords) //Определитель адреса по координатам (обратное геокодирование).
{
    ymaps.geocode(coords).then(function (res) {
        var geo_object = res.geoObjects.get(0);
        writeCookie("selection_marker_adress", geo_object.getAddressLine(), 30);
    });
}

function openParkingPreview(preview_data_json)
{
    var parking_preview_div=document.getElementById("parking_preview_div");
    parking_preview_div.style.display="block";

    preview_data_json=preview_data_json.replace("/", '');
    let preview_data = JSON.parse(preview_data_json);
    preview_data = JSON.parse(preview_data);
    let response_content=preview_data['response_content'];

    parking_preview_data=response_content["parking_preview_data"][0];

    //Заполнение формы превью
    var name_display=document.getElementById("name_display");
    var adress_display=document.getElementById("adress_display");
    var properties_display=document.getElementById("properties_display");

    name_display.innerHTML=parking_preview_data["name"];
    adress_display.innerHTML=parking_preview_data["adress"];
    
    
    //properties_display.innerHTML=parking_preview_data["properties"];




}



//Функции обработчиков элементов страницы

function addParkingButtonHandler(myMap) //Обработчик нажатия на кнопку добавления парковки
{
    let add_parking_button=document.getElementById("add_parking_button");
    let cancel_add_parking_button=document.getElementById("cancel_add_parking_button");
    if(add_parking_button===null || cancel_add_parking_button===null)
    {return(false);}

    let selection_marker=document.getElementById("selection_marker");

    let button_state="inactive";

    add_parking_button.style.display="inline-block";

    //click listener на кнопку добавления парковки
    add_parking_button.addEventListener("click", (event) => {

        console.log("add_parking_button");

        //Первое нажатие
        if(button_state=="inactive")
        {
            button_state="location_choice";
            selection_marker.style.display="block";
            add_parking_button.innerHTML="&#10004;";
            cancel_add_parking_button.style.display="inline-block";
            return(false);
        }

        //Выбор координат парковки
        if(button_state=="location_choice")
        {
            button_state="inactive";
            let center_latitude=readCookie("last_latitude");
            let center_longitude=readCookie("last_longitude");
            let selection_marker_center=[center_latitude,center_longitude];

            selection_marker.style.display="none";
            //add_parking_button.innerHTML="+";
            cancel_add_parking_button.style.display="none";

            //Переход на форму добавления парковки
            getAddress(selection_marker_center);
            setTimeout(function(){ 
                redirectTo('parking_card.php?new_parking_card=true&latitude='+center_latitude+'&longitude='+center_longitude);
            },500);
            
            return(false);
        }
    });

    //click listener на кнопку отмены добавления парковки
    cancel_add_parking_button.addEventListener("click", (event) => {

        console.log("cancel_add_parking_button");

        selection_marker.style.display="none";
        add_parking_button.innerHTML="Добавить парковку";
        cancel_add_parking_button.style.display="none";
        button_state="inactive";
        return(false);

    });
}

function filterMapSelectHandler(filter_value) //Обработчик фильтра показа парковок
{
    let filter=document.getElementById("filter");
    let filter_all=document.getElementById("filter_all");
    let filter_only_user=document.getElementById("filter_only_user");

    if(filter==null)
    {return(false);}

    if(filter_value=="all")
    {
        filter.value="all";
        filter_all.selected=true;
    }
    else if(filter_value=="only_user")
    {
        filter.value="Только мои парковки";
        filter_only_user.selected=true;
    }

    filter.onchange = function(event) {
        const filter_value = event.target.value;

        writeCookie("filter_value", filter_value, 30);

        window.location.reload();
    };
}




