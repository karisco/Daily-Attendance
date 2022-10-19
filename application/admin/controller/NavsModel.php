<?php
namespace app\admin\model;
use think\Model;
class NavsModel extends Model{

    public $userStatus = [
        1     =>    '正常',
        2     =>    '冻结'
    ];

}