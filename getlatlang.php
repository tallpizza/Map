<?php
header('Access-Control-Allow-Origin: *');
function outputJSON($msg, string $status = 'error'): void
{
    if ($status == 'error')
        error_log(print_r($msg, true) . " in " . __FILE__);

    header('Content-Type: application/json');
    die(json_encode(array(
        'data' => $msg,
        'status' => $status
    )));
}


$services['save_data'] = "_save_data";
function _save_data()
{
    $str = $_POST['dong'];
    // error_log(mb_detect_encoding($str,array('UTF-8','EUC-KR')));

    preg_match('/([가-힣]+\d*[시도군구동읍면리로]+\s*)*/', $str, $sigu);
    if (isset($sigu[0]) == true) {
        $nospace = str_replace(" ","%20",$str);
        // error_log($nospace);

        ### 주소로 검색
        $url = "http://api.vworld.kr/req/search?service=search&request=search&version=2.0&crs=EPSG:4326&size=10&page=1&query=".$nospace."&type=address&category=road&format=json&errorformat=json&key=96C113B7-156D-391E-96CC-12A157128F72";

        $res = file_get_contents($url);
        $resj = json_decode($res);

        // error_log($res);


        ### 행정구역으로 검색
        if ($resj == null) {
            $url = "http://api.vworld.kr/req/search?service=search&request=search&version=2.0&crs=EPSG:4326&size=10&page=1&query=".$nospace."&type=district&category=L4&format=json&errorformat=json&key=96C113B7-156D-391E-96CC-12A157128F72";

            $res = file_get_contents($url);
            $resj = json_decode($res);
        }

        ### 장소검색
        if ($resj == null) {
            $url = "http://api.vworld.kr/req/search?service=search&request=search&version=2.0&crs=EPSG:4326&size=10&page=1&query=".$nospace."&type=place&format=json&errorformat=json&key=96C113B7-156D-391E-96CC-12A157128F72";

            $res = file_get_contents($url);
            $resj = json_decode($res);
        }

        if ($resj == null){
            outputJSON("error : cannot get GPS data");
        }

        if ($resj->response->status == "OK") {


            try {
                $long = $resj->response->result->items[0]->point->x;
                $lat = $resj->response->result->items[0]->point->y;
            } catch (Exception $e) {
                outputJSON("couldn't find lat&long ".$e);
            }


            $newdata = array(
                "type" => "Feature",
                "properties" => array(
                    "name" => $_POST['name'],
                    "popupContent" => $_POST['discription']
                ),
                "geometry" => array(
                    "type" => "Point",
                    "coordinates" => array(
                        $long,
                        $lat
                    )
                )
            );
            $file = "list.json";
            $listfile = file_get_contents($file);
            $jlistfile = json_decode($listfile);

            array_push($jlistfile, $newdata);
            $data_to_save = json_encode($jlistfile, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            file_put_contents($file, $data_to_save);

            outputJSON("Data Added successfully \n lat : " . $lat . "\n long:  " . $long, 'success');
        } else{
            outputJSON("Data received but wrong address");
        }
    } else {
        outputJSON("주소확인필요");
        return;
    }
}



$func = isset($_POST['func']) ? $_POST['func'] : null;
if (!isset($services[$func])) {
    outputJSON('Invalid function');
}
try {
    call_user_func($services[$func]);
} catch (Exception $e) {
    outputJSON($e->getMessage());
}
