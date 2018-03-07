<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/3/2
 * Time: 15:54
 */
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Chase as ChaseModel;
use app\admin\model\Supplier;
use app\admin\validate\Chase as ChaseValidate;
use app\admin\model\Supplier as SupplierModel;
use app\admin\validate\Supplier as SupplierValidate;
use app\admin\model\WareHouse as WareHouseModel;
use app\admin\model\Good as GoodModel;
use app\admin\model\Order as OrderModel;
use think\Db;
use app\admin\model\Point as PointModel;
use app\admin\model\PointOrder as PointOrderModel;
class Chase extends Base
{
    protected $is_check_auth = ['insertsupplier','updatesupplier','supplierupdate','supplierdelete','supplierinsert','pointnewcreate','pointneworder','pointorderconfirm','pointgenerateorder','pointchaselist','pointorderinformation','pointorderupdate','pointupdateorder','pointorderclose','newcreate','neworder','orderconfirm','generateorder','chaselist','orderinformation','orderupdate','updateorder','orderclose'];
    protected $is_check_login = ['*'];
    public function  supplierList()
    {
//        $data = UserModel::all();
//        $this->assign('data',$data);
//        return $this->fetch();
        //通过分页显示列表
        $data = SupplierModel::paginate(3);
        $page = $data->render();
        $this->assign('data',$data);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function supplierInsert()
    {
        $data = input('post.');
        $val = new SupplierValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $supplier = new SupplierModel($data);
        $ret = $supplier->allowField(true)->save();
        if($ret)
        {
            $this->success('新增供应商成功！','Chase/supplierlist');
        } else {
            $this->error('新增供应商失败!');
        }

    }
    public function  insertSupplier()
    {
        return $this->fetch();
    }
    public function  updateSupplier()
    {
        $id = input('get.supplier_id');
        $data = SupplierModel::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public  function supplierUpdate()
    {
        $data = input('post.');
        $id = input('post.supplier_id');
        $val = new SupplierValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $supplier = new SupplierModel();
        $ret = $supplier->allowField(true)->save($data,['supplier_id'=>$id]);
        if($ret)
        {
            $this->success('修改供应商信息成功！','Chase/supplierlist');
        } else {
            $this->error('修改供应商信息失败!');
        }
    }

    public  function supplierDelete()
    {
        $id = input('get.supplier_id');
        $ret = SupplierModel::destroy($id);
        if($ret)
        {
            $this->success('删除供应商信息成功！','Chase/supplierlist');
        } else {
            $this->error('删除供应商信息失败!');
        }
    }


    public  function newOrder() {
        //显示基本信息
        $orderInformation = input('post.');
        $warehouseData = WareHouseModel::get($orderInformation['warehouse_id']);
        $this->assign('warehouseData',$warehouseData);
        $SupplierData = SupplierModel::get($orderInformation['supplier_id']);
        $this->assign('supplierData',$SupplierData);
        $orderInformation['warehouse_name'] = $warehouseData['warehouse_name'];
        $orderInformation['supplier_name'] = $SupplierData['supplier_name'];
        $this->assign('orderInformationData',$orderInformation);
        //选择器查找全部商品
        $goodData = Db::name('Good')->select();
        $this->assign('goodData',$goodData);
        //获取原商品字符串
        $insertStr = input('get.insertStr');
        //获取新添加商品信息
        $insertGoodID = (int)input('post.insertGood_id');
        $insertGoodNumber = (int)input('post.insertGood_number');
//        var_dump($insertGoodID);
//        var_dump($insertGoodNumber);
        if($insertStr != '') {
//            echo ('1111');
//            echo ($insertStr);
            var_dump($insertStr);
            $insertArr = $this->my_unserialize($insertStr);
            var_dump($insertArr);
            if(array_key_exists($insertGoodID,$insertArr))
            {
                $insertArr[$insertGoodID] = (int)$insertArr[$insertGoodID] +  $insertGoodNumber;
            } else {
                $insertArr[$insertGoodID] = $insertGoodNumber;
            }
            //        显示下方商品表
            $insertIdArr = array_keys($insertArr);
            $goodData = Db::table('think_good')->where('good_id','in',$insertIdArr)->field('good_id,good_name,good_price,image_name')->select();
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
            foreach ($insertArr as $key => $value) {
                $finalData[$key]['insertNumber'] = $insertArr[$key];
//                var_dump($finalData[$key]);
                $finalData[$key]['total_fee'] = (int)$finalData[$key]['insertNumber']*(int)$finalData[$key]['good_price'];
                if ($finalData[$key]['insertNumber'] < 1) {
                    unset($finalData[$key]);
                }
            }
            $total_payment = '';
            foreach ($finalData as $keyy => $valuee) {
                $total_payment = (int)$total_payment + (int)$finalData[$keyy]['total_fee'];
            }
            $this->assign('total_payment',$total_payment);
            $this->assign('preInsertData',$finalData);
            $insertStr = $this->my_serialize($insertArr);
//            var_dump($insertStr);
            $this->assign('insertStr',$insertStr);
            $finalDataStr = $this->my_serialize($finalData);
            $this->assign('finalDataStr',$finalDataStr);
        } elseif ($insertGoodID != NULL) {
//            echo ('2222');
            $insertArr = array();
            $insertArr[$insertGoodID] = $insertGoodNumber;
//            var_dump($insertArr);
            $insertStr = $this->my_serialize($insertArr);
            $insertIdArr = array_keys($insertArr);
            $goodData = Db::table('think_good')->where('good_id','in',$insertIdArr)->field('good_id,good_name,good_price,image_name')->select();
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
            foreach ($insertArr as $key => $value) {
                $finalData[$key]['insertNumber'] = $insertArr[$key];
                $finalData[$key]['total_fee'] = (int)$finalData[$key]['insertNumber']*(int)$finalData[$key]['good_price'];
            }
            $total_payment = '';
            foreach ($finalData as $keyy => $valuee) {
                $total_payment = (int)$total_payment + (int)$finalData[$keyy]['total_fee'];
            }
            $this->assign('total_payment',$total_payment);
            $this->assign('preInsertData',$finalData);
            $this->assign('insertStr',$insertStr);
            $finalDataStr = $this->my_serialize($finalData);
            $this->assign('finalDataStr',$finalDataStr);
        }   else    {
//            echo ('333');
            $this->assign('preInsertData',array());
            $this->assign('insertStr','');
            $this->assign('finalDataStr','');
            $this->assign('total_payment','');
        }
        return $this->fetch();

    }

    public  function newCreate() {
        $warehouseData = Db::name('ware_house')->select();
        $this->assign('warehouseData',$warehouseData);
        $SupplierData = Db::name('Supplier')->select();
        $this->assign('supplierData',$SupplierData);
        return $this->fetch();
    }

    public function orderConfirm () {
        //获取上一页订单信息
        $orderBaseInformation = input('post.');
        $orderGoodDataStr = input('get.orderGoodDataStr');
        $orderGoodDataArr = $this->my_unserialize($orderGoodDataStr);
        $this->assign('orderGoodDataStr',$orderGoodDataStr);
        $this->assign('orderBaseInformation',$orderBaseInformation);
        $this->assign('orderGoodData',$orderGoodDataArr);
        return $this->fetch();
    }
    public function generateOrder(){
        $order_id = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $orderData = input('post.');
        $orderGoodData = input('post.order_gooddata');
        $orderData['order_id'] = $order_id;
        $orderData['order_gooddata'] = $orderGoodData;
        var_dump($orderData);
       $order = new OrderModel($orderData);
        $ret = $order->allowField(true)->save();
        if($ret)
        {
            $this->success('新增采购订单成功！','Chase/chaselist');
        } else {
            $this->error('新增采购订单失败!');
        }
    }

    public function chaseList() {
        $condition = input('get.condition');
        if($condition != '' or $condition != NULL) {
            $condition = $condition.'%';
            $condition = '%'.$condition;
            var_dump($condition);
            $orderData = OrderModel::where('order_id','like',$condition)->whereOr('order_shipping_code','like',$condition)->paginate(8);
            $page = $orderData->render();
            $this->assign('page',$page);
            $this->assign('orderData',$orderData);
        } else {
            $orderData = OrderModel::paginate(3);
            $page = $orderData->render();
            $this->assign('page',$page);
            $this->assign('orderData',$orderData);
        }
        return $this->fetch();


    }

    public function orderInformation(){
        $orderId = input('get.order_id');
        $orderData = OrderModel::get($orderId);
        $orderGoodDataStr = $orderData['order_gooddata'];
        $orderGoodData = $this->my_unserialize($orderGoodDataStr);
        $this->assign('orderBaseInformation',$orderData);
        $this->assign('orderGoodData',$orderGoodData);
        return $this->fetch();
    }

    public function orderUpdate(){
        $orderId = input('get.order_id');
        $orderData = OrderModel::get($orderId);
        if ($orderData['order_status'] =='3') {
            $this->error('订单已完成,无法修改');
        } elseif ($orderData['order_status'] == '4') {
            $this->error('订单已关闭,无法修改');
        }
        $orderGoodDataStr = $orderData['order_gooddata'];
        $orderGoodData = $this->my_unserialize($orderGoodDataStr);
        $this->assign('orderBaseInformation',$orderData);
        $this->assign('orderGoodData',$orderGoodData);
        $this->assign('orderGoodDataStr',$orderGoodDataStr);
        return $this->fetch();
    }


    public function updateOrder() {
        $orderId = input('post.order_id');
        $orderData = input('post.');
        $order = new OrderModel();

        if ($orderData['order_status'] == '1') {
            $order['payment_time'] = date('Y-m-d H:i:s',time());
        }
        if ($orderData['order_status'] == '3') {
            $order['end_time'] = date('Y-m-d H:i:s',time());
            $orderGoodData = $this->my_unSerialize($orderData['orderGoodDataStr']);
            //取仓库库存
            $stock = $this->getWarehouseStock($orderData['order_warehouse_name']);
            if ($stock != NULL && $stock != '') {
                foreach ($orderGoodData as $key => $value) {
                    if (array_key_exists($orderGoodData[$key]['good_id'], $stock)) {
                        $stock[$orderGoodData[$key]['good_id']] = $stock[$orderGoodData[$key]['good_id']] + $orderGoodData[$key]['insertNumber'];
                    } else {
                        $stock[$orderGoodData[$key]['good_id']] = $orderGoodData[$key]['insertNumber'];
                    }
                }
                $stockStr = serialize($stock);
                $Warehouse = new WareHouseModel();
                $WarehouseRet =  $Warehouse->save(['warehouse_stock' => $stockStr],['warehouse_name' => $orderData['order_warehouse_name']]);
                if (!$WarehouseRet) {
                    $this->error('库存更新失败');
                }
                } else {
                foreach ($orderGoodData as $keyy =>$valuee){
                    $stock[$orderGoodData[$keyy]['good_id']] = $orderGoodData[$keyy]['insertNumber'];
                }
                $stockStr = serialize($stock);
                $Warehouse = new WareHouseModel();
                $WarehouseRet =  $Warehouse->save(['warehouse_stock' => $stockStr],['warehouse_name' => $orderData['order_warehouse_name']]);
                if (!$WarehouseRet) {
                    $this->error('库存更新失败');
                }
            }
        }

        $ret = $order->allowField(true)->save($orderData,['order_id'=>$orderId]);
        if($ret)
        {
            $this->success('修改订单信息成功！','Chase/chaselist');
        } else {
            $this->error('修改订单信息失败!');
        }
    }

    public function orderClose()
    {
        $orderId = input('get.order_id');
        $orderData = input('get.order_status');
        $order = new OrderModel();
        $orderStatus = Db::name('point_order')->where('order_id', $orderId)->find();
        if ($orderStatus['order_status'] != '3' && $orderStatus['order_status'] != '4') {
            if ($orderData == '4') {
                $order['close_time'] = date('Y-m-d H:i:s', time());
            }
            $ret = $order->allowField(true)->save(['order_status' => '4'], ['order_id' => $orderId]);
            if ($ret) {
                $this->success('关闭订单成功！', 'Chase/chaselist');
            } else {
                $this->error('关闭订单失败!');
            }
        } else {
            $this->error('该订单状态无法更改');
        }

    }




    public function getWarehouseStock($name){
        $WareHouse = new WareHouseModel();
        $WareHouseData = $WareHouse::get(['warehouse_name' => $name]);
        $stockArr = NULL;
        if ($WareHouseData->warehouse_stock != NULL && $WareHouseData->warehouse_stock != '') {
            $stockArr = unserialize($WareHouseData->warehouse_stock);
        }
        return $stockArr;
    }

    public function pointNewCreate() {
        $warehouseData = Db::name('ware_house')->select();
        $this->assign('warehouseData',$warehouseData);
        $pointData = Db::name('point')->select();
        $this->assign('pointData',$pointData);
        return $this->fetch();
    }

    public function pointOrderConfirm(){
        //获取上一页订单信息
        $orderBaseInformation = input('post.');
        $orderGoodDataStr = input('get.orderGoodDataStr');
        $orderGoodDataArr = $this->my_unserialize($orderGoodDataStr);
        $this->assign('orderGoodDataStr',$orderGoodDataStr);
        $this->assign('orderBaseInformation',$orderBaseInformation);
        $this->assign('orderGoodData',$orderGoodDataArr);
        return $this->fetch();
    }

    public  function pointNewOrder() {
        //显示基本信息
        $orderInformation = input('post.');
        $warehouseData = WareHouseModel::get($orderInformation['warehouse_id']);
        $this->assign('warehouseData',$warehouseData);
        $warehouseStock = unserialize($warehouseData['warehouse_stock']);
        $warehouseStockId = array_keys($warehouseStock);

        $pointData = PointModel::get($orderInformation['point_id']);
        $this->assign('pointData',$pointData);
        $orderInformation['warehouse_name'] = $warehouseData['warehouse_name'];
        $orderInformation['point_name'] = $pointData['point_name'];
        $this->assign('orderInformationData',$orderInformation);
        //选择器查找库存的商品
        $goodData = GoodModel::all($warehouseStockId);
        foreach ($goodData as $kkey => $vvalue){
            if (array_key_exists($goodData[$kkey]['good_id'],$warehouseStock)){
                $goodData[$kkey]['stock_number'] = $warehouseStock[$goodData[$kkey]['good_id']];
            }
        }
        $this->assign('goodData',$goodData);
        //获取原商品字符串
        $insertStr = input('get.insertStr');
        //获取新添加商品信息
        $insertGoodID = (int)input('post.insertGood_id');
        $insertGoodNumber = (int)input('post.insertGood_number');
//        var_dump($insertGoodID);
//        var_dump($insertGoodNumber);
        if($insertStr != '') {
//            echo ('1111');
//            echo ($insertStr);
            var_dump($insertStr);
            $insertArr = $this->my_unserialize($insertStr);
            var_dump($insertArr);
            if(array_key_exists($insertGoodID,$insertArr))
            {
                $insertArr[$insertGoodID] = (int)$insertArr[$insertGoodID] +  $insertGoodNumber;
            } else {
                $insertArr[$insertGoodID] = $insertGoodNumber;
            }
            //        显示下方商品表
            $insertIdArr = array_keys($insertArr);
            $goodData = Db::table('think_good')->where('good_id','in',$insertIdArr)->field('good_id,good_name,good_price,image_name')->select();
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
            foreach ($insertArr as $key => $value) {
                $finalData[$key]['insertNumber'] = $insertArr[$key];
//                var_dump($finalData[$key]);
                $finalData[$key]['total_fee'] = (int)$finalData[$key]['insertNumber']*(int)$finalData[$key]['good_price'];
                if ($finalData[$key]['insertNumber'] < 1) {
                    unset($finalData[$key]);
                }
            }
            $total_payment = '';
            foreach ($finalData as $keyy => $valuee) {
                $total_payment = (int)$total_payment + (int)$finalData[$keyy]['total_fee'];
            }
            $this->assign('total_payment',$total_payment);
            $this->assign('preInsertData',$finalData);
            $insertStr = $this->my_serialize($insertArr);
//            var_dump($insertStr);
            $this->assign('insertStr',$insertStr);
            $finalDataStr = $this->my_serialize($finalData);
            $this->assign('finalDataStr',$finalDataStr);
        } elseif ($insertGoodID != NULL) {
//            echo ('2222');
            $insertArr = array();
            $insertArr[$insertGoodID] = $insertGoodNumber;
//            var_dump($insertArr);
            $insertStr = $this->my_serialize($insertArr);
            $insertIdArr = array_keys($insertArr);
            $goodData = Db::table('think_good')->where('good_id','in',$insertIdArr)->field('good_id,good_name,good_price,image_name')->select();
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
            foreach ($insertArr as $key => $value) {
                $finalData[$key]['insertNumber'] = $insertArr[$key];
                $finalData[$key]['total_fee'] = (int)$finalData[$key]['insertNumber']*(int)$finalData[$key]['good_price'];
            }
            $total_payment = '';
            foreach ($finalData as $keyy => $valuee) {
                $total_payment = (int)$total_payment + (int)$finalData[$keyy]['total_fee'];
            }
            $this->assign('total_payment',$total_payment);
            $this->assign('preInsertData',$finalData);
            $this->assign('insertStr',$insertStr);
            $finalDataStr = $this->my_serialize($finalData);
            $this->assign('finalDataStr',$finalDataStr);
        }   else    {
//            echo ('333');
            $this->assign('preInsertData',array());
            $this->assign('insertStr','');
            $this->assign('finalDataStr','');
            $this->assign('total_payment','');
        }
        return $this->fetch();

    }

    public function pointGenerateOrder(){
        $order_id = '2'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $orderData = input('post.');
        $orderGoodData = input('post.order_gooddata');
        $orderData['order_id'] = $order_id;
        $orderData['order_gooddata'] = $orderGoodData;
        $order = new PointOrderModel($orderData);
        $ret = $order->allowField(true)->save();
        if($ret)
        {
            $this->success('新增采购订单成功！','Chase/pointchaselist');
        } else {
            $this->error('新增采购订单失败!');
        }
    }

    public function pointChaseList() {
        $condition = input('get.condition');
        if($condition != '' or $condition != NULL) {
            $condition = $condition.'%';
            $condition = '%'.$condition;
            var_dump($condition);
            $orderData = PointOrderModel::where('order_id','like',$condition)->whereOr('order_shipping_code','like',$condition)->paginate(8);
            $page = $orderData->render();
            $this->assign('page',$page);
            $this->assign('orderData',$orderData);
        } else {
            $orderData = PointOrderModel::paginate(3);
            $page = $orderData->render();
            $this->assign('page',$page);
            $this->assign('orderData',$orderData);
        }
        return $this->fetch();


    }

    public function pointOrderInformation(){
        $orderId = input('get.order_id');
        $orderData = PointOrderModel::get($orderId);
        $orderGoodDataStr = $orderData['order_gooddata'];
        $orderGoodData = $this->my_unserialize($orderGoodDataStr);
        $this->assign('orderBaseInformation',$orderData);
        $this->assign('orderGoodData',$orderGoodData);
        return $this->fetch();
    }


    public function pointOrderUpdate(){
        $orderId = input('get.order_id');
        $orderData = PointOrderModel::get($orderId);
        if ($orderData['order_status'] =='3') {
            $this->error('订单已完成,无法修改');
        } elseif ($orderData['order_status'] == '4') {
            $this->error('订单已关闭,无法修改');
        }
        $orderGoodDataStr = $orderData['order_gooddata'];
        $orderGoodData = $this->my_unserialize($orderGoodDataStr);
        $this->assign('orderBaseInformation',$orderData);
        $this->assign('orderGoodData',$orderGoodData);
        $this->assign('orderGoodDataStr',$orderGoodDataStr);
        return $this->fetch();
    }



    public function pointUpdateOrder() {
        $orderId = input('post.order_id');
        $orderData = input('post.');
        $order = new PointOrderModel();
        if ($orderData['order_status'] == '1') {
            $order['payment_time'] = date('Y-m-d H:i:s',time());
        }
        if ($orderData['order_status'] == '3') {
            $order['end_time'] = date('Y-m-d H:i:s',time());
            $orderGoodData = $this->my_unSerialize($orderData['orderGoodDataStr']);
            //取仓库库存
            $pointStock = $this->getPointStock($orderData['order_point_name']);
            if ($pointStock != NULL && $pointStock != '') {
                foreach ($orderGoodData as $key => $value) {
                    if (array_key_exists($orderGoodData[$key]['good_id'], $pointStock)) {
                        $pointStock[$orderGoodData[$key]['good_id']] = $pointStock[$orderGoodData[$key]['good_id']] + $orderGoodData[$key]['insertNumber'];
                    } else {
                        $pointStock[$orderGoodData[$key]['good_id']] = $orderGoodData[$key]['insertNumber'];
                    }
                }
                $stockStr = serialize($pointStock);
                $point = new PointModel();
                $pointRet =  $point->save(['point_stock' => $stockStr],['point_name' => $orderData['order_point_name']]);
                if (!$pointRet) {
                    $this->error('库存更新失败');
                }
            } else {
                foreach ($orderGoodData as $keyy =>$valuee){
                    $pointStock[$orderGoodData[$keyy]['good_id']] = $orderGoodData[$keyy]['insertNumber'];
                }
                $stockStr = serialize($pointStock);
                $point = new PointModel();
                $pointRet =  $point->save(['point_stock' => $stockStr],['point_name' => $orderData['order_point_name']]);
                if (!$pointRet) {
                    $this->error('库存更新失败');
                }
            }
        }
        if ($orderData['order_status'] == '2') {
            $orderGoodData = $this->my_unSerialize($orderData['orderGoodDataStr']);
            //取仓库库存
            $stock = $this->getWarehouseStock($orderData['order_warehouse_name']);
            if ($stock != NULL && $stock != '') {
                foreach ($orderGoodData as $keey => $value) {
                    if (array_key_exists($orderGoodData[$keey]['good_id'], $stock)) {
                        if($stock[$orderGoodData[$keey]['good_id']] >= $orderGoodData[$keey]['insertNumber']){
                            //减少库存
                            $stock[$orderGoodData[$keey]['good_id']] = $stock[$orderGoodData[$keey]['good_id']] - $orderGoodData[$keey]['insertNumber'];
                        } else {
                            $this->error('仓库库存不足');
                        }
                    }
                }
                $stockStr = serialize($stock);
                $Warehouse = new WareHouseModel();
                $WarehouseRet =  $Warehouse->save(['warehouse_stock' => $stockStr],['warehouse_name' => $orderData['order_warehouse_name']]);
                if (!$WarehouseRet) {
                    $this->error('库存更新失败');
                }
            } else {
                foreach ($orderGoodData as $keyya =>$valueea){
                    $stock[$orderGoodData[$keyya]['good_id']] = $orderGoodData[$keyya]['insertNumber'];
                }
                $stockStr = serialize($stock);
                $Warehouse = new WareHouseModel();
                $WarehouseRet =  $Warehouse->save(['warehouse_stock' => $stockStr],['warehouse_name' => $orderData['order_warehouse_name']]);
                if (!$WarehouseRet) {
                    $this->error('库存更新失败');
                }
            }
        }
        $ret = $order->allowField(true)->save($orderData,['order_id'=>$orderId]);
        if($ret)
        {
            $this->success('修改订单信息成功！','Chase/pointchaselist');
        } else {
            $this->error('修改订单信息失败!');
        }
    }

    public function getPointStock($name){
        $Point = new PointModel();
        $PointData = $Point::get(['point_name' => $name]);
        $stockArr = NULL;
        if ($PointData->point_stock != NULL && $PointData->point_stock != '') {
            $stockArr = unserialize($PointData->point_stock);
        }
        return $stockArr;
    }


    public function pointOrderClose() {
        $orderId = input('get.order_id');
        $orderData = input('get.order_status');
        $order = new PointOrderModel();
        $orderStatus = Db::name('order')->where('order_id',$orderId)->find();
        if ($orderStatus['order_status'] != '3' && $orderStatus['order_status'] != '4'){
            if ($orderData == '4') {
                $order['close_time'] = date('Y-m-d H:i:s',time());
            }
            $ret = $order->allowField(true)->save(['order_status'=>'4'],['order_id'=>$orderId]);
            if($ret)
            {
                $this->success('关闭订单成功！','Chase/pointchaselist');
            } else {
                $this->error('关闭订单失败!');
            }
        } else {
            $this->error('该订单状态无法更改');
        }

    }











}