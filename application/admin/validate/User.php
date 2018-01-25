<?php
namespace app\admin\validate;
use think\Validate;

class User extends Validate
{
    protected  $rule = [
        'name|用户名' =>'require|min:3',
        'password|密码' =>'require|min:6|confirm:repassword',
        'email|邮箱' =>'require',

    ];
    protected  $message = [
        'name.require' =>'用户名不能为空',
        'name.min' => '用户名不能少于3位',
        'password.require' => '密码不能为空',
        'password.min' => '密码不能少于6位',
        'password.confirm' =>'两次密码不一致',
        'email.require' =>'邮箱不能为空',

    ];
}


