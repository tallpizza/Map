
function load_Data(){
    fetch('list.json')
    .then(response => response.json())
    .then(data => {
        console.log(data)
        
        let points = new L.geoJson(data,{
            onEachFeature: function (feature, layer) {
                let text = '<h3>' + feature.properties['popupContent'] + '</h3><h4>' + feature.properties['name'] + '</h4>';
                // <img src="' + feature.properties.image_link + '"width="200px" /><br>Visitors: ' + String(feature.properties.visitors);
                layer.bindPopup(text);
            }
            
        });
        
        let markers = L.markerClusterGroup({
        spiderfyOnMaxZoom:true,
    });
    
    markers.addLayer(points).addTo(mymap);
})
}
load_Data();




//leaflet 지도를 id='map'에 그려넣는다.
const mymap = L.map('map').setView([37.566, 127.478], 9);
var basemap = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png');
    basemap.addTo(mymap);



// if inputbackground is clicked, display none the inputbackground
document.getElementById('inputbackground').addEventListener('click', function(){
    document.getElementById('inputbackground').style.display = 'none';
    document.getElementById('inputbox').style.display = 'none';
})

// if inputbutton is clicked, display the inputbackground
document.getElementById('inputbutton').addEventListener('click', function(){
    document.getElementById('inputbackground').style.display = 'block';
    document.getElementById('inputbox').style.display = 'flex';
})


// function to send data to server and get data from server
let senddata = function() {
    let name = document.getElementById('name').value;
    let dong = document.getElementById('dong').value;

    let data = new FormData
    data.append("func","save_data")
    data.append("name",name)
    data.append("dong",dong)
    fetch("http://182.222.233.17/MapOOB/getlatlang.php",{
        method : 'POST',
        body:data
    })
    .then(response=>response.json())
    .then(data=>{
        console.log(data)
        load_Data()
    })
}
