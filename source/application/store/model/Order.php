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
use app\api\model\Goods as GoodsApi;
use app\api\model\Order as OrderApi;
use app\common\model\Wxapp;
/**
 * 订单管理
 * Class Order
 * @package app\store\model
 */
class Order extends OrderModel
{
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
            ->order(['create_time' => 'desc'])->paginate(10, false, [
                'query' => Request::instance()->request()
            ]);

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
            
            // 开启事务
            Db::startTrans();
            try {
                // 设备
                $equip = new Equip;
                $equip->saveAll($data);
                // 设备使用记录
                


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
            $rent_info = Db::name('rent_mode')->where('id', $cart['rent_id'])->find();
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
            $goods_sku = $goods['spec'][0];                   
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
            $rent_info = $this->getRentPriceArr($value['rent_id'], $value['rent_num']);
            $value['rent_price'] = $rent_info['rent_price'];
            $value['rent_total_price'] = $rent_info['rent_total_price'];
            // 
            $order_goods[] = $value;
        }

        if (isset($input['equip'])) {            
            // 更换了设备
            $equip = $input['equip'];
            $data = [];
            foreach ($equip as $key => $value) {
                $_data = [];
                $_data['equip_id'] = $key;
                $_data['secure'] = $value['secure'];
                $_data['service_ids'] = isset($value['service']) ? implode(',', $value['service']) : '';
                $_data['status'] = 20; // 已发货
                $_data['order_id'] = $input['order_id'];
                $data[] = $_data;
                // 
                $_p_data = [];
                $_p_data['equip_id'] = $value['p_equip_id'];
                $_p_data['secure'] = null;
                $_p_data['service_ids'] = null;
                $_p_data['status'] = 10; // 在库
                $_p_data['order_id'] = null;
                $data[] = $_p_data;
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
            isset($input['equip']) ?
                $equip->saveAll($data) : '';
            // 
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

    public function getRentPriceArr($rent_id, $rent_num)
    {
        $rent_info = RentMode::get($rent_id);
        if ($rent_info['is_static'] == 0) return ['rent_price' => 18.00, 'rent_total_price' => bcmul(18, $rent_num, 2)];
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
        $_state = [];
        $_state['order_id'] = $state['order_id'];
        if ($state['pay_status'] == 10 && $state['delivery_status'] == 10) {
            $type = 0;
            $_state['pay_status'] = 20;
            $_state['pay_time'] = time();
            $_state['transaction_id'] = '变更状态支付';
        }
        
        // 
        switch ($state['chg_data']) {
            case 'pay':
                # 回到未付款  清空设备  清空支付信息
                $type = 1;
                $_state['pay_status'] = 10;
                $_state['delivery_status'] = 10;
                $_state['receipt_status'] = 10;
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
                $_state['receipt_time'] = 0;
                break;

            case 'recieve':
                $type = 3;
                # 变更为已送达
                $_state['receipt_status'] = 20;
                $_state['receipt_time'] = time();
                break;
        }


        // 开启事务
        Db::startTrans();
        try {
            // 保存订单信息
            $this->update($_state);
            // 是否清空设备
            $type == 1 || $type == 2 ?
                Equip::where('order_id', $state['order_id'])->update([
                'status' => 10,
                'order_id' => null,
                'secure' => null,
                'service_ids' => null,
                'service_time' => null
            ]) : '';
            // 是否送达设备
            $type == 3 ?
                Equip::where('order_id', $state['order_id'])->update([
                'status' => 30,
                'service_time' => time()
            ]) : '';
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
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }



}
