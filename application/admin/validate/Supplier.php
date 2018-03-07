<?php
namespace app\admin\validate;
use think\Validate;

class Supplier extends Validate
{
    protected  $rule = [
        'supplier_name|公司名称' =>'require|min:3',
        'supplier_address|公司地址' =>'require',
        'supplier_mail|邮箱' =>'require',
        'supplier_phone|联系电话' =>'require|min:6',

    ];
    protected  $message = [
        'supplier_name.require' =>'公司名不能为空',
        'supplier_name.min' => '公司名不能少于3位',
        'supplier_address.require' => '地址不能为空',
        'supplier_phone.require' => '联系电话不能为空',
        'supplier_phone.min' =>'联系电话电话不能少于6位',
        'supplier_mail.require' =>'邮箱不能为空',

    ];
}


