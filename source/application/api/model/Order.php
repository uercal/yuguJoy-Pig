<?php

namespace app\api\model;

use think\Db;
use app\common\model\Order as OrderModel;
use app\api\model\EquipUsingLog;
use app\api\model\Equip;
use app\api\model\OrderMember;
use app\api\model\Member;
use app\common\exception\BaseException;

/**
 * 订单模型
 * Class Order
 * @package app\api\model
 */
class Order extends OrderModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'update_time'
    ];


    /**
     * 订单确认-立即购买
     * @param User $user
     * @param $goods_id
     * @param $goods_num
     * @return array
     * @throws \think\exception\DbException
     */
    public function getBuyNow($user, $goods_id, $goods_num, $goods_sku_id, $rent_id, $rent_num, $rent_date, $secure, $service_ids)
    {        
        // 商品信息
        /* @var Goods $goods */
        $goods = Goods::detail($goods_id);
        $goods_spec_id = Db::name('goods_spec')->where('spec_sku_id', $goods_sku_id)->value('goods_spec_id');
        // 规格信息
        $goods['goods_spec_id'] = $goods_spec_id;
        $goods_sku = array_column($goods['spec']->toArray(), null, 'goods_spec_id')[$goods_spec_id];
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
        $goods['goods_price'] = $goods['goods_sku']['goods_price'];
        // 商品总价
        $goods['total_num'] = $goods_num;
         // 商品租赁信息
        $rent_info = Db::name('rent_mode')->where('id', $rent_id)->find();
        $goods['rent_date'] = $rent_date;
        $goods['rent_num'] = $rent_num;
        $goods['rent_info'] = $rent_info;

        if ($rent_info['is_static'] == 0) {
            $goods['rent_price'] = $rent_info['price'];
            $goods['rent_total_price'] = bcmul($rent_info['price'], $rent_num, 2);
        } else {
            $map = json_decode($rent_info['map'], true);
            foreach ($map as $key => $value) {
                if (!isset($value['max']) && $rent_num >= 6) {
                    // 没有max 为6+ 大于3月 先收3月  IMPORTANT
                    $goods['rent_total_price'] = bcmul($value['price'], 3, 2);
                    $goods['rent_price'] = $value['price'];
                    break;
                } else {
                    // 
                    if ($rent_num >= $value['min'] && $rent_num <= $value['max']) {
                        $goods['rent_total_price'] = bcmul($value['price'], $rent_num, 2);
                        $goods['rent_price'] = $value['price'];
                        break;
                    }
                }
            }
        }


        // 保险金额与否
        $goods['secure'] = $secure;
        $goods['secure_price'] = $secure == 0 ? 0 : $goods_sku['secure_price'];


        // 增值服务
        $goods['service_ids'] = $service_ids;
        $goods['service_price'] = $service_ids == 0 ? 0 : Db::name('goods_service')->whereIn('service_id', $service_ids)->sum('service_price');
        $goods['service_info'] = $service_ids == 0 ? [] : Db::name('goods_service')->whereIn('service_id', $service_ids)->select();

        // 
        $goods['total_price'] = $total_price = bcadd(bcadd(bcadd($goods['goods_price'], $goods['rent_total_price'], 2), $goods['secure_price'], 2), $goods['service_price'], 2);
        // $goods['total_price'] = $totalPrice = bcmul($goods['goods_price'], $goods_num, 2);
        $goods['all_total_price'] = bcmul($total_price, $goods['total_num'], 2);
        // 商品总重量
        $goods_total_weight = bcmul($goods['goods_sku']['goods_weight'], $goods_num, 2);
        // 当前用户收货城市id
        // $cityId = $user['address_default'] ? $user['address_default']['city_id'] : null;
        // 验证用户收货地址是否存在运费规则中
        // $intraRegion = $goods['delivery']->checkAddress($cityId);
        // $intraRegion = null;
        // 计算配送费用
        // $expressPrice = $intraRegion ?
        //     $goods['delivery']->calcTotalFee($goods_num, $goods_total_weight, $cityId) : 0;
        $expressPrice = 0.00;
        return [
            'rent_id' => $rent_id,
            'rent_price' => $goods['rent_price'],
            'rent_num' => $rent_num,
            'rent_date' => $rent_date,
            'secure' => $secure,
            'goods_list' => [$goods],               // 商品详情
            'order_total_num' => $goods_num,        // 商品总数量
            'order_total_price' => $goods['all_total_price'],    // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($goods['all_total_price'], $expressPrice, 2),  // 实际支付金额
            'address' => $user['address_default'],  // 默认地址
            'exist_address' => !$user['address']->isEmpty(),  // 是否存在收货地址
            // 'express_price' => $expressPrice,    // 配送费用
            // 'intra_region' => $intraRegion,    // 当前用户收货城市是否存在配送规则中
            // 'intra_region_error' => '很抱歉，您的收货地址不在配送范围内',
        ];
    }

    /**
     * 订单确认-购物车结算
     * @param $user
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCart($user)
    {
        $model = new Cart($user['user_id']);
        return $model->getList($user);
    }



    public function getCartByKey($user, $key_list)
    {
        $model = new Cart($user['user_id']);
        return $model->getListByKey($user, $key_list);
    }







    /**
     * 新增订单
     * @param $user_id
     * @param $order
     * @return bool
     * @throws \Exception
     */
    public function add($user_id, $order)
    {
        Db::startTrans();
        // 记录订单信息
        $this->save([
            'user_id' => $user_id,
            'wxapp_id' => self::$wxapp_id,
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
                'wxapp_id' => self::$wxapp_id,
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
            'wxapp_id' => self::$wxapp_id
        ]);
        // 更新商品库存        
        Db::commit();
        return true;
    }

    /**
     * 用户中心订单列表
     * @param $user_id
     * @param string $type
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment':
                $filter['pay_status'] = 10;
                break;
            case 'delivery':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 10;
                break;
            case 'received':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'doing':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 20;
                break;
        }
        return $this->with(['goods.image'])
            ->where('user_id', '=', $user_id)
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->order(['create_time' => 'desc'])
            ->select();
    }

    /**
     * 取消订单
     * @return bool|false|int
     * @throws \Exception
     */
    public function cancel()
    {
        if ($this['pay_status']['value'] === 20) {
            $this->error = '已付款订单不可取消';
            return false;
        }
        // 回退商品库存
        $this->backGoodsStock($this['goods']);
        return $this->save(['order_status' => 20]);
    }

    /**
     * 回退商品库存
     * @param $goodsList
     * @return array|false
     * @throws \Exception
     */
    private function backGoodsStock(&$goodsList)
    {
        $goodsSpecSave = [];
    // foreach ($goodsList as $goods) {
    //         // 下单减库存
    //     if ($goods['deduct_stock_type'] === 10) {
    //         $goodsSpecSave[] = [
    //             'goods_spec_id' => $goods['goods_spec_id'],
    //             'stock_num' => ['inc', $goods['total_num']]
    //         ];
    //     }
    // }
        // 更新商品规格库存
        return !empty($goodsSpecSave) && (new GoodsSpec)->isUpdate()->saveAll($goodsSpecSave);
    }

    /**
     * 确认收货
     * @return bool|false|int
     */
    public function receipt()
    {

        if ($this['delivery_status']['value'] === 10 || $this['receipt_status']['value'] === 20) {
            $this->error = '该订单不合法';
            return false;
        }

        $_state = [
            'receipt_status' => 20,
            'receipt_time' => time(),
            'order_status' => 30
        ];

        Db::startTrans();
        try {
            // 保存订单信息
            $this->save($_state);
            // 设备状态
            Equip::where('order_id', $this['order_id'])->update([
                'status' => 30,
                'service_time' => time()
            ]);
            // 设备使用记录            
            $data = Equip::where('order_id', $this['order_id'])->select()->toArray();
            $_data = [];
            foreach ($data as $key => $value) {
                $param = [];
                $param['order_id'] = $this['order_id'];
                $param['equip_id'] = $value['equip_id'];
                $param['member_id'] = -1;//用户确认 非员工操作
                $param['equip_status'] = 30;
                $param['wxapp_id'] = self::$wxapp_id;
                $_data[] = $param;
            }

            $log = new EquipUsingLog;
            $log->saveAll($_data);      

            // 获取配送/维修员工
            $member_ids = OrderMember::where('order_id', $this['order_id'])->column('member_id');
            // 还原配送相关员工状态                
            Member::whereIn('id', $member_ids)->update([
                'status' => 10
            ]);                        
            // 新增配送员工记录
            $_member = [];
            foreach ($member_ids as $key => $value) {
                $param = [];
                $param['member_id'] = $value;
                $param['order_id'] = $this['order_id'];
                $param['type'] = 10;//配送
                $param['status'] = 20;//已完成
                $_member[] = $param;
            }
            $orderMember = new OrderMember;
            $orderMember->saveAll($_member);
            
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }


    }

    /**
     * 获取订单总数
     * @param $user_id
     * @param string $type
     * @return int|string
     */
    public function getCount($user_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment':
                $filter['pay_status'] = 10;
                break;
            case 'delivery':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 10;
                break;
            case 'received':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'doing':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 20;
                $filter['order_status'] = 30;
                break;
        }
        return $this->where('user_id', '=', $user_id)
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->count();
    }

    /**
     * 订单详情
     * @param $order_id
     * @param null $user_id
     * @return null|static
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        if (!$order = self::get([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'order_status' => ['<>', 20]
        ], ['goods' => ['image', 'spec', 'specValueName', 'goods', 'rentMode'], 'address'])) {
            throw new BaseException(['msg' => '订单不存在']);
        }

        return $order;
    }

    /**
     * 判断商品库存不足 (未付款订单)
     * @param $goodsList
     * @return bool
     */
    public function checkGoodsStatusFromOrder(&$goodsList)
    {
        foreach ($goodsList as $goods) {
            // 判断商品是否下架
            if ($goods['goods']['goods_status']['value'] !== 10) {
                $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 已下架');
                return false;
            }
            // 付款减库存
        // if ($goods['deduct_stock_type'] === 20 && $goods['spec']['stock_num'] < 1) {
        //     $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 库存不足');
        //     return false;
        // }
        }
        return true;
    }

    /**
     * 设置错误信息
     * @param $error
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

}
