<?php


namespace app\admin\validate;


use think\Validate;

class ArticleValidate extends Validate {

    protected $rule = [

        'id'               =>            'require',
        'status'           =>            'require',

    ];

    protected $message = [

        'id.require'       =>            '系统错误',
        'status.require'       =>            '系统错误',

    ];


    protected $scene = [

        'edit'             =>            ['id','status'],

    ];

}