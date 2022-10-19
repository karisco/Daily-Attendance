<?php
namespace app\api\controller;

use think\Db;
use think\Exception;
use app\api\validate\DemoValidate;

class DemoController extends WechatRequestController {

    protected $requestWay = [
        'index'                      =>        'get',
    ];

    protected $field = [
        'index'                      =>        [''],
    ];

    public function __construct(){
        parent::__construct();
//        ( $this->userId < 0 ) && $this->returns(0,'请登录');
    }

    public function index(){
        try{

            $param = $this->param;
            empty($param) && $this->Exception('系统错误');

            $validate = new DemoValidate();
            $valiResult = $validate->scene('index')->check($param);
            $valiResult || $this->Exception($validate->getError());

            $this->returns(1,'success');

        }catch(Exception $e){
            $this->returns(1001,$e->getMessage());
        }


    }

}