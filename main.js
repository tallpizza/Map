let markers = null

function load_Data(){
    fetch('list.json')
    .then(response => response.json())
    .then(data => {
        
        let points = new L.geoJson(data,{
            onEachFeature: function (feature, layer) {
                let text = '<h3>' + feature.properties['popupContent'] + '</h3><h4>' + feature.properties['name'] + '</h4>';
                // <img src="' + feature.properties.image_link + '"width="200px" /><br>Visitors: ' + String(feature.properties.visitors);
                layer.bindPopup(text);
            }
            
        });
        
        markers = L.markerClusterGroup({
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
    let discription = document.getElementById('discription').value;
    let dong = document.getElementById('dong').value;

    let data = new FormData
    data.append("func","save_data")
    data.append("name",name)
    data.append("discription", discription)
    data.append("dong",dong)
    fetch("getlatlang.php",{
        method : 'POST',
        body:data
    })
    .then(response=>response.json())
    .then(data=>{
        if (data['status'] == 'success'){
            markers.clearLayers();
            load_Data()

            window.alert("성공!😎")
            window.location.reload()
        }
        else {
            window.alert("주소를 확인해주세요😥")
        }
        
    })
}

let remove = (index)=>{
    let data = new FormData
    data.append("func","delete_feature")
    data.append("index",index)
    fetch('getlatlang.php',{
        method: 'POST',
        body:data
    })
    .then(response=>response.json())       
    .then(data=>{
        if (data['status'] == 'success'){
            markers.clearLayers();
            load_Data()

            window.alert("성공!😎")
            window.location.reload()
        }
        else {
            window.alert("오류가 발생했어요😥")
        }
    })
}

let get_location_list = ()=>{
    fetch('list.json')
    .then(response=>response.json())
    .then(data=>{
        console.log(data)
        data.forEach((feature,index)=>{
            document.getElementById('delete_box').innerHTML += 
            feature['properties']['name']+" : "+feature['properties']['location'] 
            +"<input type='button' value='delete' onclick = 'remove("+index+")'><br>"
        })
    })
}

document.getElementById('delbutton').addEventListener('click', function(){
    document.getElementById('delete_background').style.display = 'block';
    document.getElementById('delete_box').style.display = 'flex';
    get_location_list()
})
// if inputbackground is clicked, display none the inputbackground
document.getElementById('delete_background').addEventListener('click', function(){
    document.getElementById('delete_background').style.display = 'none';
    document.getElementById('delete_box').style.display = 'none';
    document.getElementById('delete_box').innerHTML = ""
})