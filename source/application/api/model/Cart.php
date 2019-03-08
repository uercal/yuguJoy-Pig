<?php

namespace app\api\model;

use think\Cache;
use think\Db;
use app\common\model\RentMode;

/**
 * 购物车管理
 * Class Cart
 * @package app\api\model
 */
class Cart
{
    /* @var int $user_id 用户id */
    private $user_id;

    /* @var array $cart 购物车列表 */
    private $cart = [];

    /* @var bool $clear 是否清空购物车 */
    private $clear = false;

    /**
     * 构造方法
     * Cart constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->cart = Cache::get('cart_' . $this->user_id) ?: [];
    }

    /**
     * 购物车列表
     * @param \think\Model|\think\Collection $user
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($user)
    {
        // 商品列表
        $goodsList = [];
        $goodsIds = array_unique(array_column($this->cart, 'goods_id'));
        foreach ((new Goods)->getListByIds($goodsIds) as $goods) {
            $goodsList[$goods['goods_id']] = $goods;
        }
        // 购物车商品列表
        $cartList = [];
        foreach ($this->cart as $key => $cart) {
            //             
            /* @var Goods $goods */
            $goods = $goodsList[$cart['goods_id']];
            $goods['key'] = $key;
            // 规格信息
            $goods['goods_spec_id'] = $cart['goods_spec_id'];
            $goods_sku = array_column($goods['spec']->toArray(), null, 'goods_spec_id')[$cart['goods_spec_id']];
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
            $rent_info = $rent_model->getInfo($cart['rent_id'], $cart['goods_spec_id']);
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
            $goods['service_ids'] = $service_ids = $cart['service_ids'];
            $goods['service_price'] = $service_ids == 0 ? 0 : Db::name('goods_service')->whereIn('service_id', $service_ids)->sum('service_price');
            $goods['service_info'] = $service_ids == 0 ? [] : Db::name('goods_service')->whereIn('service_id', $service_ids)->select();


            // 商品总价
            $goods['total_num'] = $cart['goods_num'];
            // $goods['total_price'] = $total_price = bcmul($goods['goods_price'], $cart['goods_num'], 2);
            $goods['total_price'] = $total_price = bcadd(bcadd(bcadd($goods['goods_price'], $goods['rent_total_price'], 2), $goods['secure_price'], 2), $goods['service_price'], 2);
            $goods['all_total_price'] = bcmul($total_price, $cart['goods_num'], 2);
            // 商品总重量
            $goods['goods_total_weight'] = bcmul($goods['goods_sku']['goods_weight'], $cart['goods_num'], 2);
            // 验证用户收货地址是否存在运费规则中
            // if ($goods['delivery']->checkAddress($cityId)) {            
            //     $goods['express_price'] = $goods['delivery']->calcTotalFee(
            //         $cart['goods_num'],
            //         $goods['goods_total_weight'],
            //         $cityId
            //     );
            // } else {
            //     empty($intraRegionError)
            //         && $intraRegionError = "很抱歉，您的收货地址不在商品[{$goods['goods_name']}]的配送范围内";
            // }
            $cartList[] = $goods->toArray();
        }

        // 商品总金额
        // $orderTotalPrice = array_sum(array_column($cartList, 'all_total_price'));
        $orderTotalPrice = 0;
        // 所有商品的运费金额
        // $allExpressPrice = array_column($cartList, 'express_price');
        // 订单总运费金额
        // $expressPrice = $allExpressPrice ? Delivery::freightRule($allExpressPrice) : 0.00;        
        $expressPrice = 0.00;
        return [
            'goods_list' => $cartList,  // 商品列表
            'order_total_num' => $this->getTotalNum(),  // 商品总数量
            'order_total_price' => round($orderTotalPrice, 2), // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($orderTotalPrice, $expressPrice, 2),  // 实际支付金额           
            'address' => $user['address_default'],  // 默认地址
            'exist_address' => !$user['address']->isEmpty(),  // 是否存在收货地址
            // 'express_price' => $expressPrice,   // 配送费用
            // 'intra_region' => $intraRegion,     // 当前用户收货城市是否存在配送规则中
            // 'intra_region_error' => $intraRegionError,
        ];
    }



    public function getListByKey($user, $keys)
    {
        // 商品列表
        $goodsList = [];
        $goodsIds = array_unique(array_column($this->cart, 'goods_id'));
        foreach ((new Goods)->getListByIds($goodsIds) as $goods) {
            $goodsList[$goods['goods_id']] = $goods;
        }
        // 当前用户收货城市id
        $cityId = $user['address_default'] ? $user['address_default']['city_id'] : null;
        // 商品是否在配送范围
        $intraRegion = true;
        $intraRegionError = '';
        // 购物车商品列表
        $cartList = [];
        foreach ($this->cart as $key => $cart) {
            if (!in_array($key, $keys)) continue;
            //             
            /* @var Goods $goods */
            $goods = $goodsList[$cart['goods_id']];
            // 规格信息
            $goods['goods_spec_id'] = $cart['goods_spec_id'];
            $goods_sku = array_column($goods['spec']->toArray(), null, 'goods_spec_id')[$cart['goods_spec_id']];
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
            $goods['service_ids'] = $service_ids = $cart['service_ids'];
            $goods['service_price'] = $service_ids == 0 ? 0 : Db::name('goods_service')->whereIn('service_id', $service_ids)->sum('service_price');
            $goods['service_info'] = $service_ids == 0 ? [] : Db::name('goods_service')->whereIn('service_id', $service_ids)->select();


            // 商品总价
            $goods['total_num'] = $cart['goods_num'];
            // $goods['total_price'] = $total_price = bcmul($goods['goods_price'], $cart['goods_num'], 2);
            $goods['total_price'] = $total_price = bcadd(bcadd(bcadd($goods['goods_price'], $goods['rent_total_price'], 2), $goods['secure_price'], 2), $goods['service_price'], 2);
            $goods['all_total_price'] = bcmul($total_price, $cart['goods_num'], 2);
            // 商品总重量
            $goods['goods_total_weight'] = bcmul($goods['goods_sku']['goods_weight'], $cart['goods_num'], 2);

            $cartList[] = $goods->toArray();
        }

        // 商品总金额
        $orderTotalPrice = array_sum(array_column($cartList, 'all_total_price'));
        // $orderTotalPrice = 0;

        $expressPrice = 0.00;
        return [
            'goods_list' => $cartList,  // 商品列表
            'order_total_num' => $this->getTotalNum(),  // 商品总数量
            'order_total_price' => round($orderTotalPrice, 2), // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($orderTotalPrice, $expressPrice, 2),  // 实际支付金额           
            'address' => $user['address_default'],  // 默认地址
            'exist_address' => !$user['address']->isEmpty(),  // 是否存在收货地址       
        ];
    }
















    /**
     * 添加购物车
     * @param $goods_id
     * @param $goods_num
     * @param $goods_spec_id
     * @return bool
     */
    public function add($goods_id, $goods_num, $goods_spec_id, $rent_id, $rent_num, $rent_date, $secure, $service_ids)
    {
        $index = $goods_id . '_' . $goods_spec_id . '_' . $rent_id . '_' . $rent_num . '_' . $rent_date . '_' . $secure . '_' . $service_ids;
        $create_time = time();
        $data = compact('goods_id', 'goods_num', 'goods_spec_id', 'rent_id', 'rent_num', 'rent_date', 'secure', 'service_ids', 'create_time');
        if (empty($this->cart)) {
            $this->cart[$index] = $data;
            return true;
        }
        isset($this->cart[$index]) ? $this->cart[$index]['goods_num'] += $goods_num : $this->cart[$index] = $data;
        return true;
    }

    /**
     * 减少购物车中某商品数量
     * @param $goods_id
     * @param $goods_spec_id
     */
    public function sub($goods_id, $goods_spec_id, $rent_id, $rent_num, $rent_date, $secure, $service_ids)
    {
        $index = $goods_id . '_' . $goods_spec_id . '_' . $rent_id . '_' . $rent_num . '_' . $rent_date . '_' . $secure . '_' . $service_ids;
        $this->cart[$index]['goods_num'] > 1 && $this->cart[$index]['goods_num']--;
    }

    /**
     * 删除购物车中指定商品
     * @param $goods_id
     * @param $goods_spec_id
     */
    public function delete($goods_id, $goods_spec_id, $rent_id, $rent_num, $rent_date, $secure, $service_ids)
    {
        $index = $goods_id . '_' . $goods_spec_id . '_' . $rent_id . '_' . $rent_num . '_' . $rent_date . '_' . $secure . '_' . $service_ids;
        unset($this->cart[$index]);
    }

    /**
     * 获取当前用户购物车商品总数量
     * @return int
     */
    public function getTotalNum()
    {
        return array_sum(array_column($this->cart, 'goods_num'));
    }

    /**
     * 析构方法
     * 将cart数据保存到缓存文件
     */
    public function __destruct()
    {
        $this->clear !== true && Cache::set('cart_' . $this->user_id, $this->cart);
    }

    /**
     * 清空当前用户购物车
     */
    public function clearAll()
    {
        $this->clear = true;
        Cache::rm('cart_' . $this->user_id);
    }


    public function clearByKey($keys)
    {
        foreach ($keys as $value) {
            unset($this->cart[$value]);
        }
    }



    public function exchange($origin, $new)
    {
        $index = $origin['goods_id'] . '_' . $origin['goods_spec_id'] . '_' . $origin['rent_id'] . '_' . $origin['rent_num'] . '_' . $origin['rent_date']
            . '_' . $origin['secure'] . '_' . $origin['service_ids'];
        $create_time = $this->cart[$index]['create_time'];
        $_index = $new = array_merge($origin, $new);
        unset($_index['goods_num']);
        $_index = implode('_', $_index);
        $new['create_time'] = $this->cart[$index]['create_time'];
        unset($this->cart[$index]);
        $this->cart[$_index] = $new;

        uasort($this->cart, function ($a, $b) {
            return $a['create_time'] - $b['create_time'] > 0;
        });

        return true;
    }
}
