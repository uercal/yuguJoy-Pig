<?php

namespace app\store\model;

use app\common\model\Goods as GoodsModel;
use think\Db;

/**
 * 商品模型
 * Class Goods
 * @package app\store\model
 */
class Goods extends GoodsModel
{
    /**
     * 添加商品
     * @param array $data
     * @return bool
     */
    public function add(array $data)
    {
        if (!isset($data['images']) || empty($data['images'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['wxapp_id'] = $data['spec']['wxapp_id'] = self::$wxapp_id;
        // 开启事务
        Db::startTrans();
        try {
            // 添加商品
            $this->allowField(true)->save($data);
            // 商品规格
            $this->addGoodsSpec($data);
            // 商品服务
            !empty($data['service']) ? $this->addGoodsService($data['service']) : '';
            // 商品图片
            $this->addGoodsImages($data['images']);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e;
            Db::rollback();
        }
        return false;
    }

    /**
     * 添加商品图片
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


    private function addGoodsService($service_ids)
    {
        $this->service()->delete();
        // 
        $data = array_map(function ($service_id) {
            return [
                'goods_service_id' => $service_id,
                'wxapp_id' => self::$wxapp_id
            ];
        }, $service_ids);
        return $this->service()->saveAll($data);
    }





    /**
     * 编辑商品
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        if (!isset($data['images']) || empty($data['images'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['wxapp_id'] = $data['spec']['wxapp_id'] = self::$wxapp_id;


        // 检查租赁金额是否全部为0
        $spec_list = $data['spec_many']['spec_list'];
        $zero = 0;
        foreach ($spec_list as $key => $value) {
            $rent_mode = $value['form']['rent_mode'];
            if ($this->isZeroMode($rent_mode)) {
                $zero = 1;
                break;
            }
        }

        if ($zero == 1) {
            $this->error = '租赁模式金额不能全为0';
            return false;
        }

        // 开启事务
        Db::startTrans();
        try {
            // 保存商品
            $this->allowField(true)->save($data);
            // 商品规格
            $this->addGoodsSpec($data, true);
            // 商品图片
            $this->addGoodsImages($data['images']);
            // 商品服务
            !empty($data['service']) ? $this->addGoodsService($data['service']) : '';
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    public function isZeroMode($rent_mode)
    {
        $day = $rent_mode['day']['price'] == 0;
        $month = $rent_mode['month']['ot'] == 0 || $rent_mode['month']['tf'] == 0 || $rent_mode['month']['s'] == 0;
        $year = $rent_mode['year']['o'] == 0 || $rent_mode['year']['t'] == 0;
        if ($day && $month && $year) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * 添加商品规格
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
            // 添加商品与规格关系记录
            $model->addGoodsSpecRel($this['goods_id'], $data['spec_many']['spec_attr']);
            // 添加商品sku
            $model->addSkuList($this['goods_id'], $data['spec_many']['spec_list']);
        }
    }

    /**
     * 删除商品
     * @return bool
     */
    public function remove()
    {
        // 开启事务处理
        Db::startTrans();
        try {
            // 删除商品sku
            (new GoodsSpec)->removeAll($this['goods_id']);
            // 删除商品图片
            $this->image()->delete();
            // 删除当前商品
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
}
