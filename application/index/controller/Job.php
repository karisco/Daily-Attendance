<?php
/**
 * Created by PhpStorm.
 * User: 29588
 * Date: 2020/3/27
 * Time: 13:35
 */

namespace app\index\controller;
use think\Queue;

class Job
{

    public function index(){
        redis()->set('1','13343');
    }

    public function actionWithHelloJob(){
        $jobHandlerClassName  = 'app\index\job\Demo';
        $jobQueueName  	  = "helloJobQueue";
        $jobData       	  = [ 'name'=>'zhangsan'.time()] ;
        $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );
        if( $isPushed !== false ){
            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        }else{
            echo 'Oops, something went wrong.';
        }
    }

}
