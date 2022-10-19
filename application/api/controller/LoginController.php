<?php
namespace app\api\controller;

use think\facade\Request;
use think\Exception;
use think\Db;

class LoginController extends CommonController {

    private $table = 'user';

    private $config = [
        'AppId'      => 'wx5059e4977405e71c',
        'AppSecret'  => '9b21445912c5c8ce76e75d862c8591a3'
    ];

    public function register(){

        try{
            //接收前端code参数
            $param = Request::instance()->only(['code'] , 'post');
            empty($param) && $this->Exception('参数不存在');

            //调用微信开放接口获取openid
            $wx_message = $this->getCode($param['code']);
            $openid = $wx_message['openid'];

            //判断用户是否注册
            $find_user =Db::name($this->table)->where(['openid' => $openid])->find();
            if(empty($find_user)){

                //新用户 进行自动注册
                $insert =[
                    'openid'          => $openid,
                    'stage'           => 1,
                    'create_time'     => time(),
                    'last_login_time' => time()
                ];
 
                Db::name($this->table)->insert($insert,false,true) || $this->Exception('登录失败');

                $userInfo = [
                    'avatar'   => '',
                    'nickname' => '',
                    'phone'    => '',
                    'stage'    => 1,
                ];

            }else{

                $find_user['status'] !=1 && $this->Exception('此账号无效');

                $edit = Db::name($this->table)->where(['id'=>$find_user['id']])->data(['last_login_time' => time()])->update();
                $edit || $this->Exception('系统错误');

                $userInfo = [
                    'avatar'   => $find_user['avatar'],
                    'nickname' => $find_user['nickname'],
                    'phone'    => $find_user['phone'],
                    'stage'    => $find_user['stage'],
                ];

            }
        }catch(Exception $e){
            $this->error($e->getMessage());
        }

        //生成token 两次md5加密
        $token = md5($openid);
        $userId = Db::name($this->table)->field('id')->where(['openid' => $openid])->find();
        redis()->set($token , $userId['id'] , 864000) || $this->error('系统错误');

        $this->success([
            'userInfo'  =>  $userInfo,
            'token'     =>  $token
        ]);
    }

    public function getCode($code){
        try{

            $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->config['AppId']}&secret={$this->config['AppSecret']}&js_code=" . $code . "&grant_type=authorization_code";

            $result = json_decode($this->httpGet($url) , true);
            ( !empty($result['session_key']) && !empty($result['openid']) ) || $this->Exception('登录异常');

            return $result;

        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

    public function SetUserInfo()
    {

        try {
            $this->user = Db::name('user_wechat')->where(['id'=> $this->userId])->find();

            ($this->userId == null) && $this->json(10001, '请登录后重试');
            ($this->user['stage'] != 1) && $this->Exception('异常操作');
            $param = Request::instance()->only(['avatarUrl', 'nickName'], 'post');

//            $validate = new LoginValidate();
//            $result = $validate->scene('SetUserInfo')->check($param);
//            ($result) || $this->Exception($validate->getError());

            $update = [
                'avatar'     =>    $param['avatarUrl'],
                'nickname'    =>    $param['nickName'],
                'stage'      =>    2,
            ];

            $status = Db::name($this->table)->where(['id' => $this->userId])->update($update);
            $this->returns(1, '授权成功', [
                'avatarUrl' => $param['avatarUrl'],
                'nickName' => $param['nickName'],
                'stage' => 2,
            ]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function SetUserPhone(){

        try {

            $param = Request::instance()->only(['code', 'encryptedData', 'iv'], 'post');
            dump($param);die;
//            $validate = new LoginValidate();
//            $result = $validate->scene('SetUserPhone')->check($param);
//            ($result) || $this->Exception($validate->getError());
            ($this->user['stage'] == 1) && $this->Exception('请先授权头像昵称');
            //获取用户openid和session_key
            $result = $this->GetCode($param['code']);
            //解密获取用户手机号
            $pc = new WXBizDataCrypt($this->config['AppId'], $result['session_key']);
            $errCode = $pc->decryptData($param['encryptedData'], $param['iv'], $data);
            ($errCode != 0 || empty($data['purePhoneNumber'])) && $this->Exception('授权失败');
            $Post = [
                'phone' => $data['purePhoneNumber'],
                'stage' => 3,//获取手机号
                'update_time' => time() //更改修改时间
            ];
            $status = Db::name($this->table)->where(['id' => $this->userId])->update($Post);
            ($status)
                ? $this->json(1, '获取成功', [
                'avatar' => $this->user['avatarUrl'],
                'nickname' => $this->user['nickName'],
                'phone' => $data['purePhoneNumber'],
                'stage' => 3,
            ])
                : $this->Exception('获取失败');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}