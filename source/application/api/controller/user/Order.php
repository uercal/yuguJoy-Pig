<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\PayLog;
use app\api\model\Recharge;
use app\api\model\Wxapp as WxappModel;
use app\common\library\wechat\WxPay;
use app\api\model\OrderAfter as OrderAfterModel;

/**
 * 用户订单管理
 * Class Order
 * @package app\api\controller\user
 */
class Order extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 我的订单列表
     * @param $dataType
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists($dataType)
    {
        $model = new OrderModel;
        $list = $model->getList($this->user['user_id'], $dataType);
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 订单详情信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail($order_id)
    {
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        $detail = $this->getUser();
        $account = $detail['account_money'];
        $payInfo = $this->payItem($order, $account);
        return $this->renderSuccess(['order' => $order, 'account' => $account, 'payInfo' => $payInfo]);
    }


    public function payItem($order, $account)
    {
        // 租金
        $rent_price = $order['pay']['rent_all_price'];
        // 押金
        $goods_price = bcadd($order['pay']['goods_all_price'], 0, 2);
        //可用额度
        $quota_price = $account['actual_quota'];
        //可用金额
        $actual_money = $account['actual_money'];
        //还需支付金额
        $bonus_money = ($quota_price - $goods_price) > 0 ? 0 : ($goods_price - $quota_price);
        //actual
        $real_money = $bonus_money + $rent_price;
        //state
        $canPay = ($account['actual_money'] - $real_money) >= 0 ? 1 : 0;
        return compact('bonus_money', 'real_money', 'canPay', 'rent_price', 'goods_price');
    }


    /**
     * 取消订单
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function cancel($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->cancel()) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError());
    }

    /**
     * 确认收货
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function receipt($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        if ($model->receipt()) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError());
    }

    /**
     * 立即支付
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function pay($order_id)
    {
        // 订单详情
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 判断商品状态、库存
        if (!$order->checkGoodsStatusFromOrder($order['goods'])) {
            return $this->renderError($order->getError());
        }
        // 发起微信支付
        $wxConfig = WxappModel::getWxappCache();
        $WxPay = new WxPay($wxConfig);
        $wxParams = $WxPay->unifiedorder($order['order_no'], $this->user['open_id'], $order['pay_price']);
        return $this->renderSuccess($wxParams);
    }


    /**
     * 发起售后
     */
    public function after()
    {
        $user = $this->getUser();
        $post = input();
        $after = [];
        $after['user_id'] = $user['user_id'];
        $after['order_id'] = $post['order_id'];
        $after['request_text'] = $post['request_text'];
        $after['request_pics_ids'] = $post['request_pics_ids'];
        $model = new OrderAfterModel;
        if ($model->addAfter($after)) {
            return $this->renderSuccess('发起成功');
        }
        $error = $model->getError() ? : '发起失败';
        return $this->renderError($error);

    }


    public function afterList($dataType)
    {
        $model = new OrderAfterModel;
        $list = $model->getList($this->user['user_id'], $dataType);
        return $this->renderSuccess(compact('list'));
    }

    public function afterDetail($after_id)
    {
        $model = new OrderAfterModel;
        $detail = $model->getDetail($after_id);
        $user = $this->getUser();
        $account = $user['account_money'];
        return $this->renderSuccess(compact('detail', 'account'));
    }


    /**
     * pay after order
     * 
     */
    public function doPay($id, $type)
    {
        $user = $this->getUser();
        $user_id = $user['user_id'];
        switch ($type) {
            case 'order':
                $order_id = $id;
                $model = new OrderModel;
                // 
                $order = OrderModel::getUserOrderDetail($order_id, $user_id);
                $account = $user['account_money'];
                $payInfo = $this->payItem($order, $account);
                // 
                if ($model->doPay($order, $payInfo, $user_id)) {
                    return $this->renderSuccess('支付成功');
                }
                $error = $model->getError() ? : '支付失败';
                return $this->renderError($error);

                break;
            case 'after':
            // 售后确认
                $after_id = $id;
                $model = new OrderAfterModel;
                if ($model->doPay($after_id, $user_id)) {
                    return $this->renderSuccess('支付成功');
                }
                $error = $model->getError() ? : '支付失败';
                return $this->renderError($error);

                break;

        }
    }




    /**
     * paylog
     */
    public function payLogList($page)
    {
        $user_id = $this->getUser()['user_id'];
        // $payLog = new PayLog;
        // $list = $payLog->getList($user_id, $page);
        // return $this->renderSuccess(compact('list'));

        $data = PayLog::with(['order', 'after', 'order_goods' => ['order', 'order_goods']])->where('user_id', $user_id)->select()->toArray();

        $recharge_log = Recharge::where(['pay_status' => 20, 'status' => 10, 'user_id' => $user_id])->select()->toArray();

        $data = array_merge($data, $recharge_log);

        usort($data, function ($a, $b) {
            return $a['create_time'] < $b['create_time'];
        });


        $curpage = $page;//当前第x页，

        $rows = 15;//每页显示几条记录

        $dataTo = array();

        $dataTo = array_chunk($data, $rows);
        $showdata = array();

        if ($dataTo) {
            $showdata = $dataTo[$curpage - 1];
        } else {
            $showdata = null;
        }

        $list = [
            'current_page' => $page,
            'data' => $showdata,
            'last_page' => count($dataTo),
            'per_page' => 15,
            'total' => count($data)
        ];

        return $this->renderSuccess(compact('list'));
    }




}
