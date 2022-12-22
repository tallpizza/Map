<?php
function responseJSON($msg) {
    header('Content-Type: application/json');
    die(json_encode($msg));
}

// function generatinggps($address){
//     // preg_match('/[가-힣]+로\s*(\d+길)*\s*\d+/', $address, $list);
//     preg_match('/([가-힣]+시\s)*([가-힣]+구\s)*[가-힣]+\d*로\s*(\d+길)*\s*\d+/', $address, $sigu);
//     if (isset($sigu[0]) == true){
//     $nospace =str_replace(" ","",$sigu[0]);

//     $url = "http://api.vworld.kr/req/search?service=search&request=search&version=2.0&crs=EPSG:4326&size=10&page=1&query=".$nospace."&type=address&category=road&format=json&errorformat=json&key=13397C34-E345-3387-8F4C-8FDECB608764";

//     $res = file_get_contents($url);
//     $resj = json_decode($res);

//     if ($resj->response->status == "OK"){

//         $x = $resj->response->result->items[0]->point->x;
//         $y = $resj->response->result->items[0]->point->y;

//         $latlong = array($x, $y);
//         $GLOBALS["latlng"]= $latlong;
//     } else {
//         return;
//     }
//     } else {
//         responseJSON("주소확인필요");
//         return;
//     }
// }

$data = exec('wget --no-check-certificate -l 0 --http-user=notify --http-passwd=6cd6f41455d78245f1295895838dd1ec14449565a9a8c1c8ea43cb35b592e3ab --post-data "func=get_gps" https://iot.anhive.net/w00/login_service.php -O -');

$jdata = json_decode($data);
$array1 = $jdata->data;
// var_dump($array1[0]->oncnt);

$nmlarr = array("type" => 'FeatureCollection', "features" => []);



for ($i = 0; $i < count($array1); $i++){

    $total = $array1[$i]->total;
    // $status = "OFF";

    // $latlng= "Not defined";
    if (isset($array1[$i]->longtitude)&&isset($array1[$i]->latitude)){

        $latitude = $array1[$i]->latitude;
        $longtitude = $array1[$i]->longtitude;
        $code_name= $array1[$i]->name;
        $oncnt = $array1[$i]->oncnt;
        $offcnt = $array1[$i]->offcnt;
        $chcnt = $array1[$i]->chcnt;
        $status = "";



        for ($j=0; $j < intval($total); $j++){
            if ($j < intval($oncnt)){
                $status = "ON";
            } elseif (($j-intval($oncnt)) < intval($offcnt) && 0<= ($j-intval($oncnt))){
                $status = "OFF";
            } elseif (0 <= ($j - (intval($oncnt)+intval($offcnt)))&&($j - (intval($oncnt)+intval($offcnt))) < intval($chcnt)){
                $status = "CHECK";
            }
            $nomalarray = array(
                "type"=>"Feature",
                "geometry"=>array(
                    "type"=> "Point",
                    "coordinates" => array(
                        $longtitude,
                        $latitude,
                    )
                ),
                "properties" => array(
                    "name"=> $code_name,
                    "status"=> $status
                ));

            array_push($nmlarr["features"],$nomalarray);
        }



    } else {
        continue;
    }


}
// $jsonarray = array("data"=>$nmlarr);
responseJSON($nmlarr);

?>