<?php

namespace app\common\model;

use think\Hook;
use think\Cache;
use think\Db;

/**
 * member 
 * @package app\common\model
 */
class RentMode extends BaseModel
{
    protected $name = 'rent_mode';

    protected $append = ['rent_show_unit'];


    public function getRentShowUnitAttr($value, $data)
    {
        if ($data['is_static'] == 0) return '日';
        // 
        $map = $data['map'];
        if(!is_array($map)) $map = json_decode($data['map'], true);        
        // 
        foreach ($map as $key => $value) {
            if(!isset($value['max'])) return '月';
            if ($value['min'] == $value['max']) {
                return '年';
            } else {
                return '月';
            }
        }        
    }



    public function getList()
    {
        $list = $this->order('id')->select();
        
        foreach ($list as $key => $value) {
            $list[$key]['map'] = json_decode($value['map'], true);
            if ($value['is_static'] == 0) {
                // 不随动
                $list[$key]['price_type_text'] = '日';
                $list[$key]['show_price'] = '￥' . $value['price'];
            } else {
                // 随动
                $list[$key]['price_type_text'] = '月';
                $price = [];
                foreach ($list[$key]['map'] as $k => $v) {
                    $price[] = '￥' . $v['price'];
                }
                sort($price);
                $list[$key]['show_price'] = implode('~', $price);
            }
        }

        return $list;
    }


    

}
