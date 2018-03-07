<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/1/25
 * Time: 2:03
 */
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;


class Point extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';



}
