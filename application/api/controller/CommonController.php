<?php
namespace app\api\controller;

use think\Exception;

class CommonController extends BaseController {

    public function returns( int $code , string $msg ,$data = ''){
        $res = ($data === '') ? ['code' => $code , 'msg' => $msg ,'end_time' => time()] : ['code' => $code , 'msg' => $msg , 'end_time' => time() , 'data' => $data];
//        $res = ($data === '') ? ['code'=>$code,'msg'=>$msg,'end_time'=>time()] : ['code'=>$code,'msg'=>$msg,'end_time'=>time(),'data'=>$data];
        echo json_encode($res , true);
        exit;
    }

    public function httpGet($url){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);

        curl_close($curl);

        return $res;

    }

    public function Exception($msg){

        throw new Exception($msg);

    }

    public function webError(){
        sleep(5);
        header("HTTP/1.1 404 Not Found");exit;
    }

    public function imgtransform($imgUrl){
        $url = 'http://8.130.35.147/cache/' . $imgUrl;
        return $url;
    }

}