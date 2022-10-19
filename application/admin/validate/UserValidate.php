<?php


namespace app\admin\validate;


use think\Validate;

class UserValidate extends Validate {

    protected $rule = [

        'id'               =>            'require',

    ];

    protected $message = [

        'id.require'       =>            '系统错误',

    ];


    protected $scene = [

        'edit'             =>            ['id'],

    ];

}