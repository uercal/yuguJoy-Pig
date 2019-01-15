<?php

namespace app\common\model;

use think\Db;


/**
 * 订单商品模型
 * Class OrderGoods
 * @package app\common\model
 */
class OrderGoods extends BaseModel
{
    protected $name = 'order_goods';
    protected $updateTime = false;
    protected $append = ['service_info'];

    /**
     * 计算显示销量 (初始销量 + 实际销量)
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getServiceInfoAttr($value, $data)
    {
        return Db::name('goods_service')->whereIn('service_id', $data['service_ids'])->select()->toArray();
    }


    /**
     * 订单商品列表
     * @return \think\model\relation\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('UploadFile', 'image_id', 'file_id');
    }

    /**
     * 关联商品表
     * @return \think\model\relation\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('Goods');
    }


    /**
     * 关联租赁模式表
     */
    public function rentMode()
    {
        return $this->hasOne('RentMode', 'id', 'rent_id');
    }

    /**
     * 
     */
    public function equip()
    {
        return $this->hasMany('Equip', 'spec_value_id', 'spec_sku_id');
    }


    /**
     * 关联类型名称
     */
    public function specValueName()
    {
        return $this->hasOne('SpecValue', 'spec_value_id', 'spec_sku_id')->bind('spec_value');
    }


    /**
     * 关联商品规格表
     * @return \think\model\relation\BelongsTo
     */
    public function spec()
    {
        return $this->belongsTo('GoodsSpec', 'spec_sku_id', 'spec_sku_id');
    }

}
