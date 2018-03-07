<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/1/25
 * Time: 1:42
 */
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\WareHouse as WareHouseModel;
use app\admin\validate\WareHouse as WareHouseValidate;
use think\Db;
class WareHouse extends Base
{
    protected $is_check_auth = ['warehouseinsert','insertwarehouse','updatewarehouse','warehousedelete'];
    protected $is_check_login = ['*'];
    public function wareHouseList() {
        $data = WareHouseModel::paginate(3);
        $page = $data->render();
        $this->assign('data',$data);
        $this->assign('page',$page);
        return $this->fetch();
    }


    public function wareHouseInsert() {
     $data = input('post.');
     $val = new WareHouseValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $wareHouse = new WareHouseModel($data);
        $ret = $wareHouse->allowField(true)->save();
        if($ret)
        {
            $this->success('新增仓库成功！','ware_house/warehouselist');
        } else {
            $this->error('新增仓库失败!');
        }



    }

    public function insertWareHouse() {
        return $this->fetch();
    }
    public function updateWareHouse() {
        $id = input('get.warehouse_id');
        $data = WareHouseModel::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function wareHouseUpdate() {
        $data = input('post.');
        $id = input('post.warehouse_id');
        $val = new WareHouseValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $warehouse = new WareHouseModel();
        $ret = $warehouse->allowField(true)->save($data,['warehouse_id'=>$id]);
        if($ret)
        {
            $this->success('修改仓库信息成功！','ware_house/warehouselist');
        } else {
            $this->error('修改仓库信息失败!');
        }
    }

    public function wareHouseDelete() {
        $id = input('get.warehouse_id');
        $ret = WareHouseModel::destroy($id);
        if($ret)
        {
            $this->success('删除仓库信息成功！','ware_house/warehouselist');
        } else {
            $this->error('删除仓库信息失败!');
        }


    }

    public function stockList () {
        //选择器查找全部商品
        $data = Db::name('Good')->select();
        $this->assign('data',$data);

        //显示上方库存列表
       $wareHouseID = input('get.warehouse_id');
       $this->assign('warehouse_id',$wareHouseID);
        $wareHouseInfo = WareHouseModel::get($wareHouseID);
        $wareHouseStock = $wareHouseInfo['warehouse_stock'];
        if ($wareHouseStock != NULL || $wareHouseStock != '') {
            $wareHouseStock = unserialize($wareHouseStock);
            $good_id = array_keys($wareHouseStock);
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
            foreach ($wareHouseStock as $key => $value) {
                $finalData[$key]['stocknumber'] = $wareHouseStock[$key];
            }
            $this->assign('stockdata',$finalData);
            return $this->fetch();
        } else {
            $this->error('该仓库库存为空');
        }


    }

    public function stockInsert() {
        $good_id = input('post.good_id');
        $insertNumber = input('post.insertNumber');
        $wareHouseID = input('post.warehouse_id');
        $wareHouseInfo = WareHouseModel::get($wareHouseID);
        $wareHouseStock = $wareHouseInfo['warehouse_stock'];
        if ($wareHouseStock===NULL) {
            $wareHouseStockAry[$good_id] = $insertNumber;
            $wareHouseStockStr = serialize($wareHouseStockAry);
            $warehouse = new WareHouseModel();
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
            $warehouse = new WareHouseModel();
            $ret = $warehouse->save(['warehouse_stock' => $wareHouseStock],['warehouse_id'=>$wareHouseID]);
            if($ret)
            {
                $this->success('增加库存成功！','ware_house/warehouselist');
            } else {
                $this->error('增加库存失败!');
            }
        }
    }

    public function stockReduce() {
        $good_id = input('post.reduceid');
        $reduceNumber = input('post.reduceNumber');
        $wareHouseID = input('post.warehouse_id');
        $wareHouseInfo = WareHouseModel::get($wareHouseID);
        $wareHouseStock = $wareHouseInfo['warehouse_stock'];
        $wareHouseStock = unserialize($wareHouseStock);
        $wareHouseStock[$good_id] = (int)$wareHouseStock[$good_id]-(int)$reduceNumber;
        $wareHouseStock = serialize($wareHouseStock);
        $warehouse = new WareHouseModel();
        $ret = $warehouse->save(['warehouse_stock' => $wareHouseStock],['warehouse_id'=>$wareHouseID]);
        if($ret)
            {
                $this->success('商品出库成功！','ware_house/warehouselist');
            } else {
                $this->error('商品出库失败!');
            }
        }










}
