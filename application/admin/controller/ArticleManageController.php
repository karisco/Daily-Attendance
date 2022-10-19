<?php


namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use app\admin\validate\ArticleValidate;

class ArticleManageController extends BasicController {

    protected $table = 'article';

    public function index(){

        !isset($_GET['sort_field']) && $_GET['sort_field'] = 'id';
        !isset($_GET['sort_by']) && $_GET['sort_by'] = 'asc';
        !isset($_GET['keywords']) && $_GET['keywords'] = null;

        $search = ['a.id','a.user_id','a.type','a.title','a.create_time','a.update_time','a.like','b.nickname'];
        $sql = $this->whereSearch($search ,$_GET['keywords']);

        $articleData = Db::name($this->table)->alias('a')
            ->field('a.id , a.user_id , a.type , a.title , a.status , a.create_time , a.update_time , a.like , a.main_image , b.nickname')
            ->whereOr($sql)
            ->join('ar_user b' , 'b.id = a.user_id')
            ->order('sort', 'desc')
            ->order("$_GET[sort_field] $_GET[sort_by] ")
            ->paginate(10);
        $page = $articleData->render();
        $articleData = $articleData->items();
        empty($articleData) || array_walk($articleData , function ($value , $key) use (&$articleData){
            $articleData[$key]['main_image'] = $this->imgtransform($articleData[$key]['main_image']);
        });
//        dump($articleData);
        $this->assign([
            'list'   => $articleData,
            'page'   => $page,
            'status' => $this->statusTitle('articleStatus'),
        ]);
        return $this->fetch('index');
    }

    public function edit(){
        if($this->request->isPost()){
            $param = Request::instance()->only(['id','status'],'post');
            $param['update_time'] = time();

            $validate = new ArticleValidate();
            $result = $validate->scene('edit')->check($param);
            ($result) || $this->error($validate->getError());
            $status = Db::name($this->table)->where(['id' => $param['id']])->update($param);

            $status && $this->success('保存成功','index','',1);
        }else{
            $id = $this->request->only(['id']);
            $articleInfo = Db::name($this->table)->alias('a')
                ->field('a.id , a.code,a.user_id , a.type , a.title , a.main_image , a.content , a.status , a.create_time , a.update_time , a.like , a.main_image , b.nickname')
                ->join('ar_user b' , 'b.id = a.user_id')
                ->where(['a.id' => $id])
                ->find();
            $articleInfo['main_image'] = $this->imgtransform($articleInfo['main_image']);
//            dump($articleInfo);die;
            $this->assign([
                'info'     =>    $articleInfo,
                'status'   =>    $this->statusTitle('articleStatus'),
            ]);
            return $this->fetch();
        }
    }
}