<?php

namespace app\api\model;

use app\common\model\OrderMember as OrderMemberModel;
use app\api\model\Order;
use app\api\model\Equip;
use app\api\model\EquipUsingLog;
use app\api\model\Exam;
use think\Db;

/**
 * 订单-成员模型
 * Class OrderGoods
 * @package app\store\model
 */
class OrderMember extends OrderMemberModel
{

    /**
     * 获得指定成员进行中订单数量
     */
    public function getOrderAfter($member_id)
    {
        $list = $this->with(['order', 'after'])->where(['member_id' => $member_id])->select()->toArray();
        $order_list = [];
        $after_list = [];
        foreach ($list as $key => $value) {
            if (!empty($value['order_id'])) {
                $order_list[] = $value;
            }
            if (!empty($value['after_id'])) {
                $after_list[] = $value;
            }
        }
        $order = [];
        $order['doing'] = [];
        $order['done'] = [];
        $after = [];
        $after['doing'] = [];
        $after['done'] = [];
        $done = [];
        foreach ($order_list as $key => $value) {
            if ($value['status'] == 10) {
                $order['doing'][] = $value;
            }
            if ($value['status'] == 20) {
                $order['done'][] = $value;
                $done[] = $value;
            }
        }
        $orderCount = count($order['doing']) - count($order['done']);
        foreach ($after_list as $key => $value) {
            if ($value['status'] == 10) {
                $after['doing'][] = $value;
            }
            if ($value['status'] == 20) {
                $after['done'][] = $value;
                $done[] = $value;
            }
        }
        $afterCount = count($after['doing']) - count($after['done']);

        return compact('orderCount', 'afterCount');
    }


    /**
     * 获取该员工所有订单列表和状态
     */
    public function getMemeberOrderList($member_id, $type, $page)
    {
        switch ($type) {
            case 1:
                return $this->with(['order' => ['address']])->where('order_id', 'NOT IN', function ($query) {
                    $query->name('order_member')->where('status', 20)->whereNotNull('order_id')->field('order_id');
                })->whereNull('after_id')->where('member_id', $member_id)->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
            case 2:
                return $this->with(['after' => ['order' => ['address']]])->where('after_id', 'NOT IN', function ($query) {
                    $query->name('order_member')->where('status', 20)->whereNotNull('after_id')->field('after_id');
                })->whereNull('order_id')->where('member_id', $member_id)->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
            case 3:
                return $this->with(['order' => ['address'], 'after' => ['order' => ['address']]])->where(['status' => 20, 'member_id' => $member_id])
                    ->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
        }
    }



    /**
     * 获取该ID的详情
     */
    public function getDetail($id, $type)
    {
        switch ($type) {
            case 'order':
                $data = $this->with(['order' => ['address', 'equip' => ['goods', 'specValue']]])->where('id', $id)->find();
                $equip = $data['order']['equip'];
                break;
            case 'after':
                $data = $this->with(['after' => ['order' => ['address', 'equip' => ['goods', 'specValue']]]])->where('id', $id)->find();
                $equip = $data['after']['order']['equip'];
                break;
        }

        $equip = $equip->toArray();
        // 按产品 把设备分类  spec_value TODO
        $arr = [];
        foreach ($equip as $key => $value) {
            $arr[$value['spec_value_id']]['goods'] = $value['goods'];
            $arr[$value['spec_value_id']]['spec'] = $value['spec_value'];
            $arr[$value['spec_value_id']]['equip'][] = $value;
        }
        usort($arr, function ($a, $b) {
            return $a['spec']['spec_value_id'] > $b['spec']['spec_value_id'];
        });
        $equip = array_values($arr);

        // 
        foreach ($equip as $key => $value) {
            if (count($value['equip']) > 1) {
                $equip[$key]['more'] = false;
            }
        }

        return compact('data', 'equip');
    }


    /**
     * 员工端 申请完成派送底单
     */
    public function sendDone($input, $member_id)
    {
        // 数据组装
        $wxapp_id = $input['wxapp_id'];
        $order_id = $input['order_id'];
        $order_member_id = $input['id'];
        $exam_content = json_encode(['order_id' => $input['order_id'], 'send_content' => $input['send_content'], 'send_pic_ids' => $input['send_pic_ids']]);

        $_state = [
            'receipt_status' => 20,
            'receipt_time' => time(),
            'order_status' => 30
        ];

        Db::startTrans();
        try {
            // 保存订单信息            
            Db::name('order')->where('order_id', $order_id)->update($_state);
            // 设备状态
            Equip::where('order_id', $order_id)->update([
                'status' => 30,
                'service_time' => time()
            ]);
            // 设备使用记录            
            $data = Equip::where('order_id', $order_id)->select()->toArray();
            $_data = [];
            foreach ($data as $key => $value) {
                $param = [];
                $param['order_id'] = $order_id;
                $param['equip_id'] = $value['equip_id'];
                $param['member_id'] = $member_id; //员工操作
                $param['equip_status'] = 30;
                $param['wxapp_id'] = $wxapp_id;
                $_data[] = $param;
            }

            $log = new EquipUsingLog;
            $log->saveAll($_data);

            // 获取配送/维修员工
            $member_ids = $this->where('order_id', $order_id)->column('member_id');
            // 新增配送员工记录
            $_member = [];
            foreach ($member_ids as $key => $value) {
                $param = [];
                $param['member_id'] = $value;
                $param['order_id'] = $order_id;
                $param['status'] = 20; //已完成
                $_member[] = $param;
            }
            $this->saveAll($_member);

            // 员工确认 需要新增审批记录
            $exam = new Exam;
            $exam->save([
                'member_id' => $member_id,
                'content' => $exam_content,
                'type' => 20, //员工送达审批
                'status' => 20
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            halt($e->getMessage());
            $this->error = $e->getMessage();
            return false;
        }
    }
}
