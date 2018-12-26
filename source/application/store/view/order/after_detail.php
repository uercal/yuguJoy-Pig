<style>
.am-text-nowrap {
    white-space: unset;
}
.am-service{
    /* margin-bottom:unset; */
}
.order-detail-progress li{
    width:20%;
}


.am-form textarea[disabled]{
    cursor: not-allowed;
    background-color: #fff !important;
}
</style>

<link rel="stylesheet" href="assets/layui/css/layui.css"  media="all">


<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">    
                    <!-- progress -->
                    <div class="am-u-sm-12">
                        <?php
                        // 计算当前步骤位置
                        $progress = 0;
                        $after['status'] == 10 ? $progress = 2 : '';
                        $after['status'] == 20 ? $progress = 3 : '';
                        $after['status'] == 30 ? $progress = 4 : '';
                        $after['pay_status'] == 20 ? $progress = 5 : '';
                        $after['status'] == 40 ? $progress = 6 : '';
                        ?>
                        <ul class="order-detail-progress progress-after-<?= $progress ?>">
                            <li>
                                <span>已发起</span>
                                <div class="tip"><?= $after['create_time'] ?></div>
                            </li>
                            <li>
                                <span>进行中</span>                                
                            </li>
                            <li>
                                <span>返修中</span>
                            </li>
                            <li>
                                <span>未付款</span>
                            </li>
                            <li>
                                <span>已完成</span>
                                <?php if ($after['status'] === 40) : ?>
                                    <div class="tip">
                                        <?= date('Y-m-d H:i:s', $after['pay_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>                            
                        </ul>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">售后信息</div>
                    </div>
                    <div class="am-scrollable-horizontal">
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>售后单号</th>                                
                                <th>用户</th>
                                <th>结款金额</th>                                
                                <th>交易状态</th>
                                <th>售后单状态</th>
                            </tr>
                            <tr>
                                <td><?= $after['after_no'] ?></td>                                
                                <td>
                                    <a href="<?= $detail['user']['avatarUrl'] ?>" title="点击查看大图" target="_blank">
                                        <img src="<?= $detail['user']['avatarUrl'] ?>" width="72" height="72" alt="">
                                    </a>
                                    <p><?= $detail['user']['nickName'] ?></p>
                                    <p class="am-link-muted">(用户id：<?= $detail['user']['user_id'] ?>)</p>
                                </td>
                                <td>
                                    <?= $after['pay_price'] ?>
                                </td>
                                <td>
                                    <?= $after['pay_status'] == 10 ? '未支付' : '已支付' ?>
                                </td>
                                <td>
                                    <?= $after['status_text'] ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">地址信息</div>
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
                                                                                                                                               


                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">售后发起说明</div>
                        </div>                     
                        
                        <form id="my-form" class="am-form tpl-form-line-form" method="post">
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label" style="width:auto;padding-top:unset;">故障说明:</label>
                                <div class="am-u-sm-9 am-u-end">                                    
                                    <textarea style="border:1px solid #ccc;" cols="30" rows="10" disabled="disabled"><?= $after['request_text'] ?></textarea>
                                </div>
                            </div>        

                            <?php if (!empty($after['request_pics'])) : ?>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label" style="width:auto;padding-top:unset;"> 售后图片说明: </label>                                
                                <div class="am-u-sm-9 am-u-end">     
                                    <?php foreach ($after['request_pics'] as $pic) : ?>                                                                              
                                    <a href="<?= $pic['file_path'] ?>" title="点击查看大图" target="_blank" style="margin-right:10px;">
                                        <img name="" src="<?= $pic['file_path'] ?>" width="72" height="72" alt="">
                                    </a>                                          
                                    <?php endforeach; ?>                                                                                   
                                </div>                               
                            </div>
                            <?php endif; ?>

                            <input type="hidden" name="after[member_ids]" id="member_ids" value="">
                            <button type="submit" id="_sub" style="display:none;"></button>
                        </form>


                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">派遣人员</div>
                        </div>                  
                                    
                        

                        <?php if ($after['status'] == 10) : ?>
                        <div class="am-form-group" style="padding-left:20px;">
                            <table class="layui-table" lay-data="{width: 628, height:332, url:'<?= url('order/getMemberAjax') ?>', page:true, id:'idTest'}" lay-filter="demo">
                                <thead>
                                    <tr>
                                    <th lay-data="{type:'checkbox', width:60 }"></th>
                                    <th lay-data="{field:'id', width:80, sort: true}">ID</th>
                                    <th lay-data="{field:'name', width:160}">用户名</th>
                                    <th lay-data="{field:'phone', width:160, sort: true}">手机号</th>
                                    <th lay-data="{field:'role_name', width:80}">角色</th>
                                    <th lay-data="{field:'status_text', width:80,sort:true}">状态</th>                                                                
                                    </tr>
                                </thead>
                            </table>                    
                        </div>


                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary" id="sub">
                                    派单
                                </button>
                            </div>
                        </div>  
                        
                        <?php else : ?>

                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <thead>
                                <tr>
                                    <th>人员id</th>
                                    <th>姓名</th>
                                    <th>角色</th>
                                    <th>电话</th>                                    
                                </tr>
                            </thead>                                
                            <tbody>
                                <?php foreach ($after['members'] as $item) : ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td><?= $item['name'] ?></td>
                                        <td><?= $item['role_name'] ?></td>
                                        <td><?= $item['phone'] ?></td>                                        
                                    </tr>                                    
                                <?php endforeach; ?>
                            </tbody>                                                                    
                        </table>       

                        <?php endif; ?>







                        <!-- -->                                        
                        <?php if ($after['type'] == 10) : ?>
                        <div class="widget-head am-cf" style="position:relative;">
                            <div class="widget-title am-fl">维修设备</div> 
                        </div>                                                                                                                   
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <thead>
                                <tr>
                                    <th>维修类型</th>
                                    <th>设备id</th>
                                    <th>所属产品</th>
                                    <th>型号/类型</th>
                                    <th>保修</th>
                                    <th>增值服务</th>
                                    <th>状态</th>                                    
                                </tr>
                            </thead>                                
                            <tbody>
                                <?php foreach ($after['checked_equip'] as $item) : ?>
                                    <tr>
                                        <td>即时维修</td>
                                        <td><?= $item['equip_id'] ?></td>
                                        <td><?= $item['goods_name'] ?></td>
                                        <td><?= $item['spec_value']['spec_value'] ?></td>
                                        <td><?= $item['secure'] == 0 ? '标准保' : '意外保' ?></td>
                                        <td><?= $item['services'] ? : '无' ?></td>
                                        <td><?= $item['status_text'] ?></td>                                        
                                    </tr>     
                                <?php endforeach; ?>
                                <?php foreach ($after['exchange_equip'] as $item) : ?>
                                    <tr>
                                        <td>以换代修-旧设备</td>
                                        <td><?= $item['equip_id'] ?></td>
                                        <td><?= $item['goods_name'] ?></td>
                                        <td><?= $item['spec_value']['spec_value'] ?></td>
                                        <td><?= $item['secure'] == 0 ? '标准保' : '意外保' ?></td>
                                        <td><?= $item['services'] ? : '无' ?></td>
                                        <td><?= $item['status_text'] ?></td>                                        
                                    </tr>     
                                <?php endforeach; ?>
                                <?php foreach ($after['new_equip'] as $item) : ?>
                                    <tr>
                                        <td>以换代修-新设备</td>
                                        <td><?= $item['equip_id'] ?></td>
                                        <td><?= $item['goods_name'] ?></td>
                                        <td><?= $item['spec_value']['spec_value'] ?></td>
                                        <td><?= $item['secure'] == 0 ? '标准保' : '意外保' ?></td>
                                        <td><?= $item['services'] ? : '无' ?></td>
                                        <td><?= $item['status_text'] ?></td>                                        
                                    </tr>     
                                <?php endforeach; ?>
                                <?php foreach ($after['back_equip'] as $item) : ?>
                                    <tr>
                                        <td>返修设备</td>
                                        <td><?= $item['equip_id'] ?></td>
                                        <td><?= $item['goods_name'] ?></td>
                                        <td><?= $item['spec_value']['spec_value'] ?></td>
                                        <td><?= $item['secure'] == 0 ? '标准保' : '意外保' ?></td>
                                        <td><?= $item['services'] ? : '无' ?></td>
                                        <td><?= $item['status_text'] ?></td>                                        
                                    </tr>     
                                <?php endforeach; ?>
                            </tbody>                                                                    
                        </table>        
                        <?php endif; ?>


                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">售后处理说明</div>
                        </div>                     
                        
                        <form id="my-form" class="am-form tpl-form-line-form" method="post">
                            <?php if (!empty($after['check_text'])) : ?>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label" style="width:auto;padding-top:unset;">结果说明:</label>
                                <div class="am-u-sm-9 am-u-end">                                    
                                    <textarea style="border:1px solid #ccc;" cols="30" rows="10" disabled="disabled"><?= $after['check_text'] ?></textarea>
                                </div>
                            </div>        
                            <?php endif; ?>

                            <?php if (!empty($after['check_pics'])) : ?>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label" style="width:auto;padding-top:unset;"> 图片说明: </label>                                
                                <div class="am-u-sm-9 am-u-end">     
                                    <?php foreach ($after['check_pics'] as $pic) : ?>                                                                              
                                    <a href="<?= $pic['file_path'] ?>" title="点击查看大图" target="_blank" style="margin-right:10px;">
                                        <img name="" src="<?= $pic['file_path'] ?>" width="72" height="72" alt="">
                                    </a>                                          
                                    <?php endforeach; ?>                                                                                   
                                </div>                               
                            </div>
                            <?php endif; ?>
                            
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label" style="width:auto;padding-top:unset;"> 售后单状态: </label>                                
                                <div class="am-u-sm-9 am-u-end" style="font-weight:bold;">                                         
                                        <?= $after['status_text'] ?>
                                </div>                               
                            </div>

                        </form>                                                
                        
                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/layui/layui.all.js" charset="utf-8"></script>
<script>

layui.use('table', function(){
    var table = layui.table;                                    
    table.on('checkbox(demo)', function(obj){
        var checkStatus = table.checkStatus('idTest');
        var member_ids = [];
        checkStatus.data.map(function(e){
            member_ids.push(e.id);
        });
        console.log(member_ids);
        $('#member_ids').attr('value','');
        $('#member_ids').attr('value',member_ids);
        // console.log(obj.checked); //当前是否选中状态
        // console.log(obj.data); //选中行的相关数据
        // console.log(obj.type); //如果触发的是全选，则为：all，如果触发的是单选，则为：one
    });                  
});
</script>
<script>
    $(function () {
       
        /**
         * 表单验证提交
         * @type {*}
         */
        
        $('#my-form').superForm({
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


        $('#sub').on('click',function(){                           
            $('#_sub').click();
        })                
    });

  
</script>
