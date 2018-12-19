<style>
.am-text-nowrap {
    white-space: unset;
}
.am-service{
    /* margin-bottom:unset; */
}
.order-detail-progress li{
    width:16.6666%;
}



.order-detail-progress.progress-7 li:nth-child(7) {
  color: #fff;
}

</style>

<link rel="stylesheet" href="assets/layui/css/layui.css"  media="all">


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
                        $detail['order_status']['value'] === 30 && $progress += 1;
                        $detail['done_status']['value'] === 20 && $progress += 1;
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
                                <span>租赁中</span>
                                <?php if ($detail['order_status']['value'] === 30) : ?>
                                    <div class="tip">
                                        租赁于 <?= date('Y-m-d H:i:s', $detail['receipt_time']) ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                            <li>
                                <span>完成</span>
                                <?php if ($detail['done_status']['value'] === 20) : ?>
                                    <div class="tip">
                                        完成于 <?= date('Y-m-d H:i:s', $detail['done_time']) ?>
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
                            <input type="hidden" name="member_ids" id="member_ids" value="">
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
                                        <td><?= $item['services'] ? : '无' ?></td>
                                        <td><?= $item['status_text'] ?></td>
                                        <!-- <td><?= $item['equip_id'] ?></td> -->
                                    </tr>     
                                <?php endforeach; ?>
                            </tbody>                                                                    
                        </table>        


                        <!--  -->
                        <div class="widget-head am-cf" style="position:relative;">
                            <div class="widget-title am-fl">配送人员</div> 
                        </div>                                                                                                                   
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
                                <?php foreach ($member_list as $item) : ?>
                                    <tr>
                                        <td><?= $item['id'] ?></td>
                                        <td><?= $item['name'] ?></td>
                                        <td><?= $item['role']['role_name'] ?></td>
                                        <td><?= $item['phone'] ?></td>                                        
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
                    <?php endif; ?>
                                        


                    <?php if ($detail['pay_status']['value'] === 20) : ?>                        
                        <?php if ($detail['delivery_status']['value'] === 10) : ?>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">配送人员</div>
                            </div>                                                                                                                
                            <div class="am-form-group">
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
                           
                        <?php else : ?>                                                         
                        <?php endif;
                        endif; ?>                                                                                                                                         

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
                            service += '<input class="equip-radio" type="radio" name="equip['+data[i]['equip_id']+'][secure]"  value="0" data-am-ucheck checked>标准保</label><label class="am-checkbox-inline">';
                            service += '<input class="equip-radio" type="radio" name="equip['+data[i]['equip_id']+'][secure]" value="1" data-am-ucheck>意外保</label></div></td>';
                            service += '<td><div class="am-form-group am-service">'
                            var service_data = data[i]['goods']['service'];
                            for(var j in service_data){                                
                                service += '<label class="am-checkbox-inline am-secondary">';
                                service += '<input class="equip-checkbox" type="checkbox" name="equip['+data[i]['equip_id']+'][service][]" value="'+service_data[j]['service_id']+'" data-am-ucheck>'+service_data[j]['service_name']+'</label>'
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
                    $('.equip-radio,.equip-checkbox').uCheck();             
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
