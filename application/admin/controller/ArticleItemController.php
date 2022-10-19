<?php


namespace app\admin\controller;

use think\Db;
use think\Exception;
use think\facade\Request;

class ArticleItemController extends BasicController {

    protected $table = 'article_item';

    public function index(){
        $data = Db::name($this->table)->select();
        $data = $this->getTree($data , 0 );
        $this->assign([
            'list'    =>    $data,
            'status'  =>    $this->statusTitle('itemStatus')
        ]);
        return $this->fetch('index');
    }

    public function add(){
        if($this->request->isPost()){
            $data = Request::instance()->only(['title','parent_id','status'],'post');
            $data['create_time'] = time();
            $status = Db::name($this->table)->insert($data);
            $status || $this->error('插入失败','index');
            $this->success('插入成功','index');
        }else{
            $data = Db::name($this->table)->where(['parent_id' => 0])->select();
            $this->assign([
                'list'    =>    $data,
            ]);
            return $this->fetch('add');
        }
    }

    public function sort(){

    }

    public function changeStatus(){
        try{
            $param = Request::instance()->only(['id','status']);
            empty($param) && $this->error('系统错误');
            $param['status'] == 1 ? $param['status'] = 2 : $param['status'] = 1;
            $param['update_time'] = time();

            $status = Db::name($this->table)->where(['id' => $param['id']])->update($param);
            $status && $this->success('保存成功','index','',1);

        }catch(Exception $e){

        }
    }
    public function delete(){
        try{
            $param = Request::instance()->only(['id']);
            empty($param) && $this->error('系统错误');

            $status = Db::name($this->table)->where(['id' => $param['id']])->delete();
            $status && $this->success('删除成功','index','',1);

        }catch(Exception $e){

        }
    }

}