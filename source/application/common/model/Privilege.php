<?php

namespace app\common\model;

use think\Cache;
use think\Hook;
use think\Request;
use think\Db;

/**
 * member 
 * @package app\common\model
 */
class Privilege extends BaseModel
{
    protected $name = 'store_privilege';

    public static function getALL()
    {
        $model = new static;
        $data = $model->order(['sort' => 'asc'])->select();
        $all = !empty($data) ? $data->toArray() : [];
        $tree = [];
        Cache::set('privilege_' . $model::$wxapp_id, compact('all', 'tree'));
        return Cache::get('privilege_' . $model::$wxapp_id);
    }


    public static function getCacheTree()
    {
        return self::getALL()['all'];
    }

    public static function detail($id)
    {
        return self::get($id);
    }



    public function getList()
    {
        $request = Request::instance();
        return $this->order(['id' => 'desc'])
            ->paginate(15, false, ['query' => $request->request()]);
    }


    public function add()
    {
        $data = input();
        $data = $data['privilege'];
        $data['api_menu_id'] = json_encode($data['api_menu_id']);    
        $data['wxapp_id'] = self::$wxapp_id;
        // 开启事务
        Db::startTrans();
        try {
            // 添加商品
            $this->allowField(true)->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }


    public function remove()
    {
        // 开启事务处理
        Db::startTrans();
        try {          
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
