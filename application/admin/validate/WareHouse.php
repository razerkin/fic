<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/1/25
 * Time: 2:14
 */
namespace app\admin\validate;
use think\Validate;

class WareHouse extends Validate {
    protected  $rule = [
        'warehouse_name|仓库名称' =>'require|min:3',
        'warehouse_phone|仓库电话' =>'require|min:8',
        'warehouse_address|仓库地址' =>'require',

    ];
    protected  $message = [
        'warehouse_name.require' =>'仓库名称不能为空',
        'warehouse_name.min' => '仓库名称不能少于3位',
        'warehouse_phone.require' => '仓库电话不能为空',
        'warehouse_phone.min' => '仓库电话不能少于8位',
        'warehouse_address.require' =>'仓库地址不能为空',

    ];





}