<?php

namespace app\common\model;

use think\Hook;
use think\Cache;
use think\Db;

/**
 * member 
 * @package app\common\model
 */
class Role extends BaseModel
{
    protected $name = 'store_member_role';

    protected $append = ['api_menu'];

    public function getApiMenuAttr($value, $data)
    {
        return Db::name('store_privilege')->whereIn('id',$data['privilege_ids'])->column('api_menu_id');        
    }

    public static function getALL()
    {
        $model = new static;
        $data = $model->order(['sort' => 'asc'])->select();
        $all = !empty($data) ? $data->toArray() : [];
        $tree = [];
        Cache::set('role_' . $model::$wxapp_id, compact('all', 'tree'));
        return Cache::get('role_' . $model::$wxapp_id);
    }


    public static function getCacheTree()
    {
        return self::getALL()['all'];
    }


    public static function detail($id)
    {
        $data = self::get($id);
        $privilege_ids = explode(',', $data['privilege_ids']);
        $privilege_list = Db::name('store_privilege')->select();
        foreach ($privilege_list as $key => $value) {
            if (in_array($value['id'], $privilege_ids)) {
                $value['is_selected'] = true;
                $privilege_list[$key] = $value;
            } else {
                $value['is_selected'] = false;
                $privilege_list[$key] = $value;
            }
        }
        $data['privilege_list'] = $privilege_list;
        return $data;
    }
}
