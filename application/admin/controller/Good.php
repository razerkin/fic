<?php
namespace app\admin\controller;
use app\admin\model\Good as GoodModel;
use app\admin\validate\Good as GoodValidate;
use app\admin\controller\Base;
use think\File;
use think\Request;

class Good extends Base
{

    protected $is_check_login = ['*'];
    public function goodList()
    {
        $data = GoodModel::paginate(3);
        $page = $data->render();
        $this->assign('data',$data);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function insertGood()
    {
        return $this->fetch();
    }

    public function goodInsert()
    {

        $data = input('post.');
        $good = new GoodModel($data);
        $file = request()->file('image');
        if ($file){
            $info = $file->validate(['size'=>3145728,'ext'=>'jpg,png,gif'])->rule('uniqid')->move('upload/goodimage');
            if($info) {
                $good['image_name'] =$info->getFileName();
             } else {
                echo $file->getError();
            }
        } else {
            $this->error('没有接收到图片文件！');
        }
        $val = new GoodValidate();
        if (!($val->check($good)))
        {
            $this->error($val->getError());
            exit;
        }
        $ret = $good->allowField(true)->save();
        if($ret)
        {
            $this->success('新增商品成功！','Good/goodlist');
        } else {
            $this->error('新增商品失败!');
        }
    }


    public function  updateGood()
    {
        $id = input('get.good_id');
        $data = GoodModel::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public  function goodUpdate()
    {
        $data = input('post.');
        $id = input('post.good_id');
        $file = request()->file('image');
        if ($file){
            $info = $file->validate(['size'=>3145728,'ext'=>'jpg,png,gif'])->rule('uniqid')->move('upload/goodimage');
            if($info) {
                $data['image_name'] =$info->getFileName();
            }
        } else {
            $data['image_name'] = $data['first_image_name'];
        }
        $val = new GoodValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $good = new GoodModel();
        $ret = $good->allowField(true)->save($data, ['good_id' => $id]);
        if($ret)
        {
            $this->success('修改商品成功！','Good/goodlist');
        } else {
            $this->error('修改商品失败!');
        }
    }

    public  function goodDelete()
    {
        $id = input('get.good_id');
//        $image = input('get.image_name');
//       $ret1 = unlink('upload/goodimage/'.$image);

        $ret = GoodModel::destroy($id);
        if($ret)
        {
            $this->success('删除商品信息成功！','Good/goodlist');
        } else {
            $this->error('删除商品信息失败!');
        }
    }







}
