<?php
namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use app\admin\validate\UserValidate;

class UserController extends BasicController{

    protected $table = 'user';

    public function index(){


        !isset($_GET['sort_field']) && $_GET['sort_field']  = 'id';
        !isset($_GET['sort_by'])    && $_GET['sort_by']     = 'asc';
        !isset($_GET['keywords'])   && $_GET['keywords']    = null;

        $search = ['id','nickname','location','phone','mail','status','create_time','last_login_time','update_time'];
        $sql = $this->whereSearch($search ,$_GET['keywords']);

        $userData = Db::name($this->table)
            ->whereOr($sql)
            ->order("$_GET[sort_field] $_GET[sort_by]")
            ->paginate(10);
        $page = $userData->render();
        $userData = $userData->items();
        empty($userData) || array_walk($userData , function ($value , $key) use (&$userData){
            $userData[$key]['avatar'] = $this->imgtransform($userData[$key]['avatar']);
        });
        $this->assign([
            'list'   => $userData,
            'page'   => $page,
            'status' => $this->statusTitle('userStatus'),
        ]);
        return $this->fetch('index');
    }

    public function edit(){
        if($this->request->isPost()){

            $param = Request::instance()->only(['id','sex','status'],'post');
            $param['update_time'] = time();

            $validate = new UserValidate();
            $result = $validate->scene('edit')->check($param);
            ($result) || $this->error($validate->getError());

            $status = Db::name($this->table)->where(['id' => $param['id']])->update($param);

            $status && $this->success('保存成功','index','',1);
            dump($status);

        }else{
            $id = $this->request->only(['id']);
            $userInfo = Db::name($this->table)
                        ->field(['id','account','nickname','avatar','sex','birthday','mail','location','phone','status','create_time','last_login_time','update_time'])
                        ->where(['id' => $id])
                        ->find();
            $userInfo['avatar'] = $this->imgtransform($userInfo['avatar']);

            $this->assign([
                'info'     =>    $userInfo,
                'status'   =>    $this->statusTitle('userStatus'),
                'sex'      =>    $this->statusTitle('userSex'),
            ]);
            return $this->fetch();
        }
    }
}