<?php

namespace app\api\model;

use app\common\model\Equip as EquipModel;
use app\api\model\EquipCheckLog;
use think\Cache;
use think\Db;


class Equip extends EquipModel
{
    public function specType($goods_id)
    {
        $data = $this->field(['spec_value_id', 'type'])->where('goods_id', $goods_id)->group('spec_value_id,type')->select()->toArray();
        return $this->getTypeText($data);
    }


    public function getTypeText($data)
    {
        $type = [10 => '笔记本', 20 => '打印机'];
        foreach ($data as $key => $value) {
            $data[$key]['type_text'] = $type[$value['type']];
        }
        $_data = [];
        foreach ($data as $key => $value) {
            if (!isset($_data[$value['spec_value_id']])) {
                $_data[$value['spec_value_id']] = [];
            }
            $_data[$value['spec_value_id']][] = $value;

        }
        return $_data;
    }


    public function getFilterList($filter, $page)
    {
        if (empty($filter)) {
            return $this->with(['goodsGetName', 'specValue'])->order('create_time', 'desc')->paginate(12, false, ['page' => $page, 'list_rows' => 12]);
        } else {

            $map = [];
            isset($filter['status']) ? $map['status'] = ['=', $filter['status']] : '';
            isset($filter['start_time']) ? $map['create_time'][] = ['>=', strtotime($filter['start_time'])] : '';
            isset($filter['end_time']) ? $map['create_time'][] = ['<=', strtotime($filter['end_time'])] : '';
            // halt($map);
            if (isset($filter['cate_id'])) {
                return $this->with(['goodsGetName', 'specValue'])->where('goods_id', 'IN', function ($query) use ($filter) {
                    $query->name('goods')->where('category_id', $filter['cate_id'])->field('goods_id');
                })->where($map)->order('create_time', 'desc')->paginate(12, false, ['page' => $page, 'list_rows' => 12]);
            } else {
                return $this->with(['goodsGetName', 'specValue'])->where($map)->order('create_time', 'desc')->paginate(12, false, ['page' => $page, 'list_rows' => 12]);
            }

        }
    }


    public function getCateArr()
    {
        return Db::name('category')->select()->toArray();
    }


    // 在库停用 状态切换
    public function changeStatus($member_id)
    {
        $status = $this->status;
        $order_id = $this->order_id;
        if (($status == 10 || $status == 50) && !$order_id) {
            $_usingLog = [];
            $_usingLog['equip_id'] = $this->equip_id;
            $_usingLog['member_id'] = $member_id;
            $_usingLog['equip_status'] = $status == 10 ? 50 : 10;
            $_usingLog['wxapp_id'] = 10001;                       
            // 开启事务
            Db::startTrans();
            try {
                $this->save(['status' => $status == 10 ? 50 : 10]);                
                // 设备使用记录                                
                $this->equipUsingLog()->save($_usingLog);
                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                $this->error = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
     * isChecking
     */
    public function isChecking($equip_id)
    {
        $log = Db::name('equip_check_log')->where(['equip_id' => $equip_id])->order('create_time', 'desc')->select()->toArray();
        if (!empty($log)) {
            return $log[0]['check_status'] == 10 ? true : false;
        } else {
            return false;
        }
    }

    public function getCheckDetail($equip_id)
    {
        $log = EquipCheckLog::where(['equip_id' => $equip_id])->order('create_time', 'desc')->limit('1')->find();
        return $log;
    }

    /**
     * startCheck
     */
    public function startCheck($member_id)
    {
        $status = $this->status;
        $order_id = $this->order_id;

        $_check_log = [];
        $_check_log['order_id'] = $order_id ? $order_id : null;
        $_check_log['equip_id'] = $this->equip_id;
        $_check_log['check_member_id'] = $member_id;
        $_check_log['check_time'] = time();
        $_check_log['check_status'] = 10;

         // 开启事务
        Db::startTrans();
        try {
            // 在库 维修
            if ($status == 10) {
                $this->save(['status' => 40]);
                $_usingLog = [];
                $_usingLog['equip_id'] = $this->equip_id;
                $_usingLog['member_id'] = $member_id;
                $_usingLog['equip_status'] = $status == 10 ? 50 : 10;
                $_usingLog['wxapp_id'] = 10001;                
                // 设备使用记录                                
                $this->equipUsingLog()->save($_usingLog);
                // 维修记录  只开始
                $this->equipCheckLog()->save($_check_log);
                Db::commit();
                return true;
            }

            // 维修中 维修
            if ($status == 40) {
                // 维修记录  只开始
                $this->equipCheckLog()->save($_check_log);
                Db::commit();
                return true;
            }


        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    /**
     * endCheck
     * 1.result==0 ： 修复成功设备状态都是维修中，区别在于是否绑定order_id，如果绑定 转为 使用中（针对返修情况），没有绑定一律转为在库
     * 2.result==1 :  修复失败，设备转为停用状态。
     * 3.以上情况都要增加 维修记录
     */
    public function checkingRes($member_id, $content, $result)
    {
        $status = $this->status;
        $order_id = $this->order_id;
        // 
        if ($status == 40) {
            // check
            $_check_log = [];
            $_check_log['order_id'] = $order_id ? $order_id : null;
            $_check_log['equip_id'] = $this->equip_id;
            $_check_log['check_member_id'] = $member_id;
            $_check_log['content'] = $content;
            $_check_log['check_time'] = time();
            $_check_log['check_status'] = $result == 0 ? 20 : 30;
            // using
            $_usingLog = [];
            $_usingLog['equip_id'] = $this->equip_id;
            $_usingLog['order_id'] = $order_id ? $order_id : null;
            $_usingLog['member_id'] = $member_id;
            $_usingLog['equip_status'] = $result == 0 ? ($order_id ? 30 : 10) : 50;
            $_usingLog['wxapp_id'] = 10001;
            // 开启事务
            Db::startTrans();
            try {
                $this->save(['status' => $result == 0 ? ($order_id ? 30 : 10) : 50]);                
                // 设备使用记录                                
                $this->equipUsingLog()->save($_usingLog);
                // 设备维修记录
                $this->equipCheckLog()->save($_check_log);
                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                $this->error = $e->getMessage();
                return false;
            }
        }
        return false;


    }
}
