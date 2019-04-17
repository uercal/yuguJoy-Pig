<?php

namespace app\store\model;

use app\common\model\Equip as EquipModel;
use think\Db;
use think\Request;
use think\Session;

/**
 * 设备模型
 * Class Goods
 * @package app\store\model
 */
class Equip extends EquipModel
{

    public function getStatusTextAttr($value, $data)
    {
        $status = [10 => '在库', 20 => '运送中', 30 => '使用中', 40 => '维修中', 50 => '停用'];
        return $status[$data['status']];
    }

    /**
     * 添加设备
     * @param array $data
     * @return bool
     */
    public function add(array $data)
    {

        $data['wxapp_id'] = $data['spec']['wxapp_id'] = self::$wxapp_id;        
        // 型号 id
        $data['spec_value_id'] = $data['spec_value_id'];
        
        // 开启事务
        Db::startTrans();
        try {
            // 添加设备
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }

    /**
     * 添加设备图片
     * @param $images
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function addGoodsImages($images)
    {
        $this->image()->delete();
        $data = array_map(function ($image_id) {
            return [
                'image_id' => $image_id,
                'wxapp_id' => self::$wxapp_id
            ];
        }, $images);
        return $this->image()->saveAll($data);
    }

    /**
     * 编辑设备
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        $data['wxapp_id'] = $data['spec']['wxapp_id'] = self::$wxapp_id;
        // 开启事务
        Db::startTrans();
        try {
            // 保存设备
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 添加设备规格
     * @param $data
     * @param $isUpdate
     * @throws \Exception
     */
    private function addGoodsSpec(&$data, $isUpdate = false)
    {
        // 更新模式: 先删除所有规格
        $model = new GoodsSpec;
        $isUpdate && $model->removeAll($this['goods_id']);
        // 添加规格数据
        if ($data['spec_type'] === '10') {
            // 单规格
            $this->spec()->save($data['spec']);
        } else if ($data['spec_type'] === '20') {
            // 添加设备与规格关系记录
            $model->addGoodsSpecRel($this['goods_id'], $data['spec_many']['spec_attr']);
            // 添加设备sku
            $model->addSkuList($this['goods_id'], $data['spec_many']['spec_list']);
        }
    }

    /**
     * 删除设备
     * @return bool
     */
    public function remove()
    {
        // 开启事务处理
        Db::startTrans();
        try {           
            // 删除当前设备
            $this->delete();
            // 事务提交
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Db::rollback();
            return false;
        }
    }



    // 获取 在库 状态的设备
    public function getEquipList($ids)
    {        
        // 检验
        $equip_ids = preg_replace('# #', '', $ids);
        $equip_ids = str_replace('，', ',', $equip_ids);
        $equip_ids = explode(',', $equip_ids);
        $equip_ids = array_unique($equip_ids);

        $empty_arr = $equip_ids;
        $error_arr = [];
        $data = [];
        $ids = $this->with(['goods' => ['service' => ['service']], 'goodsGetName', 'specValue'])->whereIn('equip_id', $equip_ids)->select();
        foreach ($ids as $k => $v) {
            if (in_array($v['equip_id'], $empty_arr)) {
                array_del($empty_arr, $v['equip_id']);
            }
            if ($v['status'] != 10) {
                $error_arr[] = $v['equip_id'];
            } else {
                $data[] = $v;
            }
        }
        if (count($error_arr) != 0 || !empty($empty_arr)) {
            $msg = '';
            $msg .= !empty($empty_arr) ? '设备id:' . implode(',', $empty_arr) . '不存在！<br>' : '';
            $msg .= count($error_arr) != 0 ? '设备id:' . implode(',', $error_arr) . '是非在库状态！' : '';
            return ['code' => 1, 'msg' => $msg, 'data' => $data];
        }

        return ['code' => 0, 'msg' => '添加成功', 'data' => $data];
    }


    // 更换指定设备
    public function getOne($equip_id, $status = ['=', 10])
    {
        $data = $this->with(['goodsGetName', 'specValue', 'order', 'goods' => ['service' => ['service']]])->where([
            'equip_id' => ['=', $equip_id],
            'status' => $status
        ])->find();
        if ($data) $data->status_text = $this->getStatusTextAttr('', $data);
        return $data;
    }


    public function chgStatus($equip_id, $state)
    {
        Db::startTrans();
        try {
            // 
            $order_id = $this->where('equip_id', $equip_id)->value('order_id');                                 
            //         
            $this->addEquipLog($equip_id, $order_id, $state);                        
            // 
            $this->where('equip_id', $equip_id)->update([
                'status' => $state
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
