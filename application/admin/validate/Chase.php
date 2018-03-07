<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/3/2
 * Time: 15:57
 */
namespace app\admin\validate;
use think\Validate;

class Chase extends Validate
{
    protected $rule = [
        'point_name|区域名称' => 'require|min:3',
        'point_phone|负责人电话' => 'require|min:8',
        'point_address|地址' => 'require',
        'point_manager|负责人名称' => 'require',
        'point_mail|负责人邮箱' => 'require',
        'point_province|省' => 'require',
        'point_city|市' => 'require',
        'point_prefecture|区' => 'require',

    ];
    protected $message = [
        'point_name.require' => '区域名称不能为空',
        'point_name.min' => '区域名称不能少于3位',
        'point_phone.require' => '负责人电话不能为空',
        'point_phone.min' => '负责人电话不能少于8位',
        'point_address.require' => '负责人地址不能为空',
        'point_manager.require' => '负责人名称不能为空',
        'point_mail.require' => '负责人邮箱不能为空',
        'point_province.require' => '省级地址不能为空',
        'point_city.require' => '市级地址不能为空',
        'point_prefecture.require' => '区级地址不能为空',

    ];


}