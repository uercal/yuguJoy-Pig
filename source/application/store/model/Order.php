<?php

namespace app\store\model;

use app\common\model\Order as OrderModel;
use think\Request;
use think\Db;
use app\common\model\OrderGoods;
use app\store\model\RentMode;
use app\store\model\Equip;
// 
use app\store\model\EquipCheckLog;
use app\store\model\EquipUsingLog;
// 
use app\store\model\Member;
use app\store\model\OrderMember;
// 
use app\api\model\Goods as GoodsApi;
use app\api\model\Order as OrderApi;
use app\common\model\Wxapp;
use think\Session;
// 
use app\store\model\Deduct;
use app\store\model\AccountMoney;


// 

/**
 * 订单管理
 * Class Order
 * @package app\store\model
 */
class Order extends OrderModel
{

    public $error;


    /**
     * 订单列表
     * @param $filter
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($filter)
    {

        $request = Request::instance();
        $map = $request->request();

        $_map = [];
        if (!empty($map['order_no'])) $_map['order_no'] = ['=', $map['order_no']];
        if (!empty($map['startDate']) && !empty($map['endDate'])) $_map['create_time'] = ['between', [strtotime($map['startDate']), strtotime($map['endDate'])]];


        $data = $this->with(['goods.image', 'address', 'user'])
            ->where($filter)
            ->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(10, false, [
                'query' => Request::instance()->request()
            ])->append(['after_status']);

        return ['data' => $data, 'map' => $map];
    }


    /**
     * getOne
     */
    public function getOne()
    {
        $request = Request::instance();
        $map = $request->request();
        $_map = [];
        if (!empty($map['order_no'])) {
            $_map['order_no'] = ['=', $map['order_no']];
        } else {
            return ['data' => [], 'map' => $map];
        }

        $data = $this->with(['goods.image', 'address', 'user'])
            ->where($_map)
            ->order(['create_time' => 'desc'])
            ->paginate(10, false, [
                'query' => Request::instance()->request()
            ])->append(['after_status']);

        return ['data' => $data, 'map' => $map];
    }

    /**
     * 确认发货
     * @param $data
     * @return bool|false|int
     */
    public function delivery($input)
    {
        if (!isset($input['equip'])) {
            $this->error = "请添加设备";
            return false;
        } else {
            if (empty($input['member_ids'])) {
                $this->error = "请添加配送员";
                return false;
            }
            // halt($input);
            $equip = $input['equip'];
            $order_id = $input['order_id'];

            $data = [];
            foreach ($equip as $key => $value) {
                $_data = [];
                $_data['equip_id'] = $key;
                $_data['secure'] = $value['secure'];
                $_data['service_ids'] = isset($value['service']) ? implode(',', $value['service']) : '';
                $_data['status'] = 20; // 已发货
                $_data['order_id'] = $order_id;
                $data[] = $_data;
            }
            // 
            $session = Session::get('yoshop_store');
            $wxapp_id = $session['wxapp']['wxapp_id'];
            $user = $session['user'];
            $type = $user['type'];
            $member_id = $type == 0 ? 0 : $user['member_id'];



            $usingLog = [];
            foreach ($data as $key => $value) {
                $_usingLog = [];
                $_usingLog['order_id'] = $value['order_id'];
                $_usingLog['equip_id'] = $value['equip_id'];
                $_usingLog['member_id'] = $member_id;
                $_usingLog['equip_status'] = $value['status'];
                $_usingLog['wxapp_id'] = $wxapp_id;
                $usingLog[] = $_usingLog;
            }



            // 员工配送记录
            $order_member = [];
            $member = [];
            $member_ids = explode(',', $input['member_ids']);
            foreach ($member_ids as $key => $value) {
                $_order_member = [];
                $_order_member['member_id'] = $value;
                $_order_member['order_id'] = $input['order_id'];
                $_order_member['status'] = 10; //进行中
                $_order_member['wxapp_id'] = $wxapp_id;
                $order_member[] = $_order_member;

                // 员工状态变更
                $_member['id'] = $value;
                $_member['status'] = 20; //配送中
                $member[] = $_member;
            }



            // 开启事务
            Db::startTrans();
            try {
                // 设备
                $equip = new Equip;
                $equip->saveAll($data);
                // 设备使用记录                
                $this->usingLog()->saveAll($usingLog);
                // 员工配送记录
                $this->orderMember()->saveAll($order_member);
                // 员工状态变更
                $member_model = new Member;
                $member_model->saveAll($member);
                // 订单
                $this->save([
                    'delivery_status' => 20,
                    'delivery_time' => time()
                ]);
                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                $this->error = $e->getMessage();
                return false;
            }
        }
    }


    /**
     * 自主新增
     */
    public function addSelf($post)
    {
        $goods_data = $post['data'];
        $order_data = $post['order'];

        // check
        // 用户检查
        $user_id = $order_data['user_id'];
        $user_obj = Db::name('user')->where('user_id', $user_id)->find();
        if (!$user_obj) return ['error' => '归属用户不存在'];
        // 


        // 商品列表
        $goodsList = [];
        $goodsIds = array_unique(array_column($goods_data, 'goods_id'));
        foreach ((new GoodsApi)->getListByIds($goodsIds) as $goods) {
            $goodsList[$goods['goods_id']] = $goods;
        }
        $intraRegion = true;
        $intraRegionError = '';
        // 购物车商品列表
        $cartList = [];
        foreach ($goods_data as $key => $cart) {
            //             
            /* @var Goods $goods */
            $goods = $goodsList[$cart['goods_id']];
            $goods['key'] = $key;
            // 规格信息            
            $goods['goods_spec_id'] = $cart['spec_obj']['goods_spec_id'];
            $goods_sku = array_column($goods['spec']->toArray(), null, 'goods_spec_id')[$cart['spec_obj']['goods_spec_id']];
            // 多规格文字内容
            $goods_sku['goods_attr'] = '';
            if ($goods['spec_type'] === 20) {
                $attrs = explode('_', $goods_sku['spec_sku_id']);
                $spec_rel = array_column($goods['spec_rel']->toArray(), null, 'spec_value_id');
                foreach ($attrs as $specValueId) {
                    $goods_sku['goods_attr'] .= $spec_rel[$specValueId]['spec']['spec_name'] . ':'
                        . $spec_rel[$specValueId]['spec_value'] . '; ';
                }
            }
            $goods['goods_sku'] = $goods_sku;
            // 商品单价
            $goods['goods_price'] = $goods_sku['goods_price'];
            // 商品租赁信息
            $rent_model = new Rentmode;
            $rent_info = $rent_model->getInfo($cart['rent_id'], $goods['goods_spec_id']);
            $goods['rent_date'] = $cart['rent_date'];
            $goods['rent_num'] = $cart['rent_num'];
            $goods['rent_info'] = $rent_info;

            if ($rent_info['is_static'] == 0) {
                $goods['rent_total_price'] = bcmul($rent_info['price'], $cart['rent_num'], 2);
                $goods['rent_price'] = $rent_info['price'];
            } else {
                $map = json_decode($rent_info['map'], true);
                foreach ($map as $key => $value) {
                    if (!isset($value['max']) && $cart['rent_num'] >= 6) {
                        // 没有max 为6+ 大于3月 先收3月  IMPORTANT
                        $goods['rent_total_price'] = bcmul($value['price'], 3, 2);
                        $goods['rent_price'] = $value['price'];
                        break;
                    } else {
                        // 
                        if ($cart['rent_num'] >= $value['min'] && $cart['rent_num'] <= $value['max']) {
                            if ($cart['rent_num'] >= 3) {
                                $goods['rent_total_price'] = bcmul($value['price'], 3, 2);
                            } else {
                                $goods['rent_total_price'] = bcmul($value['price'], $cart['rent_num'], 2);
                            }
                            $goods['rent_price'] = $value['price'];
                            break;
                        }
                    }
                }
            }

            // 保险金额与否
            $goods['secure'] = $cart['secure'];
            $goods['secure_price'] = $cart['secure'] == 0 ? 0 : $goods_sku['secure_price'];

            // 增值服务
            sort($cart['service']);
            $goods['service_ids'] = $service_ids = implode(',', $cart['service']);
            $goods['service_price'] = $service_ids == 0 ? 0 : Db::name('goods_service')->whereIn('service_id', $service_ids)->sum('service_price');
            $goods['service_info'] = $service_ids == 0 ? [] : Db::name('goods_service')->whereIn('service_id', $service_ids)->select();


            // 商品总价
            $goods['total_num'] = $cart['total_num'];
            $goods['total_price'] = $total_price = bcadd(bcadd(bcadd($goods['goods_price'], $goods['rent_total_price'], 2), $goods['secure_price'], 2), $goods['service_price'], 2);
            $goods['all_total_price'] = bcmul($total_price, $cart['total_num'], 2);
            // 商品总重量
            $goods['goods_total_weight'] = bcmul($goods['goods_sku']['goods_weight'], $cart['total_num'], 2);
            $cartList[] = $goods->toArray();
        }

        // 商品总金额
        $orderTotalPrice = array_sum(array_column($cartList, 'all_total_price'));
        // 所有商品的运费金额
        // $allExpressPrice = array_column($cartList, 'express_price');
        // 订单总运费金额
        // $expressPrice = $allExpressPrice ? Delivery::freightRule($allExpressPrice) : 0.00;        
        $expressPrice = 0.00;
        $order = [
            'goods_list' => $cartList,  // 商品列表
            'order_total_num' => $this->getTotalNum($goods_data),  // 商品总数量
            'order_total_price' => round($orderTotalPrice, 2), // 商品总金额 (不含运费)
            // 'order_pay_price' => bcadd($orderTotalPrice, $expressPrice, 2),  // 实际支付金额                 
            'order_pay_price' => bcadd($order_data['pay_price'], $expressPrice, 2),  // 实际支付金额                 
        ];

        $model = new OrderApi;

        $order['address']['name'] = $order_data['address']['name'];
        $order['address']['phone'] = $order_data['address']['phone'];
        $order['address']['detail'] = $order_data['address']['detail'];
        $order['address']['province_id'] = $order_data['region_option'][0];
        $order['address']['city_id'] = $order_data['region_option'][1];
        $order['address']['region_id'] = $order_data['region_option'][2];
        $order['address']['user_id'] = $order_data['user_id'];

        return $order;
    }



    public function add($user_id, $order)
    {
        $wxapp_id = Wxapp::detail()['wxapp_id'];
        // halt($order);
        Db::startTrans();
        // 记录订单信息
        $this->save([
            'user_id' => $user_id,
            'wxapp_id' => $wxapp_id,
            'order_no' => $this->orderNo(),
            'total_price' => $order['order_total_price'],
            'pay_price' => $order['order_pay_price']
        ]);
        // 订单商品列表
        $goodsList = [];
        // 更新商品库存 (下单减库存)
        $deductStockData = [];
        foreach ($order['goods_list'] as $goods) {
            /* @var Goods $goods */
            // 取消购物车 单一物品购买
            $goods_sku = $goods['goods_sku'];
            // 
            $goodsList[] = [
                'user_id' => $user_id,
                'wxapp_id' => $wxapp_id,
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'image_id' => $goods['image'][0]['image_id'],
                'deduct_stock_type' => $goods['deduct_stock_type'],
                'spec_type' => $goods['spec_type'],
                'spec_sku_id' => $goods_sku['spec_sku_id'],
                'goods_spec_id' => $goods_sku['goods_spec_id'],
                'content' => $goods['content'],
                'goods_price' => $goods_sku['goods_price'],
                // 
                'rent_id' => $goods['rent_info']['id'],
                'rent_price' => $goods['rent_price'],
                'rent_date' => strtotime($goods['rent_date']),
                'rent_num' => $goods['rent_num'],
                'rent_total_price' => $goods['rent_total_price'],
                'secure' => $goods['secure'],
                'secure_price' => $goods['secure_price'],
                'service_ids' => $goods['service_ids'],
                'service_price' => $goods['service_price'],
                'all_total_price' => $goods['all_total_price'],
                // 
                'total_num' => $goods['total_num'],
                'total_price' => $goods['total_price'],
            ];
        }
        // 保存订单商品信息
        $this->goods()->saveAll($goodsList);
        // 保存订单地址                
        $this->address()->save([
            'name' => $order['address']['name'],
            'phone' => $order['address']['phone'],
            'province_id' => $order['address']['province_id'],
            'city_id' => $order['address']['city_id'],
            'region_id' => $order['address']['region_id'],
            'detail' => $order['address']['detail'],
            'user_id' => $order['address']['user_id'],
            'wxapp_id' => $wxapp_id
        ]);
        // 更新商品库存        
        Db::commit();
        return true;
    }



    public function getTotalNum($goods_data)
    {
        return array_sum(array_column($goods_data, 'total_num'));
    }










    /**
     * 修改订单
     */
    public function edit($input)
    {
        // 

        if (isset($input['order']) && $input['order']['pay_price'] <= 0) {
            $this->error = "金额不能小于等于0";
            return false;
        }
        // order_goods init        
        $order_goods = [];
        foreach ($input['goods'] as $key => $value) {
            $value['order_goods_id'] = $key;
            $value['rent_date'] = strtotime($value['rent_date']);
            // 
            $rent_info = $this->getRentPriceArr($value['rent_id'], $value['rent_num'], $value['order_goods_id']);
            $value['rent_price'] = $rent_info['rent_price'];
            $value['rent_total_price'] = $rent_info['rent_total_price'];
            // 
            $order_goods[] = $value;
        }

        if (isset($input['equip'])) {
            // 
            $session = Session::get('yoshop_store');
            $wxapp_id = $session['wxapp']['wxapp_id'];
            $user = $session['user'];
            $type = $user['type'];
            $member_id = $type == 0 ? 0 : $user['member_id'];
            // 更换了设备
            $equip = $input['equip'];
            $data = [];
            $log_data = [];
            foreach ($equip as $key => $value) {
                $_data = [];
                $_data['equip_id'] = $key;
                $_data['secure'] = $value['secure'];
                $_data['service_ids'] = isset($value['service']) ? implode(',', $value['service']) : '';
                $_data['status'] = 20; // 已发货
                $_data['order_id'] = $input['order_id'];
                $data[] = $_data;
                // 
                $_data_log = [];
                $_data_log['order_id'] = $input['order_id'];
                $_data_log['equip_id'] = $key;
                $_data_log['member_id'] = $member_id;
                $_data_log['equip_status'] = 20;
                $_data_log['wxapp_id'] = $wxapp_id;
                $_data_log['create_time'] = time();
                $log_data[] = $_data_log;
                // 
                $_p_data = [];
                $_p_data['equip_id'] = $value['p_equip_id'];
                $_p_data['secure'] = null;
                $_p_data['service_ids'] = null;
                $_p_data['status'] = 10; // 在库
                $_p_data['order_id'] = null;
                $data[] = $_p_data;
                // 
                $_p_data_log = [];
                $_p_data_log['order_id'] = null;
                $_p_data_log['equip_id'] = $value['p_equip_id'];
                $_p_data_log['member_id'] = $member_id;
                $_p_data_log['equip_status'] = 10;
                $_p_data_log['wxapp_id'] = $wxapp_id;
                $_p_data_log['create_time'] = time();
                $log_data[] = $_p_data_log;
            }
        }



        // 开启事务
        Db::startTrans();
        try {
            // 保存订单信息
            isset($input['order']) ? $this->save($input['order'], ['order_id' => $input['order_id']]) : '';
            // 保存订单商品信息
            $this->updateOrderGoods($order_goods);
            // 更新设备信息
            $equip = new Equip;
            if (isset($input['equip'])) {
                $equip->saveAll($data);
                // 
                $log = new EquipUsingLog;
                $log->saveAll($log_data);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    public function updateOrderGoods($order_goods)
    {
        $model = new OrderGoods();
        return $model->saveAll($order_goods);
    }

    public function getRentPriceArr($rent_id, $rent_num, $order_goods_id)
    {
        $mode = new RentMode;
        $goods_spec_id = OrderGoods::get($order_goods_id)['goods_spec_id'];
        $rent_info = $mode->getInfo($rent_id, $goods_spec_id);
        // halt($rent_info->toArray());
        if ($rent_info['is_static'] == 0) return ['rent_price' => $rent_info['price'], 'rent_total_price' => bcmul($rent_info['price'], $rent_num, 2)];
        // 
        $map = json_decode($rent_info['map'], true);
        foreach ($map as $key => $value) {
            if (!isset($value['max'])) {
                return ['rent_price' => $value['price'], 'rent_total_price' => bcmul($value['price'], $rent_num > 3 ? 3 : $rent_num, 2)];
            } else {
                if ($rent_num >= $value['min'] && $rent_num <= $value['max']) {

                    return ['rent_price' => $value['price'], 'rent_total_price' => bcmul($value['price'], $rent_num > 3 ? 3 : $rent_num, 2)];
                }
            }
        }
    }


    /**
     * 修改订单状态
     */
    public function chgStatus($state)
    {
        $session = Session::get('yoshop_store');
        $wxapp_id = $session['wxapp']['wxapp_id'];
        $user = $session['user'];
        $type = $user['type'];
        $member_id = $type == 0 ? 0 : $user['member_id'];
        // 
        $_state = [];
        $_state['order_id'] = $state['order_id'];
        if ($state['pay_status'] == 10 && $state['delivery_status'] == 10) {
            $type = 0;
            $_state['pay_status'] = 20;
            $_state['pay_time'] = time();
            $_state['transaction_id'] = '变更状态支付';
        }
        // halt([$state, $_state]);
        // 
        switch ($state['chg_data']) {
            case 'pay':
                # 回到未付款  清空设备  清空支付信息
                $type = 1;
                $_state['pay_status'] = 10;
                $_state['delivery_status'] = 10;
                $_state['receipt_status'] = 10;
                $_state['order_status'] = 10;
                $_state['pay_time'] = 0;
                $_state['receipt_time'] = 0;
                $_state['transaction_id'] = '';
                // 
                break;

            case 'delivery':
                $type = 2;
                # 回到为发货  清空设备
                $_state['delivery_status'] = 10;
                $_state['receipt_status'] = 10;
                $_state['order_status'] = 10;
                $_state['receipt_time'] = 0;
                break;

            case 'recieve':
                $type = 3;
                # 变更为已送达
                $_state['receipt_status'] = 20;
                $_state['order_status'] = 30;
                $_state['receipt_time'] = time();
                break;
        }


        // 开启事务
        Db::startTrans();
        try {
            // 保存订单信息
            $this->update($_state);

            // deduct  TODO











            // log            
            $data = Equip::where('order_id', $state['order_id'])->select()->toArray();
            if (!empty($data)) {
                $_data = [];
                foreach ($data as $key => $value) {
                    $param = [];
                    $param['order_id'] = ($type == 1 || $type == 2) ? null : $state['order_id'];
                    $param['equip_id'] = $value['equip_id'];
                    $param['member_id'] = $member_id;
                    $param['equip_status'] = ($type == 1 || $type == 2) ? 10 : 30;
                    $param['wxapp_id'] = $wxapp_id;
                    $_data[] = $param;
                }
                $log = new EquipUsingLog;
                $log->saveAll($_data);
            }

            // 获取配送/维修员工
            $member_ids = OrderMember::where('order_id', $state['order_id'])->column('member_id');
            // 是否清空设备
            if ($type == 1 || $type == 2) {
                Equip::where('order_id', $state['order_id'])->update([
                    'status' => 10,
                    'order_id' => null,
                    'secure' => null,
                    'service_ids' => null,
                    'service_time' => null
                ]);
                OrderMember::where('order_id', $state['order_id'])->delete();
            }

            // 是否送达设备
            if ($type == 3) {
                Equip::where('order_id', $state['order_id'])->update([
                    'status' => 30,
                    'service_time' => time()
                ]);

                // UsingLog
                $data = Equip::where('order_id', $state['order_id'])->select()->toArray();
                $_data = [];
                foreach ($data as $key => $value) {
                    $param = [];
                    $param['order_id'] = $state['order_id'];
                    $param['equip_id'] = $value['equip_id'];
                    $param['member_id'] = $member_id;
                    $param['equip_status'] = 30;
                    $param['wxapp_id'] = $wxapp_id;
                    $_data[] = $param;
                }
                $log = new EquipUsingLog;
                $log->saveAll($_data);

                // 新增配送员工记录 和 状态
                $_member = [];
                foreach ($member_ids as $key => $value) {
                    $param = [];
                    $param['member_id'] = $value;
                    $param['order_id'] = $state['order_id'];
                    $param['status'] = 20; //已完成
                    $_member[] = $param;
                }
                $orderMember = new OrderMember;
                $orderMember->saveAll($_member);
            }

            // 还原配送相关员工状态                
            // Member::whereIn('id', $member_ids)->update([
            //     'status' => 10
            // ]);



            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    public function deleteAll($order_id)
    {
        Db::startTrans();
        try {
            // 删除订单信息
            $order = $this->get($order_id);
            $order->delete($order_id);
            // 清空设备
            Equip::where('order_id', $order_id)->update([
                'status' => 10,
                'order_id' => null,
                'secure' => null,
                'service_ids' => null,
                'service_time' => null
            ]);
            // 删除订单地址 和 商品
            $order->address()->delete();
            $order->goods()->delete();
            // 删除员工配送相关记录
            OrderMember::where(['order_id' => $order_id])->delete();
            // 
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }




    /**
     * 订单完结
     */
    public function endOrder($order_id)
    {
        $deductModel = new Deduct;
        $deduct_list = $deductModel->where('order_id', $order_id)->select()->toArray();
        $is_done = true;
        if (empty($deduct_list)) {

            // $this->error = '该订单没有扣款信息';
            // return false;
            $rentModel = new RentMode;
            $orderGoodsModel = new OrderGoods;
            $order_goods_data = $orderGoodsModel->where(['order_id' => $order_id])->select()->toArray();
            $_rent_date = 0;
            foreach ($order_goods_data as $key => $value) {
                $rent = $rentModel->where('id', $value['rent_id'])->find();
                if ($rent['rent_show_unit'] == '日') {
                    $rent_date = strtotime("+" . $value['rent_num'] . " days", $value['rent_date']);
                } else {
                    $rent_date = strtotime("+" . $value['rent_num'] . " months", $value['rent_date']);
                }
                $_rent_date = $_rent_date < $rent_date ? $rent_date : $_rent_date;
            }
            if (time() < $_rent_date) {
                $this->error = '订单租期未满';
                $is_done = false;
            }
        } else {
            foreach ($deduct_list as $key => $value) {
                if ($value['rent_end'] > time()) {
                    $this->error = '订单租期未满';
                    $is_done = false;
                    break;
                }
            }
        }


        if (!$is_done) {
            // return false;
        }
        // 开启事务
        Db::startTrans();
        try {
            //完结订单  

            if (!$is_done) {
                // return false;
                $deduct_data = [];
                foreach ($deduct_list as $key => $value) {
                    $_deduct_data = [];
                    $_deduct_data['id'] = $value['id'];
                    $_deduct_data['status'] = 20;
                    $deduct_data[] = $_deduct_data;
                }
                $deductModel->saveAll($deduct_data);
            }

            //更新订单状态
            $this->save([
                'done_status' => 20,
                'done_time' => time()
            ], [
                'order_id' => $order_id
            ]);

            // 订单有押金额度  进行用户返还
            $obj = $this->where('order_id', $order_id)->find();
            $user_id = $obj['user_id'];
            $freezing_account = $obj['freezing_account'];
            $freezing_quota = $obj['freezing_quota'];

            $account = new AccountMoney;
            $account->where('user_id', $user_id)->setDec('freezing_account', $freezing_account);
            $account->where('user_id', $user_id)->setDec('freezing_quota', $freezing_quota);
            $account->where('user_id', $user_id)->setInc('account_money', $freezing_account);
            $account->where('user_id', $user_id)->setInc('quota_money', $freezing_quota);


            // log
            $data = Equip::where('order_id', $order_id)->select()->toArray();
            $_data = [];
            foreach ($data as $key => $value) {
                $param = [];
                $param['order_id'] = null;
                $param['equip_id'] = $value['equip_id'];
                $param['member_id'] = 0;
                $param['equip_status'] = 10;
                $_data[] = $param;
            }
            $log = new EquipUsingLog;
            $log->saveAll($_data);


            // 更新订单设备记录
            Equip::where('order_id', $order_id)->update([
                'status' => 10,
                'order_id' => null,
                'secure' => null,
                'service_ids' => null,
                'service_time' => null
            ]);


            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }



    /**
     * 订单迁移
     */
    public function migrateOrder($post)
    {
        $user_id = $post['user_id'];
        if ($this->user_id == $user_id) {
            $this->error = '不能迁移原本用户';
            return false;
        }
        // 
        // 开启事务
        Db::startTrans();
        try {
            // 订单本身
            $this->save([
                'user_id' => $user_id
            ]);
            // 扣款订单
            $deduct = new Deduct;
            $deduct->where(['order_id' => $this->order_id])->update([
                'user_id' => $user_id
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }
}
