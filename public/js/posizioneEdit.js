let map, infoWindow;
//document.getElementById('invio').style.visibility = 'hidden';
var oldLAT = parseFloat(document.getElementById("lat").value);
var oldLONG = parseFloat(document.getElementById("lng").value);
console.log(oldLAT, oldLONG);
function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: oldLAT, lng: oldLONG },
    zoom: 8,
    scrollwheel: true,
    });

    const uluru = { lat: oldLAT, lng: oldLONG };
        let marker = new google.maps.Marker({
        position: uluru,
        map: map,
        draggable: true
    });

    infoWindow = new google.maps.InfoWindow();

    const locationButton = document.createElement("button");

    locationButton.textContent = "Trova la tua posizione";
    locationButton.setAttribute('type', 'button');
    locationButton.setAttribute('id', 'posizioneBTN');
    locationButton.setAttribute('class', 'btn btn-secondary btn-lg btn-block');
    //map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
    //document.body.appendChild(locationButton);
    //btn posizione
    //document.getElementById("pos").appendChild(locationButton);
    //document.getElementById('pos').style.visibility = 'hidden';
    /*locationButton.addEventListener("click", () => {
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
        (position) => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            infoWindow.setPosition(pos);
            infoWindow.setContent("Posizione trovata: clicca su '+' per proseguire!");
            infoWindow.open(map);
            map.setCenter(pos);
            console.log("latitudine: "+pos.lat);
            console.log("longitudine: "+pos.lng);
            marker.setPosition(pos)
            },
            () => {
                handleLocationError(true, infoWindow, map.getCenter());
            }
            );
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }
    });*/
    var flag = 0;
    google.maps.event.addListener(marker,'position_changed',
    function (){
        let lat = marker.position.lat()
        let lng = marker.position.lng()
        $('#lat').val(lat)
        $('#lng').val(lng)
        console.log(lat, lng);
        if(flag == 0){
            document.getElementById('lbl').remove();
            flag = 1;
        }

        //document.getElementById('posizioneBTN').remove();
        document.getElementById('invio').style.visibility = 'visible' //mostra pulsante submit se è stata trovata la posizione
    })

    google.maps.event.addListener(map,'click',
    function (event){
        pos = event.latLng
        marker.setPosition(pos)
        infoWindow.close();
    })
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
        browserHasGeolocation
        ? "Error: The Geolocation service failed."
        : "Error: Your browser doesn't support geolocation."
    );
    infoWindow.open(map);
}
