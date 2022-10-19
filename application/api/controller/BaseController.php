<?php
namespace app\api\controller;

use think\Container;
use think\App;
use think\Response;
use think\exception\HttpResponseException;

class   BaseController{

    //token
    protected $token = '';

    //user id
    protected $userId = -1;

    //user
    protected $user;

    protected $app;

    //request
    protected $request;

    //前置操作列表
    protected $beforeActionList = [];

    public function __construct(App $app = null){
        $this->app =$app ? : Container::get('app');
        $this->request = $this->app['request'];

        //User initialization
        $this->_initUser();

        //app initialization
        $this->initialize();
    }

    private function _initUser(){

        $token = $this->request->header('xx-token');

        if(!$token){
            return;
        }

        //获取服务器的token
        $userId = redis()->get($token);

        if($userId != null){
            $this->userId = $userId;
        }
    }

    protected function initialize(){
    }

    protected function success($data = '' , $msg = 'success' , $header = []){

        $code = 1;

        $res = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        $type                                   = 'json';
        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Api-Version,XX-Wxapp-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';

        $response                               = Response::create($res, $type)->header($header);
        throw new HttpResponseException($response);
    }

    protected function error($msg , $data = '' , $header = []){

        $code = 0;

        if(is_array($msg)){
            $code = $msg['code'];
            $msg = $msg['msg'];
        }

        $res = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        $type                                   = 'json';
        $header['Access-Control-Allow-Origin']  = '*';
        $header['Access-Control-Allow-Headers'] = 'X-Requested-With,Content-Type,XX-Device-Type,XX-Token,XX-Api-Version,XX-Wxapp-AppId';
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE,OPTIONS';

        $response                               = Response::create($res, $type)->header($header);
        throw new HttpResponseException($response);
    }
}