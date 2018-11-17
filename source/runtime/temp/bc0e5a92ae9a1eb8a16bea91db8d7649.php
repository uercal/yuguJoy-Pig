<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:73:"F:\demo\yuguJoy-pig\web/../source/application/store\view\order\detail.php";i:1542262882;s:68:"F:\demo\yuguJoy-pig\source\application\store\view\layouts\layout.php";i:1542093361;}*/ ?>
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
        <style>
.am-text-nowrap {
    white-space: unset;
}
.am-service{
    /* margin-bottom:unset; */
}
</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">
                    <div class="am-u-sm-12">
                        <?php
                        // 计算当前步骤位置
                        $progress = 2;
                        $detail['pay_status']['value'] === 20 && $progress += 1;
                        $detail['delivery_status']['value'] === 20 && $progress += 1;
                        $detail['receipt_status']['value'] === 20 && $progress += 1;
                        // $detail['order_status']['value'] === 30 && $progress += 1;
                        ?>
                        <ul class="order-detail-progress progress-<?= $progress ?>">
                            <li>
                                <span>下单时间</span>
                                <div class="tip"><?= $detail['create_time'] ?></div>
                            </li>
                            <li>
                                <span>付款</span>
                                <?php if ($detail['pay_status']['value'] === 20) : ?>
                                    <div class="tip">
                                        付款于 <?= date('Y-m-d H:i:s', $detail['pay_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>发货</span>
                                <?php if ($detail['delivery_status']['value'] === 20) : ?>
                                    <div class="tip">
                                        发货于 <?= date('Y-m-d H:i:s', $detail['delivery_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>收货</span>
                                <?php if ($detail['receipt_status']['value'] === 20) : ?>
                                    <div class="tip">
                                        收货于 <?= date('Y-m-d H:i:s', $detail['receipt_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>完成</span>
                                <?php if ($detail['order_status']['value'] === 30) : ?>
                                    <div class="tip">
                                        完成于 <?= date('Y-m-d H:i:s', $detail['receipt_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">基本信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>订单号</th>
                                <th>实付款</th>
                                <th>买家</th>
                                <th>交易状态</th>
                            </tr>
                            <tr>
                                <td><?= $detail['order_no'] ?></td>
                                <td>
                                    <p>￥<?= $detail['pay_price'] ?></p>
                                    <p class="am-link-muted">(含运费：￥<?= $detail['express_price'] ?>)</p>
                                </td>
                                <td>
                                    <p><?= $detail['user']['nickName'] ?></p>
                                    <p class="am-link-muted">(用户id：<?= $detail['user']['user_id'] ?>)</p>
                                </td>
                                <td>
                                    <p>付款状态：
                                        <span class="am-badge
                                        <?= $detail['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['pay_status']['text'] ?></span>
                                    </p>
                                    <p>发货状态：
                                        <span class="am-badge
                                        <?= $detail['delivery_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['delivery_status']['text'] ?></span>
                                    </p>
                                    <p>收货状态：
                                        <span class="am-badge
                                        <?= $detail['receipt_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['receipt_status']['text'] ?></span>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">商品信息(大于3月先收3月租金)</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>商品名称(悬浮查看型号)</th>                                                                                                                         
                                <th>租赁模式(悬浮查看内容)</th>
                                <th>增值服务</th>
                                <th>起租时间</th>
                                <th>起租时长</th>
                                <th>租金单价</th>                                                            
                                <th>租金</th>
                                <th>押金</th>
                                <th>购买数量</th>
                                <th>总价</th>
                            </tr>
                            <?php foreach ($detail['goods'] as $goods) : ?>                            
                                <tr>
                                    <td class="goods-detail am-text-middle rent_content" data-content="型号:<?= $goods['spec_value'] ?>">
                                        <div class="goods-image">
                                            <img src="<?= $goods['image']['file_path'] ?>" alt="">
                                        </div>
                                        <div class="goods-info">
                                            <p class="goods-title"><?= $goods['goods_name'] ?></p>
                                            <p class="goods-spec am-link-muted">
                                                <?= $goods['goods_attr'] ?>
                                            </p>
                                        </div>
                                    </td>                                          
                                    <td data-content="<?= $goods['rent_mode']['content'] ?>" class="rent_content"><?= $goods['rent_mode']['name'] ?></td>
                                    <!-- 增值服务 -->
                                    <td style="font-size:12px;">
                                        <p><?= $goods['secure'] == 0 ? '标准保(免费)' : '意外保' ?></p>                                    
                                        <?php foreach ($goods['service_info'] as $i) : ?>
                                            <p><?= $i['service_name'] ?></p>
                                        <?php endforeach; ?>                                        
                                    </td>
                                    <td><?= date('Y-m-d', $goods['rent_date']) ?></td>
                                    <td>
                                        <?php if ($goods['rent_mode']['is_static'] == 0) : ?><?= $goods['rent_num'] ?>天
                                        <?php else : ?><?= $goods['rent_num'] ?>个月<?php if ($goods['rent_num'] > 3) : ?><p style="color:#777;font-size:12px;">(大于3个月先收3月租金)</p>
                                        <?php endif;
                                        endif; ?>
                                    </td>         
                                    <td>￥<?= $goods['rent_price'] ?></td>                           
                                    <td>￥<?= $goods['rent_total_price'] ?></td>
                                    <td>￥<?= $goods['goods_price'] ?></td>
                                    <td>×<?= $goods['total_num'] ?></td>
                                    <td>￥<?= $goods['total_price'] ?></td>
                                </tr>
                                
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="10" class="am-text-right">总计金额：￥<?= $detail['total_price'] ?></td>                                
                            </tr>                            
                            </tbody>
                        </table>

                        <?php if ($detail['pay_status']['value'] === 20 && $detail['delivery_status']['value'] === 10) : ?>
                        <div class="widget-head am-cf" style="position:relative;">
                            <div class="widget-title am-fl">发货设备</div>
                            <div style="right:0;position:absolute;">                                              
                                <small>输入设备id（如1000,10001）</small>                                                                                            
                                <input type="text" class="tpl-form-input" id="equip_ids"
                                        required>                                                                                                        
                                <button class="am-btn am-btn-default am-btn-success am-radius" id="addEquip">添加设备</button>
                            </div>
                        </div>
                        <form id="delivery" class="my-form am-form tpl-form-line-form" method="post"
                                  action="<?= url('order/delivery', ['order_id' => $detail['order_id']]) ?>">  
                            <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                <thead>
                                    <tr>
                                        <th>设备id</th>
                                        <th>所属产品</th>
                                        <th>型号/类型</th>
                                        <th>保修</th>
                                        <th>增值服务</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>                                
                                <tbody id="equip">
                                        
                                    
                                </tbody>                                                                    
                            </table>
                            <button type="submit" id="delivery_sub" style="display:none;"></button>
                        </form>

                        <?php elseif ($detail['pay_status']['value'] === 20 && $detail['delivery_status']['value'] === 20) : ?>
                        <div class="widget-head am-cf" style="position:relative;">
                            <div class="widget-title am-fl">发货设备</div> 
                        </div>                                                                                                                   
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <thead>
                                <tr>
                                    <th>设备id</th>
                                    <th>所属产品</th>
                                    <th>型号/类型</th>
                                    <th>保修</th>
                                    <th>增值服务</th>
                                    <th>状态</th>
                                    <!-- <th>操作</th> -->
                                </tr>
                            </thead>                                
                            <tbody>
                                <?php foreach ($detail['equip'] as $item) : ?>
                                    <tr>
                                        <td><?= $item['equip_id'] ?></td>
                                        <td><?= $item['goods_name'] ?></td>
                                        <td><?= $item['spec_value']['spec_value'] ?></td>
                                        <td><?= $item['secure'] == 0 ? '标准保' : '意外保' ?></td>
                                        <td><?= $item['services']?:'无' ?></td>
                                        <td><?= $item['status_text'] ?></td>
                                        <!-- <td><?= $item['equip_id'] ?></td> -->
                                    </tr>     
                                <?php endforeach; ?>
                            </tbody>                                                                    
                        </table>                        
                        <?php endif; ?>

                    

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">收货信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>收货人</th>
                                <th>收货电话</th>
                                <th>收货地址</th>
                            </tr>
                            <tr>
                                <td><?= $detail['address']['name'] ?></td>
                                <td><?= $detail['address']['phone'] ?></td>
                                <td>
                                    <?= $detail['address']['region']['province'] ?>
                                    <?= $detail['address']['region']['city'] ?>
                                    <?= $detail['address']['region']['region'] ?>
                                    <?= $detail['address']['detail'] ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($detail['pay_status']['value'] === 20) : ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">付款信息</div>
                        </div>
                        <div class="am-scrollable-horizontal">
                            <table class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs">
                                <tbody>
                                <tr>
                                    <th>应付款金额</th>
                                    <th>支付方式</th>
                                    <th>支付流水号</th>
                                    <th>付款状态</th>
                                    <th>付款时间</th>
                                </tr>
                                <tr>
                                    <td>￥<?= $detail['pay_price'] ?></td>
                                    <td>微信支付</td>
                                    <td><?= $detail['transaction_id'] ? : '--' ?></td>
                                    <td>
                                        <span class="am-badge
                                        <?= $detail['pay_status']['value'] === 20 ? 'am-badge-success' : '' ?>">
                                                <?= $detail['pay_status']['text'] ?></span>
                                    </td>
                                    <td>
                                        <?= $detail['pay_time'] ? date('Y-m-d H:i:s', $detail['pay_time']) : '--' ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; if ($detail['pay_status']['value'] === 20) : if ($detail['delivery_status']['value'] === 10) : ?>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">发货信息</div>
                            </div>
                            <!-- 去发货 -->
                                                                                          
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary" id="delivery_btn">
                                        确认发货
                                    </button>
                                </div>
                            </div>
                           
                        <?php else : endif;
                        endif; ?>                                                                                                                                         

                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('.my-form').superForm();
        

        $('#delivery').superForm({
            // 自定义验证
            validation: function () {                
                return true;
            }
        })

        $(document).on('mouseenter', '.rent_content', function(){            
            var content = $(this).attr('data-content');               
            tip_index = layer.tips(content, $(this), {time: 0});
        }).on('mouseleave', '.rent_content', function(){
            layer.close(tip_index);
        });

      
        $('#addEquip').on('click',function(){            
            var ids = $('#equip_ids').val();
            var s = is_exist(ids);            
            if(!s){
                layer.msg('请勿重复添加设备');return false;
            }else{
                $.post('<?= url('order/addEquipList') ?>',{ids:ids},function(res){
                    if(res.code==1){
                        layer.msg(res.msg);
                    }
                    var data = res.data;
                    var html = "";                    
                    if(data.length>0){
                        for (var i in data){
                            // 服务html
                            var service = '<td><div class="am-form-group am-service"><label class="am-checkbox-inline">';
                            service += '<input type="radio" name="equip['+data[i]['equip_id']+'][secure]"  value="0" data-am-ucheck checked>标准保</label><label class="am-checkbox-inline">';
                            service += '<input type="radio" name="equip['+data[i]['equip_id']+'][secure]" value="1" data-am-ucheck>意外保</label></div></td>';
                            service += '<td><div class="am-form-group am-service">'
                            var service_data = data[i]['goods']['service'];
                            for(var j in service_data){                                
                                service += '<label class="am-checkbox-inline am-secondary">';
                                service += '<input type="checkbox" name="equip['+data[i]['equip_id']+'][service][]" value="'+service_data[j]['service_id']+'" data-am-ucheck>'+service_data[j]['service_name']+'</label>'
                            }                            
                            service += '</div></td>';
                            // 
                            html += "<tr equip-id='"+ data[i]['equip_id'] +"'><td data-type='equip_id'><input type='hidden' name='equip[]' value='"+data[i]['equip_id']+"' disabled>"+data[i]['equip_id']+"</td>";                            
                            html += "<td>"+data[i]['goods_name']+"</td>";
                            html += "<td>"+data[i]['spec_value']['spec_value']+"</td>";
                            html += service;
                            html += "<td><button class='am-btn am-btn-default-sm am-btn-danger am-radius' onclick='delEquip(this)'>删除</button></td></tr>";                            
                        }
                    }
                    $('#equip').append(html);   
                    $('input[type="checkbox"],input[type="radio"]').uCheck();             
                })
            }   
            
        });



        $('#delivery_btn').on('click',function(){
            $('#delivery_sub').click();
        })


        function is_exist(ids){
            // 
            ids = ids.replace('，',',');
            ids = ids.split(',');   
            // 
            var done_ids = [];
            $('input[name="equip[]"]').map(function(){                
                done_ids.push($(this).val());
            })
            console.log({ids,done_ids});
            for(var i in ids){
                var index = $.inArray(ids[i],done_ids);                
                if(index>=0) return false;
            }
            return true;
        }
                
    });


    function delEquip(e){
        console.log($(e).parent().parent().remove());
    }
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
