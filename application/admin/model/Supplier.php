<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/3/2
 * Time: 16:46
 */
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;


class Supplier extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';



}