<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/1/25
 * Time: 1:42
 */
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Point as PointModel;
use app\admin\validate\Point as PointValidate;
use think\Db;
class Point extends Base
{
    protected $is_check_auth = ['pointinsert','insertpoint','updatepoint','pointupdate','pointdelete','sellpointinsert'];
    protected $is_check_login = ['*'];
    public function pointList() {
        $data = Db::table('think_point')->distinct(true)->order('point_province')->field('point_province')->select();

        $this->assign('data',$data);

        return $this->fetch();
    }
    public function cityList() {
        $province = input('get.point_province');
        $data = Db::table('think_point')->distinct(true)->where('point_province',$province)->order('point_city')->field('point_city')->select();
        $this->assign('province',$province);
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function prefectureList() {
        $city = input('get.point_city');
        $province = input('get.point_province');
        $data = Db::table('think_point')->distinct(true)->where('point_province',$province)->where('point_city',$city)->order('point_prefecture')->field('point_prefecture')->select();
        $this->assign('province',$province);
        $this->assign('city',$city);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function pointInsert() {
     $data = input('post.');
     $val = new PointValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $wareHouse = new PointModel($data);
        $ret = $wareHouse->allowField(true)->save();
        if($ret)
        {
            $this->success('新增分销点成功！','point/pointlist');
        } else {
            $this->error('新增分销点失败!');
        }



    }

    public function insertPoint() {
        return $this->fetch();
    }
    public function updatePoint() {
        $id = input('get.id');
        $data = PointModel::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function pointUpdate() {
        $data = input('post.');
        $id = input('post.point_id');
        $val = new PointValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $warehouse = new PointModel();
        $ret = $warehouse->allowField(true)->save($data,['point_id'=>$id]);
        if($ret)
        {
            $this->success('修改分销点信息成功！','point/pointlist');
        } else {
            $this->error('修改分销点信息失败!');
        }
    }

    public function pointDelete() {
        $id = input('get.id');
        $ret = PointModel::destroy($id,true);
        if($ret)
        {
            $this->success('删除分销点信息成功！','point/pointlist');
        } else {
            $this->error('删除分销点信息失败!');
        }
    }

    public function sellPointList () {
//        //选择器查找全部商品
//        $data = Db::name('Good')->select();
//        $this->assign('data',$data);
//
//        //显示上方库存列表
//       $wareHouseID = input('get.warehouse_id');
//       $this->assign('warehouse_id',$wareHouseID);
//        $wareHouseInfo = PointModel::get($wareHouseID);
//        $wareHouseStock = $wareHouseInfo['warehouse_stock'];
//        $wareHouseStock = unserialize($wareHouseStock);
//        $good_id = array_keys($wareHouseStock);
//        $goodData = Db::name('Good')->where('good_id','in',$good_id)->select();
//        $newData = array();
//        foreach ($goodData as $k =>$v)
//        {
//
//            $newData[$k]=$goodData[$k]['good_id'];
//        }
////        $newData =array_flip($newData);
//        $finalData = array();
//        foreach ($goodData as $a => $b){
//            if ($newData[$a]===$goodData[$a]['good_id']) {
//                $finalData[$newData[$a]] = $goodData[$a];
//            }
//        }
//        foreach ($wareHouseStock as $key => $value) {
//            $finalData[$key]['stocknumber'] = $wareHouseStock[$key];
//        }
//        $this->assign('stockdata',$finalData);
//        return $this->fetch();

        $prefecture = input('get.point_prefecture');
        $city = input('get.point_city');
        $province = input('get.point_province');
        $data = Db::table('think_point')->distinct(true)->where('point_province',$province)->where('point_city',$city)->where('point_prefecture',$prefecture)->order('point_id')->select();
        $this->assign('data',$data);
        return $this->fetch();

    }

    public function sellPointInsert() {
        $good_id = input('post.good_id');
        $insertNumber = input('post.insertNumber');
        $wareHouseID = input('post.warehouse_id');
        $wareHouseInfo = PointModel::get($wareHouseID);
        $wareHouseStock = $wareHouseInfo['warehouse_stock'];
        if ($wareHouseStock===NULL) {
            $wareHouseStockAry[$good_id] = $insertNumber;
            $wareHouseStockStr = serialize($wareHouseStockAry);
            $warehouse = new PointModel();
            $ret = $warehouse->save(['warehouse_stock' => $wareHouseStockStr],['warehouse_id'=>$wareHouseID]);
            if($ret)
            {
                $this->success('增加库存成功！','ware_house/stocklist');
            } else {
                $this->error('增加库存失败!');
            }

        } else {
            $wareHouseStock = unserialize($wareHouseStock);
            if (!array_key_exists($good_id,$wareHouseStock)) {
                $wareHouseStock[$good_id] = $insertNumber;
            } else {
                $wareHouseStock[$good_id] = (int)$wareHouseStock[$good_id]+ (int)$insertNumber;
            }
            $wareHouseStock = serialize($wareHouseStock);
            $warehouse = new PointModel();
            $ret = $warehouse->save(['warehouse_stock' => $wareHouseStock],['warehouse_id'=>$wareHouseID]);
            if($ret)
            {
                $this->success('增加库存成功！','ware_house/warehouselist');
            } else {
                $this->error('增加库存失败!');
            }
        }
    }

    public function sellPointReduce() {
        $good_id = input('post.reduceid');
        $reduceNumber = input('post.reduceNumber');
        $wareHouseID = input('post.warehouse_id');
        $wareHouseInfo = PointModel::get($wareHouseID);
        $wareHouseStock = $wareHouseInfo['warehouse_stock'];
        $wareHouseStock = unserialize($wareHouseStock);
        $wareHouseStock[$good_id] = (int)$wareHouseStock[$good_id]-(int)$reduceNumber;
        $wareHouseStock = serialize($wareHouseStock);
        $warehouse = new PointModel();
        $ret = $warehouse->save(['warehouse_stock' => $wareHouseStock],['warehouse_id'=>$wareHouseID]);
        if($ret)
            {
                $this->success('商品出库成功！','ware_house/warehouselist');
            } else {
                $this->error('商品出库失败!');
            }
        }

    public function pointStockList () {
        //选择器查找全部商品
        $data = Db::name('Good')->select();
        $this->assign('data',$data);

        //显示上方库存列表
        $pointID = input('get.point_id');
        $this->assign('point_id',$pointID);
        $pointInfo = PointModel::get($pointID);
        $pointStock = $pointInfo['point_stock'];
        if ($pointStock != NULL && $pointStock != ''){
            $pointStock = unserialize($pointStock);
            $good_id = array_keys($pointStock);
            $goodData = Db::name('Good')->where('good_id','in',$good_id)->select();
            $newData = array();
            foreach ($goodData as $k =>$v)
            {

                $newData[$k]=$goodData[$k]['good_id'];
            }
//        $newData =array_flip($newData);
            $finalData = array();
            foreach ($goodData as $a => $b){
                if ($newData[$a]===$goodData[$a]['good_id']) {
                    $finalData[$newData[$a]] = $goodData[$a];
                }
            }
            foreach ($pointStock as $key => $value) {
                $finalData[$key]['stocknumber'] = $pointStock[$key];
            }
            $this->assign('stockdata',$finalData);
            return $this->fetch();
        } else {
            $this->error('该分销点库存为空');
        }


    }


    public function stockReduce() {
        $good_id = input('post.reduceid');
        $reduceNumber = input('post.reduceNumber');
        $pointID = input('post.point_id');
        $pointInfo = PointModel::get($pointID);
        $pointStock = $pointInfo['warehouse_stock'];
        $pointStock = unserialize($pointStock);
        $pointStock[$good_id] = (int)$pointStock[$good_id]-(int)$reduceNumber;
        $pointStock = serialize($pointStock);
        $point = new PointModel();
        $ret = $point->save(['point_stock' => $pointStock],['point_id'=>$pointID]);
        if($ret)
        {
            $this->success('商品出库成功！','point/pointlist');
        } else {
            $this->error('商品出库失败!');
        }
    }






}
