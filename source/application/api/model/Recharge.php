<?php

namespace app\api\model;

use app\common\model\Recharge as RechargeModel;
use app\common\exception\BaseException;
use think\Cache;
use think\Db;

class Recharge extends RechargeModel
{
    public function add($user_id, $price)
    {
        Db::startTrans();
        // 记录订单信息
        $this->save([
            'user_id' => $user_id,
            'order_no' => $this->orderNo(),
            'source' => 10,
            'pay_price' => $price
        ]);        
        // 更新    
        Db::commit();
        return true;
    }



    public function getList($user_id, $dataType)
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($dataType) {
            case 'all':
                break;
            case 'undo':
                $filter['status'] = 10;
                $filter['pay_status'] = 10;
                break;
            case 'done':
                $filter['status'] = 10;
                $filter['pay_status'] = 20;
                break;
        }
        return $this->where('user_id', '=', $user_id)
            ->order(['create_time' => 'desc'])
            ->where($filter)
            ->select()
            ->append(['pay_status_text', 'status_text']);
    }



    /**
     * 订单详情
     * @param $order_id
     * @param null $user_id
     * @return null|static
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public static function getUserChargeDetail($id, $user_id)
    {
        if (!$order = self::get([
            'id' => $id,
            'user_id' => $user_id,
            'pay_status' => ['<>', 20]
        ])) {
            throw new BaseException(['msg' => '订单不存在']);
        }

        return $order;
    }


    /**
     * 取消订单
     * @return bool|false|int
     * @throws \Exception
     */
    public function cancel()
    {
        if ($this['pay_status'] === 20) {
            $this->error = '已付款订单不可取消';
            return false;
        }
        return $this->save(['status' => 20]);
    }









}
