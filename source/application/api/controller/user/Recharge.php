<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use app\api\model\Wxapp as WxappModel;
use app\common\library\wechat\WxPayCharge as WxPayChargeModel;
use app\api\model\OrderAfter as OrderAfterModel;
use app\api\model\Recharge as RechargeModel;

/**
 * 用户订单管理
 * Class Order
 * @package app\api\controller\user
 */
class Recharge extends Controller
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


    public function lists($dataType)
    {
        $model = new RechargeModel;
        $list = $model->getList($this->user['user_id'], $dataType);
        return $this->renderSuccess(compact('list'));
    }


    /**
     * 取消订单
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function cancel($id)
    {
        $model = RechargeModel::getUserChargeDetail($id, $this->user['user_id']);
        if ($model->cancel()) {
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
    public function pay($price)
    {
        $model = new RechargeModel;        
        // 创建订单
        if ($model->add($this->user['user_id'], $price)) {
            // 发起微信支付
            return $this->renderSuccess([
                'payment' => $this->wxPay(
                    $model['order_no'],
                    $this->user['open_id'],
                    $model['pay_price']
                ),
                'recharge_id' => $model['id']
            ]);
        }
        $error = $model->getError() ? : '充值创建失败';
        return $this->renderError($error);
    }



    /**
     * 构建微信支付
     * @param $order_no
     * @param $open_id
     * @param $pay_price
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    private function wxPay($order_no, $open_id, $pay_price)
    {
        $wxConfig = WxappModel::getWxappCache();
        $WxPay = new WxPayChargeModel($wxConfig);
        return $WxPay->unifiedorder($order_no, $open_id, $pay_price);
    }



    public function rechargePay($id)
    {        
        // 订单详情
        $order = RechargeModel::getUserChargeDetail($id, $this->user['user_id']);        
        // 发起微信支付
        $wxConfig = WxappModel::getWxappCache();
        $WxPay = new WxPayChargeModel($wxConfig);
        $wxParams = $WxPay->unifiedorder($order['order_no'], $this->user['open_id'], $order['pay_price']);                   
        return $this->renderSuccess($wxParams);
    }

}
