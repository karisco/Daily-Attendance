<?php
namespace app\api\validate;

use think\Validate;

class DemoValidate extends Validate{
    protected $rule = [
        'id'                =>        'require',
    ];

    protected $message = [
        'id.require'        =>        '系统错误',
    ];

    protected $scene = [
        'index'        =>        ['id'],
    ];
}