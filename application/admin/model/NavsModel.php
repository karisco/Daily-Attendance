<?php
namespace app\admin\model;
use think\Model;
class NavsModel extends Model{

    public $userStatus = [
        1     =>    '正常',
        2     =>    '冻结'
    ];

    public $userSex = [
        1     =>    '男',
        2     =>    '女'
    ];

    public $articleStatus = [
        1     =>    '待审核',
        2     =>    '不通过审核',
        3     =>    '通过审核',
    ];

    public $itemStatus = [
        1     =>    '正常',
        2     =>    '冻结'
    ];

}