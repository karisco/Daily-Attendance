<?php

namespace app\admin\controller;

use think\Controller;
use think\captcha\Captcha;
use app\admin\validate\LoginValidate;
use think\facade\Request;
use think\Db;
use think\facade\Cookie;


class LoginController extends Controller{

    public function index(){
        $request = strtolower(Request::instance()->method());
        if($request == 'get'){
            return $this->fetch('login' , ['msg' => '']);
        }else{
            $param = Request::instance()->only(['account' ,'verify' ,'pass'],'post');

            //验证器验证
            $validate = new LoginValidate();
            $result = $validate->scene('index')->check($param);

            //返回报错
            if(!$result) return $this->fetch('login',['code' => 0 , 'msg' => $validate->getError()]);
//            if(!captcha_check($param['verify'])) return $this->fetch('login',['msg' => '8验证码错误']);

            //验证用户
            $user = Db::name('admin')->field(['id','account'])->where(['account' => $param['account'] , 'pass' => $param['pass']])->find();
            if(empty($user)) return $this->fetch('login',['msg' => '用户不存在']);

            //缓存和cookie
            Cookie::set('token',md5($user['account'].time()),604800);
            redis()->set(md5($user['account'].time()) , $user['id'] , 604800);

            $user = Db::name('admin')->where(['id'=>$user['id']])->data(['last_login_time' => time() , 'last_login_location' => Request::instance()->ip()])->update();

            $this->redirect('index/index');

        }
    }

    //验证码
    public function verify(){
        $config = [
            'fontSize'    =>   15,
            'imageH'      =>   34,
            'imageW'      =>   120,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

}