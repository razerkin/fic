<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/1/23
 * Time: 15:53
 */
namespace app\admin\validate;
use think\Validate;

class Good extends Validate
{
    protected  $rule = [
        'good_name|商品名称' =>'require',
        'image_name|图片' =>'require',
        'good_price|价格' =>'require|number',

    ];
    protected  $message = [
        'good_name.require' =>'商品名称不能为空',
        'image_name.require' =>'请上传图片',
        'good_price.require' =>'价格不能为空',
        'good_price.number' =>'价格必须为数字',

    ];
}