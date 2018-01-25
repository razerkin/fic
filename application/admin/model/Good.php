<?php
namespace app\admin\model;
use think\Model;
use traits\model\SoftDelete;


class Good extends Model
{
    use SoftDelete;
    protected static $deleteTime = 'delete_time';





}



