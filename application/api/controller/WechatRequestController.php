<?php
namespace app\api\controller;

use think\facade\Request;


class WechatRequestController extends CommonController {

    protected $action;

    public function __construct(){
        parent::__construct();
        $this->action =Request::instance()->action(true);

        $this->request();

        $this->param();

        $this->validate();
    }

    protected function request(){
        //判断此方法是否可以访问
        array_key_exists($this->action,$this->requestWay ?? $this->returns(1001,'系统错误')) || $this->webError();
        //判断此方法的访问方式

        $way = Request::instance()->method();
        if($this->requestWay != 'put'){
            strtolower($way) == $this->requestWay[$this->action] || $this->webError();
        }

    }

    protected function param(){
        if(isset($this->field[$this->action])){
            $this->param = Request::instance()->only($this->field[$this->action],$this->requestWay[$this->action]);
        }else{
            $requestWay = $this->requestWay[$this->action];
            $this->param = Request::instance()->$requestWay();
        }
    }

    protected function validate(){

        //获取对应的控制器名称
        $controller = Request::instance()->controller();

        $module = Request::instance()->module();

        //获取对应的验证器路径
        $ValidateName = "app\\{$module}\\validate\\{$controller}";

        //判读验证器是否存在
        if(!class_exists($ValidateName)){return $this;}

        //获取到对应的验证器对象

        $validate = new $ValidateName();

        //反射目标对象

        $property = new \ReflectionProperty($validate,'scene');

        //解除限制

        $property->setAccessible(true);

        //获取目标中的属性及验证场景

        $scene = $property->getValue($validate);

        //判读是否存在对应的验证场景,无则不使用验证场景

        if(!array_key_exists($this->action,$scene)){return $this;}

        //判断参数是否符合场景
        (!$validate->scene($this->action)->check($this->param)) && $this->returns(1002,$validate->getError());
    }
}