<?php

namespace app\common\model;

use think\Hook;
use think\Cache;
use think\Db;

/**
 * member 
 * @package app\common\model
 */
class GoodsService extends BaseModel
{
    protected $name = 'goods_service';


    public static function detail($id)
    {
        return self::get($id);
    }
    
    public static function getAll(){
        $model = new static;
        $data = $model->order(['create_time' => 'asc'])->select();
        $all = !empty($data) ? $data->toArray() : [];
        return $all;
    }
}
