<?php

/**
 * 后台菜单配置
 *    'home' => [
 *       'name' => '首页',                // 菜单名称
 *       'icon' => 'icon-home',          // 图标 (class)
 *       'index' => 'index/index',         // 链接
 *     ],
 */
return [
    'index' => [
        'name' => '首页',
        'icon' => 'icon-home',
        'index' => 'index/index',
    ],
    'goods' => [
        'name' => '产品管理',
        'icon' => 'icon-goods',
        'index' => 'goods/index',
        'submenu' => [
            [
                'name' => '产品分类',
                'index' => 'goods.category/index',
                'uris' => [
                    'goods.category/index',
                    'goods.category/add',
                    'goods.category/edit'
                ],
            ],
            [
                'name' => '产品列表',
                'index' => 'goods/index',
                'uris' => [
                    'goods/index',
                    'goods/add',
                    'goods/edit'
                ],
            ],
            [
                'name' => '产品服务',
                'index' => 'goods/service',
                'uris' => [
                    'goods/service',
                    'goods/service_add',
                    'goods/service_edit'
                ],
            ]
        ],
    ],
    'equip' => [
        'name' => '设备管理',
        'icon' => 'icon-goods',
        'index' => 'equip/index',
        'submenu' => [
            [
                'name' => '设备列表',
                'index' => 'equip/index',
            ],
            [
                'name' => '设备使用记录',
                'index' => 'equip/usingLog',
            ],
            [
                'name' => '维修记录',
                'index' => 'equip/checkLog',
            ]
        ]
    ],
    'exam' => [
        'name' => '审批管理',
        'icon' => 'icon-order',
        'index' => 'exam/index',
        'submenu' => [
            [
                'name' => '审核列表',
                'index' => 'exam/index',
            ],
        ]
    ],
    'order' => [
        'name' => '订单管理',
        'icon' => 'icon-order',
        'index' => 'order/delivery_list',
        'submenu' => [
            [
                'name' => '主订单',
                'index' => 'order/delivery_list',
                'submenu' => [
                    [
                        'name' => '待发货',
                        'index' => 'order/delivery_list',
                    ],
                    [
                        'name' => '待收货',
                        'index' => 'order/receipt_list',
                    ],
                    [
                        'name' => '待付款',
                        'index' => 'order/pay_list',
                    ],
                    [
                        'name' => '租赁中',
                        'index' => 'order/rent_list',
                    ],
                    [
                        'name' => '已完成',
                        'index' => 'order/complete_list',
        
                    ],
                    [
                        'name' => '已取消',
                        'index' => 'order/cancel_list',
                    ],
                    [
                        'name' => '全部订单',
                        'index' => 'order/all_list',
                    ],
                ]
            ],
            [
                'name' => '售后订单',
                'index' => 'order/order_after',
            ]
                     
        ]
    ],
    'notice' => [
        'name' => '通知管理',
        'icon' => 'icon-order',
        'index' => 'notice/index',
        'submenu' => [
            [
                'name' => '通知列表',
                'index' => 'notice/index'
            ]
        ]
    ],
    'finance' => [
        'name' => '财政管理',
        'icon' => 'icon-order',
        'index' => 'finance/account',
        'submenu' => [
            [
                'name' => '用户余额',
                'index' => 'finance/account'
            ],
            [
                'name' => '扣款订单',
                'index' => 'finance/deduct',
            ],
            [
                'name' => '扣款记录',
                'index' => 'finance/deduct_log',
            ],
            [
                'name' => '充值记录',
                'index' => 'finance/recharge',
            ],
            [
                'name' => '额度发放记录',
                'index' => 'finance/quota',
            ],
        ]
    ],
    'user' => [
        'name' => '用户管理',
        'icon' => 'icon-user',
        'index' => 'user/index'
    ],
    'member' => [
        'name' => '员工管理',
        'icon' => 'icon-user',
        'index' => 'member/index',
        'submenu' => [
            [
                'name' => '员工列表',
                'icon' => 'icon-user',
                'index' => 'member/index'
            ],
            [
                'name' => '角色管理',
                'icon' => 'icon-user',
                'index' => 'member/role'
            ],
            [
                'name' => '权限列表',
                'icon' => 'icon-user',
                'index' => 'member/privilege'
            ]

        ]

    ],
    'statics' => [
        'name' => '统计',
        'icon' => 'icon-goods',
        'index' => 'statics/index',
        'submenu' => [
            [
                'name' => '总统计',
                'icon' => 'icon-user',
                'index' => 'statics/index'
            ],
            [
                'name' => '日期统计',
                'icon' => 'icon-user',
                'index' => 'statics/time'
            ]        
        ]
    ],
//    'marketing' => [
//        'name' => '营销管理',
//        'icon' => 'icon-marketing',
//        'index' => 'marketing/index',
//        'submenu' => [],
//    ],
    'wxapp' => [
        'name' => '小程序',
        'icon' => 'icon-wxapp',
        'color' => '#36b313',
        'index' => 'wxapp/setting',
        'submenu' => [
            [
                'name' => '小程序设置',
                'index' => 'wxapp/setting',
            ],
            [
                'name' => '页面管理',
                'active' => true,
                'submenu' => [
                    [
                        'name' => '首页设计',
                        'index' => 'wxapp.page/home'
                    ],
                    [
                        'name' => '页面链接',
                        'index' => 'wxapp.page/links'
                    ],
                ]
            ],
            [
                'name' => '帮助中心',
                'index' => 'wxapp.help/index',
                'urls' => [
                    'wxapp.help/index',
                    'wxapp.help/add',
                    'wxapp.help/edit',
                    'wxapp.help/delete'
                ]
            ],
            [
                'name' => '导航设置',
                'index' => 'wxapp/tabbar'
            ],

        ],
    ],
    'plugins' => [
        'name' => '应用中心',
        'icon' => 'icon-application',
        'is_svg' => true,   // 多色图标
        'index' => 'plugins/index',
    ],
    'setting' => [
        'name' => '设置',
        'icon' => 'icon-setting',
        'index' => 'setting/store',
        'submenu' => [
            [
                'name' => '商城设置',
                'index' => 'setting/store',
            ],
            [
                'name' => '交易设置',
                'index' => 'setting/trade',
            ],
            [
                'name' => '配送设置',
                'index' => 'setting.delivery/index',
                'uris' => [
                    'setting.delivery/index',
                    'setting.delivery/add',
                    'setting.delivery/edit',
                ],
            ],
            [
                'name' => '短信通知',
                'index' => 'setting/sms'
            ],
            [
                'name' => '上传设置',
                'index' => 'setting/storage',
            ],
            [
                'name' => '其他',
                'active' => true,
                'submenu' => [
                    [
                        'name' => '清理缓存',
                        'index' => 'setting.cache/clear'
                    ],
                    [
                        'name' => '环境检测',
                        'index' => 'setting.science/index'
                    ],
                ]
            ]
        ],
    ],
];
