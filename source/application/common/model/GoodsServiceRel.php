<?php

namespace app\common\model;

/**
 * 商品规格关系模型
 * Class GoodsSpecRel
 * @package app\common\model
 */
class GoodsServiceRel extends BaseModel
{
    protected $name = 'goods_service_rel';    
    protected $updateTime = false;
    /**
     * 关联规格组
     * @return \think\model\relation\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo('GoodsService','goods_service_id','service_id')->bind(['service_id','service_name','service_price','service_content']);
    }

}
