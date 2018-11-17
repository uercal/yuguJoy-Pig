<?php

namespace app\store\model;

use app\common\model\GoodsService as GoodsServiceModel;
use think\Db;
use think\Request;

/**
 * 商品服务模型
 * Class GoodsService
 * @package app\store\model
 */
class GoodsService extends GoodsServiceModel
{
    public function getList()
    {
        return $this->order('create_time')->paginate(15, false, [
            'query' => Request::instance()->request()
        ]);;
    }

    public function add(array $data)
    {
        $data['wxapp_id'] = self::$wxapp_id;            
        // 开启事务
        Db::startTrans();
        try {
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }    


    
}
