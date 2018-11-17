<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:72:"F:\demo\yuguJoy-pig\web/../source/application/store\view\equip\index.php";i:1542263608;s:68:"F:\demo\yuguJoy-pig\source\application\store\view\layouts\layout.php";i:1542093361;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title><?= $setting['store']['values']['name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="icon" type="image/png" href="assets/store/i/favicon.ico"/>
    <meta name="apple-mobile-web-app-title" content="<?= $setting['store']['values']['name'] ?>"/>
    <link rel="stylesheet" href="assets/store/css/amazeui.min.css"/>
    <link rel="stylesheet" href="assets/store/css/app.css"/>
    <link rel="stylesheet" href="//at.alicdn.com/t/font_783249_t6knt0guzo.css">
    <script src="assets/store/js/jquery.min.js"></script>
    <script src="//at.alicdn.com/t/font_783249_e5yrsf08rap.js"></script>
    <script>
        BASE_URL = '<?= isset($base_url) ? $base_url : '' ?>';
        STORE_URL = '<?= isset($store_url) ? $store_url : '' ?>';
    </script>
</head>

<body data-type="">
<div class="am-g tpl-g">
    <!-- 头部 -->
    <header class="tpl-header">
        <!-- 右侧内容 -->
        <div class="tpl-header-fluid">
            <!-- 侧边切换 -->
            <div class="am-fl tpl-header-button switch-button">
                <i class="iconfont icon-menufold"></i>
            </div>
            <!-- 刷新页面 -->
            <div class="am-fl tpl-header-button refresh-button">
                <i class="iconfont icon-refresh"></i>
            </div>
            <!-- 其它功能-->
            <div class="am-fr tpl-header-navbar">
                <ul>
                    <!-- 欢迎语 -->
                    <li class="am-text-sm tpl-header-navbar-welcome">
                        <a href="<?= url('store.user/renew') ?>">欢迎你，<span><?= $store['user']['user_name'] ?></span>
                        </a>
                    </li>
                    <!-- 退出 -->
                    <li class="am-text-sm">
                        <a href="<?= url('passport/logout') ?>">
                            <i class="iconfont icon-tuichu"></i> 退出
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- 侧边导航栏 -->
    <div class="left-sidebar">
        <?php $menus = $menus ?: []; $group = $group ?: 0; ?>
        <!-- 一级菜单 -->
        <ul class="sidebar-nav">
            <li class="sidebar-nav-heading"><?= $setting['store']['values']['name'] ?></li>
            <?php foreach ($menus as $key => $item): ?>
                <li class="sidebar-nav-link">
                    <a href="<?= isset($item['index']) ? url($item['index']) : 'javascript:void(0);' ?>"
                       class="<?= $item['active'] ? 'active' : '' ?>">
                        <?php if (isset($item['is_svg']) && $item['is_svg'] === true): ?>
                            <svg class="icon sidebar-nav-link-logo" aria-hidden="true">
                                <use xlink:href="#<?= $item['icon'] ?>"></use>
                            </svg>
                        <?php else: ?>
                            <i class="iconfont sidebar-nav-link-logo <?= $item['icon'] ?>"
                               style="<?= isset($item['color']) ? "color:{$item['color']};" : '' ?>"></i>
                        <?php endif; ?>
                        <?= $item['name'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- 子级菜单-->
        <?php $second = isset($menus[$group]['submenu']) ? $menus[$group]['submenu'] : []; if (!empty($second)) : ?>
            <ul class="left-sidebar-second">
                <li class="sidebar-second-title"><?= $menus[$group]['name'] ?></li>
                <li class="sidebar-second-item">
                    <?php foreach ($second as $item) : if (!isset($item['submenu'])): ?>
                            <!-- 二级菜单-->
                            <a href="<?= url($item['index']) ?>" class="<?= $item['active'] ? 'active' : '' ?>">
                                <?= $item['name']; ?>
                            </a>
                        <?php else: ?>
                            <!-- 三级菜单-->
                            <div class="sidebar-third-item">
                                <a href="javascript:void(0);"
                                   class="sidebar-nav-sub-title <?= $item['active'] ? 'active' : '' ?>">
                                    <i class="iconfont icon-caret"></i>
                                    <?= $item['name']; ?>
                                </a>
                                <ul class="sidebar-third-nav-sub">
                                    <?php foreach ($item['submenu'] as $third) : ?>
                                        <li>
                                            <a class="<?= $third['active'] ? 'active' : '' ?>"
                                               href="<?= url($third['index']) ?>">
                                                <?= $third['name']; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; endforeach; ?>
                </li>
            </ul>
        <?php endif; ?>
    </div>

    <!-- 内容区域 start -->
    <div class="tpl-content-wrapper <?= empty($second) ? 'no-sidebar-second' : '' ?>">
        <div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf">设备列表</div>
                </div>
                <div class="widget-body am-fr">
                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-6">
                        <div class="am-form-group">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-btn-success am-radius"
                                       href="<?= url('equip/add') ?>">
                                        <span class="am-icon-plus"></span> 新增
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 搜索栏 -->
                    <form method="GET" action="" id="form">
                        <div class="am-form-group" style="position:absolute;right:20px;">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius"
                                    href="javascript:;">
                                        <span class="am-icon-home"></span> 所属产品                                        
                                    </a>
                                    <select name="goods_id"  id="goods_id" style="font-size:12px;">
                                        <option value=""></option>
                                        <?php if (isset($goods)) : foreach ($goods as $first) : ?>
                                            <option value="<?= $first['goods_id'] ?>"
                                            <?php if (isset($map['goods_id']) && $map['goods_id'] == $first['goods_id']) : ?> selected 
                                            <?php endif; ?> 
                                            >
                                                <?= $first['goods_name'] ?>
                                            </option>                                            
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>                                
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">
                                    <a class="am-btn am-btn-default am-radius "
                                    href="javascript:;">
                                        <span class="am-icon-phone"></span> 状态                                        
                                    </a>
                                    <select name="status"  id="status" style="font-size:12px;"
                                            >
                                        <option value=""></option>                                       
                                        <option value="10" <?php if (isset($map['status']) && $map['status'] == 10) : ?> selected <?php endif; ?>>在库</option>
                                        <option value="20" <?php if (isset($map['status']) && $map['status'] == 20) : ?> selected <?php endif; ?>>运送中</option>
                                        <option value="30" <?php if (isset($map['status']) && $map['status'] == 30) : ?> selected <?php endif; ?>>使用中</option>
                                        <option value="40" <?php if (isset($map['status']) && $map['status'] == 40) : ?> selected <?php endif; ?>>维修中</option>
                                        <option value="50" <?php if (isset($map['status']) && $map['status'] == 50) : ?> selected <?php endif; ?>>停用</option>                                        
                                    </select>
                                </div>
                                <div class="am-btn-group am-btn-group-xs" style="display:flex;">                                    
                                    <a type="button" class="am-btn am-btn-default am-btn-primary am-margin-right" id="my-start">服役开始日期</a>
                                    <input type="text" class="am-form-field" style="padding: 3px 5px;" name="service_time" id="service_time" value="<?= isset($map['service_time']) ? $map['service_time'] : "" ?>">                                    
                                </div>

                                <div class="am-btn-group am-btn-group-xs">
                                    <a class="am-btn am-btn-default am-btn-success am-radius" id="search"
                                    href="javascript:;">
                                        <span class="am-icon-search"></span> 搜索
                                    </a>
                                </div>
                            </div>                            
                        </div>
                    </form>
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>设备ID</th>
                                <th>所属产品</th>
                                <th>对应型号</th>                                
                                <th>设备排序</th>
                                <th>设备状态</th>
                                <th>服役开始时间</th>
                                <th>二维码</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()) : foreach ($list as $item) : ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['equip_id'] ?></td>                                    
                                    <td class="am-text-middle">
                                        <p class="item-title"><?= $item['goods']['goods_name'] ?></p>
                                    </td>
                                    <td>
                                        <p class="item-title"><?= $item['spec_value']['spec_value'] ?></p>
                                    </td>                                    
                                    <td class="am-text-middle"><?= $item['sort'] ?></td>
                                    <td class="am-text-middle <?= $item['status']==30?'am-primary':($item['status']==20?'am-success':'')?>"><?= $item['status_text'] ?></td>                                    
                                    <td class="am-text-middle"><?= $item['service_time']?date('Y-m-d',$item['service_time']):'' ?></td>
                                    <td class="am-text-middle">
                                        <a href="<?= url(
                                                    'equip/madeQrCode',
                                                    ['equip_id' => $item['equip_id']]
                                                ) ?>" target="_blank;">
                                            <i class="am-icon-pen"></i> 生成
                                        </a>
                                    </td>
                                    <td class="am-text-middle">
                                    <?php if ($item['status'] == 10): ?>
                                        <div class="tpl-table-black-operation">
                                            <a href="<?= url(
                                                        'equip/edit',
                                                        ['equip_id' => $item['equip_id']]
                                                    ) ?>">
                                                <i class="am-icon-pencil"></i> 编辑
                                            </a>                                            
                                            <a href="javascript:;" class="item-delete tpl-table-black-operation-del"
                                               data-id="<?= $item['equip_id'] ?>">
                                                <i class="am-icon-trash"></i> 删除
                                            </a>
                                        </div>
                                    <?php else :?>
                                    <div class="tpl-table-black-operation">                                                                                                                               
                                        <a href="<?= url(
                                                    'order/detail',
                                                    ['order_id' => $item['order_id']]
                                                ) ?>" class="am-btn am-btn-xs am-radius">
                                            <i class="am-icon-book"></i> 查看归属订单
                                        </a> 
                                    </div>

                                    <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                            else : ?>
                                <tr>
                                    <td colspan="9" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="am-u-lg-12 am-cf">
                        <div class="am-fr"><?= $list->render() ?> </div>
                        <div class="am-fr pagination-total am-margin-right">
                            <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        // 
        var b = "<?= isset($map['service_time']) ? $map['service_time'] : "" ?>";        
        var service_time = new Date(b);                                  
        // 
        $('#my-start').datepicker().on('changeDate.datepicker.amui', function(event) {                     
            service_time = new Date(event.date);                
            $('#service_time').val($('#my-start').data('date'));            
            $(this).datepicker('close');
        });

        // 
        $('#search').on('click', function(e) {
            var url = "<?php echo url('equip/index') ?>";
            var param = $('#form').serialize();
            var html = url + '&' + param;
            window.location.href = html;            
        });    


        // 删除元素
        var url = "<?= url('equip/delete') ?>";
        $('.item-delete').delete('equip_id', url);

    });
</script>


    </div>
    <!-- 内容区域 end -->

</div>
<script src="assets/layer/layer.js"></script>
<script src="assets/store/js/jquery.form.min.js"></script>
<script src="assets/store/js/amazeui.min.js"></script>
<script src="assets/store/js/webuploader.html5only.js"></script>
<script src="assets/store/js/art-template.js"></script>
<script src="assets/store/js/app.js"></script>
<script src="assets/store/js/file.library.js"></script>
</body>

</html>
