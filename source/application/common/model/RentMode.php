<?php

namespace app\common\model;

use think\Hook;
use think\Cache;
use think\Db;
use function Qiniu\json_decode;

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
        if (!is_array($map)) $map = json_decode($data['map'], true);
        // 
        foreach ($map as $key => $value) {
            if (!isset($value['max'])) return '月';
            if ($value['min'] == $value['max']) {
                return '年';
            } else {
                return '月';
            }
        }
    }



    public function getList($goods_spec_id)
    {

        $rent_mode = Db::name('goods_spec')->where(['goods_spec_id' => $goods_spec_id])->value('rent_mode');
        $rent_mode = json_decode($rent_mode, true);


        $list = $this->order('id')->select();

        foreach ($list as $key => $value) {
            if ($value['is_static'] == 0) {
                // day
                $list[$key]['price'] = $rent_mode['day']['price'];
            } else {
                $map = json_decode($value['map'], true);
                foreach ($map as $k => $v) {
                    if (isset($v['max'])) {
                        // 
                        if ($v['min'] == $v['max']) {
                            // year
                            if ($v['min'] == 12) {
                                $map[$k]['price'] = $rent_mode['year']['o'];
                            }
                            if ($v['min'] == 24) {
                                $map[$k]['price'] = $rent_mode['year']['t'];
                            }
                        } else {
                            // 1-2 & 3-5
                            if ($v['min'] == 1) {
                                // 1-2
                                $map[$k]['price'] = $rent_mode['month']['ot'];
                            }
                            if ($v['min'] == 3) {
                                // 3-5
                                $map[$k]['price'] = $rent_mode['month']['tf'];
                            }
                        }
                    } else {
                        // 6+
                        $map[$k]['price'] = $rent_mode['month']['s'];
                    }
                }
                $list[$key]['map'] = json_encode($map);
            }
        }





        foreach ($list as $key => $value) {
            $list[$key]['map'] = json_decode($value['map'], true);
            if ($value['is_static'] == 0) {
                // 不随动                
                $list[$key]['price_type_text'] = '日';
                $list[$key]['show_price'] = '￥' . $value['price'];
                $list[$key]['content'] = "￥" . $value['price'] . "/日," . $value['content'];
                $list[$key]['rent_limit'] = 1;
                $list[$key]['rent_num'] = 1;
                if ($value['price'] == 0) {
                    unset($list[$key]);
                    continue;
                }
            } else {
                // 随动
                $list[$key]['price_type_text'] = '月';
                $price = [];
                foreach ($list[$key]['map'] as $k => $v) {
                    $price[] = '￥' . $v['price'];
                }
                sort($price);

                if ($value['time_type'] == 20 && count($price) == 1) {
                    if ($price[0] == '￥0') {
                        unset($list[$key]);
                        continue;
                    }
                    $list[$key]['content'] = '6个月以上：' . $price[0] . '/月.' . $value['content'];
                    $list[$key]['rent_limit'] = 6;
                    $list[$key]['rent_num'] = 6;
                }
                if ($value['time_type'] == 20 && count($price) == 2) {
                    if (($price[0] == '￥0' || $price[1] == '￥0')) {
                        unset($list[$key]);
                        continue;
                    }
                    $list[$key]['content'] = '1个月至2个月：' . $price[0] . '/月，3个月至5个月：' . $price[1] . '/月.' . $value['content'];
                    $list[$key]['rent_limit'] = 5;
                    $list[$key]['rent_num'] = 1;
                }
                if ($value['time_type'] == 30) {
                    if (($price[0] == '￥0' || $price[1] == '￥0')) {
                        unset($list[$key]);
                        continue;
                    }
                    $list[$key]['content'] = '1年（' . $price[0] . '/月）,2年（' . $price[1] . '/月）.' . $value['content'];
                    $list[$key]['rent_limit'] = 24;
                    $list[$key]['rent_num'] = 12;
                }
                $list[$key]['show_price'] = implode('~', $price);
            }
        }
        $list = array_values($list->toArray());
        return $list;
    }


    public function getInfo($rent_id, $goods_spec_id)
    {
        $rent_mode = Db::name('goods_spec')->where(['goods_spec_id' => $goods_spec_id])->value('rent_mode');
        $rent_mode = json_decode($rent_mode, true);

        $rent_obj = $this->where('id', $rent_id)->find();
        $map = json_decode($rent_obj['map'], true);
        if ($rent_obj['is_static'] == 0) {
            $rent_obj['price'] = $rent_mode['day']['price'];
        } else {
            foreach ($map as $k => $v) {
                if (isset($v['max'])) {
                    // 
                    if ($v['min'] == $v['max']) {
                        // year
                        if ($v['min'] == 12) {
                            $map[$k]['price'] = $rent_mode['year']['o'];
                        }
                        if ($v['min'] == 24) {
                            $map[$k]['price'] = $rent_mode['year']['t'];
                        }
                    } else {
                        // 1-2 & 3-5
                        if ($v['min'] == 1) {
                            // 1-2
                            $map[$k]['price'] = $rent_mode['month']['ot'];
                        }
                        if ($v['min'] == 3) {
                            // 3-5
                            $map[$k]['price'] = $rent_mode['month']['tf'];
                        }
                    }
                } else {
                    // 6+
                    $map[$k]['price'] = $rent_mode['month']['s'];
                }
            }
            $rent_obj['map'] = json_encode($map);
        }
        return $rent_obj;
    }
}
