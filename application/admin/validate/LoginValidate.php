<?php


namespace app\admin\validate;

use think\Validate;

class LoginValidate extends Validate

{

    protected $rule = [

        'account'          =>            'require',
        'pass'             =>            'require',
        'verify'           =>            'require',

    ];

    protected $message = [

        'account.require'  =>            '请输入用户名',
        'pass.require'     =>            '请输入密码',
        'verify.require'   =>            '请输入验证码',

    ];


    protected $scene = [

        'login'            =>            ['account' ,'pass' , 'verity'],

    ];

}