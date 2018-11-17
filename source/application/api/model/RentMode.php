<?php

namespace app\api\model;

use app\common\model\RentMode as RentModeModel;

/**
 * 模型
 * Class
 * @package app\api\model
 */
class RentMode extends RentModeModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
    ];
    
}
