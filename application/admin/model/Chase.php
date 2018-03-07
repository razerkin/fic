<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/3/2
 * Time: 15:56
 */


namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;


class Chase extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';



}