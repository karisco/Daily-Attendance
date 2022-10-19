<?php


namespace app\admin\controller;

use app\admin\model\NavsModel;
use think\App;
use think\facade\Cookie;
use think\Controller;

class BasicController extends Controller {


    public function rules(){

    }

    public function __construct(){
        parent::__construct();
        $cookie = Cookie::get('token');
        $token = redis()->get($cookie);
        empty($token) && $this->error('请先登录','login/index');
    }

    public function exit(){
        $cookie = Cookie::get('token');
        $token = redis()->del($cookie);
        $this->redirect('login/index');
    }

    public function statusTitle($name){
        $model = new NavsModel();
        return $model->$name;
    }

    public function whereSearch(array $field , string $keywords = null ){
        if($keywords == null){
            return true;
        }
        foreach ($field as $item) {
            $map[] =  [$item, 'like', '%'.$keywords.'%'];
            $return[] = $map;
            $map = null;
        }
        return $return;
    }

    public function imgtransform($imgUrl){
        $url = 'http://8.130.35.147/cache/'.$imgUrl;
        return $url;
    }

    function getTree($array, $pid=0){
        $tree = array();
        foreach ($array as $key => $value) {
            if ($value['parent_id'] == $pid) {
                $value['children'] = $this->getTree($array, $value['id']);
                $tree[] = $value;
            }
        }
        return $tree;
    }
}