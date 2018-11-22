<?php

namespace app\common\model;

use think\Model;
use think\Request;
use think\Session;
use think\Db;

/**
 * 模型基类
 * Class BaseModel
 * @package app\common\model
 */
class BaseModel extends Model
{
    public static $wxapp_id;
    public static $base_url;

    /**
     * 模型基类初始化
     */
    public static function init()
    {
        parent::init();
        // 获取当前域名
        self::$base_url = base_url();
        // 后期静态绑定wxapp_id
        self::bindWxappId(get_called_class());
    }

    /**
     * 后期静态绑定类名称
     * 用于定义全局查询范围的wxapp_id条件
     * 子类调用方式:
     *   非静态方法:  self::$wxapp_id
     *   静态方法中:  $self = new static();   $self::$wxapp_id
     * @param $calledClass
     */
    private static function bindWxappId($calledClass)
    {
        $class = [];
        if (preg_match('/app\\\(\w+)/', $calledClass, $class)) {
            $callfunc = 'set' . ucfirst($class[1]) . 'WxappId';
            method_exists(new self, $callfunc) && self::$callfunc();
        }
    }

    /**
     * 设置wxapp_id (store模块)
     */
    protected static function setStoreWxappId()
    {
        $session = Session::get('yoshop_store');
        self::$wxapp_id = $session['wxapp']['wxapp_id'];
    }

    /**
     * 设置wxapp_id (api模块)
     */
    protected static function setApiWxappId()
    {
        $request = Request::instance();
        self::$wxapp_id = $request->param('wxapp_id');
    }

    /**
     * 获取当前域名
     * @return string
     */
    protected static function baseUrl()
    {
        $request = Request::instance();
        $host = $request->scheme() . '://' . $request->host();
        $dirname = dirname($request->baseUrl());
        return empty($dirname) ? $host : $host . $dirname . '/';
    }

    /**
     * 定义全局的查询范围
     * @param \think\db\Query $query
     */
    protected function base($query)
    {
        if (self::$wxapp_id > 0) {
            $query->where($query->getTable() . '.wxapp_id', self::$wxapp_id);
        }
    }





    /**
     * 设备更新 使用记录  和 维修记录
     */
    public function addEquipLog($equip_id, $order_id, $state)
    {   
        // 
        $session = Session::get('yoshop_store');
        $wxapp_id = $session['wxapp']['wxapp_id'];
        $user = $session['user'];
        $type = $user['type'];
        $member_id = $type == 0 ? 0 : $user['member_id'];
        Db::startTrans();
        try {
            // 
            Db::name('equip_using_log')->insert([
                'order_id' => $order_id,
                'equip_id' => $equip_id,
                'member_id' => $member_id,
                'equip_status' => $state,
                'wxapp_id' => $wxapp_id,
                'create_time' => time()
            ]);            
            // 如果维修
            if ($state == 40) {
                Db::name('equip_check_log')->insert([
                    'order_id' => $order_id,
                    'equip_id' => $equip_id,
                    'check_member_id' => $member_id,
                    'check_status' => 10,
                    'check_time' => time(),
                    'wxapp_id' => $wxapp_id,
                    'create_time' => time()
                ]);
            }
            //    
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }        
    }
    
}
