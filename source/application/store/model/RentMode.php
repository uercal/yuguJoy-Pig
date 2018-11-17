<?php

namespace app\store\model;

use app\common\model\RentMode as RentModeModel;

/**
 * 订单商品模型
 * Class RentMode
 * @package app\store\model
 */
class RentMode extends RentModeModel
{

    public function getHtml($id,$order_goods_id)
    {

        $data = $this->get($id);
    

        if ($data['is_static'] == 0) return '<input type="text" name="goods['.$order_goods_id.'][rent_num]" class="am-form-field rent-num-input" onchange="if(value.length>2)value=value.slice(0,2)" style="text-align:center;" value="1">
        <span style="color:#777;font-size:12px;">日</span>';
        //         
        $map = json_decode($data['map'], true);
        // 
        foreach ($map as $key => $value) {
            if(!isset($value['max'])) return '<input type="text" name="goods['.$order_goods_id.'][rent_num]" class="am-form-field rent-num-input" onchange="if(value.length>2)value=value.slice(0,2);if(value<6)value=6;" style="text-align:center;" value="6">
            <span style="color:#777;font-size:12px;">月</span>';
            if ($value['min'] == $value['max']) {
                return '<label class="am-radio-inline"><input type="radio" name="goods['.$order_goods_id.'][rent_num]" value="12" data-am-ucheck checked>1年</label><label class="am-radio-inline">
                <input type="radio" name="goods['.$order_goods_id.'][rent_num]" value="24" data-am-ucheck>2年</label>';
            } else {
                return '<input type="text" name="goods['.$order_goods_id.'][rent_num]" class="am-form-field rent-num-input" onchange="if(value.length>2)value=value.slice(0,2);if(value<1)value=1;if(value>5)value=5;" style="text-align:center;" value="1">
                <span style="color:#777;font-size:12px;">月</span>';
            }
        } 

    }

}
