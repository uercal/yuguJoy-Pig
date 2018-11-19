<style>

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button{
	-webkit-appearance: none !important;
	margin: 0;
}

.am-selected-list{
    font-size:1.2rem;
}

.am-input-group-label{
    border:0;
    background-color:#fff;
}

.rent-num-input{
    width:50% !important;
}

.rent-num-div{
    display: flex;
    align-items: center;
    justify-content: center;    
}

.am-text-nowrap {
    white-space: unset;
}

.am-service{
    margin-bottom:unset;
}

</style>

<div class="row-content am-cf">
    <form id="my-form" method="post">
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
                                    <input style="width:auto;text-align:center;border: 1px solid #c2cad8;margin:0 auto;" 
                                    type="number" name="order[pay_price]" class="am-form-field am-round" id="pay_price" 
                                    value="<?= $detail['pay_price'] ?>" <?= $detail['pay_status']['value'] == 20 ? 'disabled' : '' ?> >
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
                            am-text-nowrap am-margin-bottom-xs" style="margin-bottom:80px;">
                            <tbody>
                            <tr>
                                <th>商品名称(悬浮查看型号)</th>  
                                <th>增值服务</th>                                                                                          
                                <th>租赁模式</th>                                
                                <th>租赁说明</th>
                                <th>起租时间</th>
                                <th>起租时长</th>
                                <!-- <th>租金单价</th>                                                              
                                <th>租金</th> -->
                                <th>押金</th>
                                <th>购买数量</th>
                                <th>总价</th>
                                <!-- <th>操作</th> -->
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
                                    
                                    <td style="font-size:12px;">
                                        <p><?= $goods['secure'] == 0 ? '标准保(免费)' : '意外保' ?></p>                                    
                                        <?php foreach ($goods['service_info'] as $i) : ?>
                                            <p><?= $i['service_name'] ?></p>
                                        <?php endforeach; ?>                                                                          
                                        </td>
                                    <!-- 租赁模式 -->
                                    <td style="width:10%;">
                                        <select class="rent-mode-select" order-goods-id="<?= $goods['order_goods_id'] ?>"
                                        data-am-selected="{btnWidth: '60%', btnSize: 'xs'}" name="goods[<?= $goods['order_goods_id'] ?>][rent_id]">
                                            <?php foreach ($rent_list as $rent) : ?>
                                            <option value="<?= $rent['id'] ?>" <?= $rent['id'] == $goods['rent_mode']['id'] ? 'selected' : '' ?>
                                            data-content="<?= $rent['content'] ?>" class="rent_content" data-rent-unit="<?= $rent['rent_show_unit'] ?>">
                                                <?= $rent['name'] ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td style="width:10%;font-size:12px;">
                                        <?= $goods['rent_mode']['content'] ?>
                                    </td>                                    
                                    <!-- 开始时间 -->                                   
                                    <td style="width:10%;">
                                        <input type="text" style="font-size:12px;" class="am-btn am-btn-default am-round" name="goods[<?= $goods['order_goods_id'] ?>][rent_date]" 
                                        data-am-datepicker value="<?= date('Y-m-d', $goods['rent_date']) ?>">
                                    </td>
                                    <td style="width:10%;">
                                        <div class="rent-num-div am-input-group am-input-group-sm" order-goods-id="<?= $goods['order_goods_id'] ?>">      
                                            
                                            <?php if ($goods['rent_mode']['is_static'] == 0) : ?>
                                                <input type="text" name="goods[<?= $goods['order_goods_id'] ?>][rent_num]" class="am-form-field rent-num-input" onchange="if(value.length>2)value=value.slice(0,2)" style="text-align:center;" value="<?= $goods['rent_num'] ?>">
                                                <span style="color:#777;font-size:12px;"><?= $goods['rent_mode']['rent_show_unit'] ?></span>   
                                            <?php else : ?>
                                            <?php if (!isset(json_decode($goods['rent_mode']['map'], true)[0]['max'])) : ?>
                                                <input type="text" name="goods[<?= $goods['order_goods_id'] ?>][rent_num]" class="am-form-field rent-num-input" onchange="if(value.length>2)value=value.slice(0,2);if(value<6)value=6;" style="text-align:center;" value="<?= $goods['rent_num'] ?>">
                                                <span style="color:#777;font-size:12px;"><?= $goods['rent_mode']['rent_show_unit'] ?></span>
                                            <?php else : ?>
                                            <?php if (json_decode($goods['rent_mode']['map'], true)[0]['min'] == json_decode($goods['rent_mode']['map'], true)[0]['max']) : ?>
                                                <label class="am-radio-inline">
                                                    <input type="radio" name="goods[<?= $goods['order_goods_id'] ?>][rent_num]" value="12" data-am-ucheck <?= $goods['rent_num'] == 12 ? 'checked' : '' ?>> 1年
                                                </label>
                                                <label class="am-radio-inline">
                                                    <input type="radio" name="goods[<?= $goods['order_goods_id'] ?>][rent_num]" value="24" data-am-ucheck <?= $goods['rent_num'] == 24 ? 'checked' : '' ?>> 2年
                                                </label>
                                            <?php else : ?>
                                                <input type="text" name="goods[<?= $goods['order_goods_id'] ?>][rent_num]" class="am-form-field rent-num-input" onchange="if(value.length>2)value=value.slice(0,2);if(value<1)value=1;if(value>5)value=5;" style="text-align:center;" value="<?= $goods['rent_num'] ?>">
                                                <span style="color:#777;font-size:12px;"><?= $goods['rent_mode']['rent_show_unit'] ?></span>
                                            <?php endif;
                                            endif;
                                            endif; ?>
                                            <!-- <input type="text" class="am-form-field rent-num-input" oninput="if(value>99)value=99" style="text-align:center;" value="<?= $goods['rent_num'] ?>">
                                            <span class="am-input-group-label">天</span> -->




                                        </div>
                                        <!-- <?php if ($goods['rent_mode']['is_static'] == 0) : ?><?= $goods['rent_num'] ?>天
                                        <?php else : ?><?= $goods['rent_num'] ?>个月<?php if ($goods['rent_num'] > 3) : ?><p style="color:#777;font-size:12px;">(大于3个月先收3月租金)</p>
                                        <?php endif;
                                        endif; ?> -->
                                    </td>         
                                    <!-- <td>￥<?= $goods['rent_price'] ?></td>                           
                                    <td>￥<?= $goods['rent_total_price'] ?></td> -->
                                    <td>￥<?= $goods['goods_price'] ?></td>
                                    <td>
                                        <div class="rent-num-div am-input-group am-input-group-sm">                                                  
                                            <span style="color:#777;font-size:12px;">x</span> 
                                            <input type="text" name="goods[<?= $goods['order_goods_id'] ?>][total_num]" class="am-form-field rent-num-input" onchange="if(value.length>3)value=value.slice(0,3)" style="text-align:center;" value="<?= $goods['total_num'] ?>">                                              
                                        </div>                                                                                                                                                
                                    </td>
                                    <td>￥<?= $goods['total_price'] ?></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="9" class="am-text-right">总计金额：￥<?= $detail['total_price'] ?></td>
                            </tr>
                            </tbody>                           
                        </table>
                    </div>


                    <?php if ($detail['pay_status']['value'] === 20 && $detail['delivery_status']['value'] === 20) : ?>
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
                                    <th>操作</th>
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
                                        <td>
                                            <div class="tpl-table-black-operation">
                                                <a class="tpl-table-black-operation-green equip-chg"
                                                href="javascript:;">
                                                变更</a>    
                                                <a class="tpl-table-black-operation equip-state-chg"
                                                href="javascript:;" equip-id="<?= $item['equip_id'] ?>">
                                                状态更改</a>                       
                                            </div>             
                                        </td>
                                    </tr>
                                    <tr equip-id="<?= $item['equip_id'] ?>" style="display:none;">
                                        
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
                                        

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">操作</div>
                    </div>
                    <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-5 am-margin-top-lg am-margin-bottom-lg">
                            <button class="j-submit am-btn am-btn-success" style="margin-right:40px;" type="submit">确认修改
                            </button>
                            <a class="am-btn am-btn-primary" id="chg_sta" style="margin-right: 40px;">变更状态
                            </a>
                            <?php if ($detail['pay_status']['value'] == 20 && $detail['delivery_status']['value'] == 10) : ?>
                            <a class="am-btn am-btn-danger" href="<?= url('order/detail', ['order_id' => $detail['order_id']]) ?>" >去发货
                            </a>
                            <?php endif; ?>
                        </div>                                
                    </div>
                </div>
            </div>

        </div>
    </div>
    </form>

    <!--  -->

    <div class="am-modal am-modal-confirm" tabindex="-1" id="my-confirm">
        <div class="am-modal-dialog">
            <div class="am-modal-hd">确认</div>
            <div class="am-modal-bd">
            当前状态只能变更为“已付款”
            </div>
            <div class="am-modal-footer">
            <span class="am-modal-btn" data-am-modal-cancel>取消</span>
            <span class="am-modal-btn" data-am-modal-confirm>确定</span>
            </div>
        </div>
    </div>


    <!-- 状态变更 form -->
    <form id="chgStatus" action="<?= url('order/changeStatus') ?>" method="post">
        <input type="hidden" name="state[order_id]" value="<?= $detail['order_id'] ?>">
        <input type="hidden" name="state[pay_status]" value="<?= $detail['pay_status']['value'] ?>">
        <input type="hidden" name="state[delivery_status]" value="<?= $detail['delivery_status']['value'] ?>">
        <input type="hidden" name="state[receipt_status]" value="<?= $detail['receipt_status']['value'] ?>">
        <input type="hidden" name="state[order_status]" value="<?= $detail['order_status']['value'] ?>">
        <input type="hidden" name="state[chg_data]" id="chg_data" value="">
        <button type="submit" id="state_submit" style="display:none;" ></button>
    </form>

       

</div>
<script>
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm({
            // form data
            buildData: function () {
                
            },
            // 自定义验证
            validation: function () {                
                return true;
            }
        });
                


        $('#chgStatus').superForm({
            // 自定义验证
            validation: function () {                
                return true;
            }
        })









        // 悬浮
        $(document).on('mouseenter', '.rent_content', function(){            
            var content = $(this).attr('data-content');               
            tip_index = layer.tips(content, $(this), {time: 0});
        }).on('mouseleave', '.rent_content', function(){
            layer.close(tip_index);
        });
        

        // 更改价格
        $('#pay_price').on('change',function(e){
            var value = $(this).val();            
            value = new Number(value);
            value = value.toFixed(2);            
            $(this).val(value);
        })


        // 租赁模式选择
        $('.rent-mode-select').on('change',function(){
            // console.log($(this).children('option:selected'));
            var obj = $(this).children('option:selected');
            var unit = obj.attr('data-rent-unit');
            var order_goods_id = $(this).attr('order-goods-id');
            var rent_id = obj.val();
            var rent_content = obj.attr('data-content');
            $(this).parent().next().html(rent_content);
            var rent_num_obj = $('div[order-goods-id="'+order_goods_id+'"]');   
            $.get("<?= url('order/getRentHtml') ?>",{rent_id:rent_id,order_goods_id:order_goods_id},function(res){
                rent_num_obj.html(res);
            })                                   
        });


        // 更改已发货设备
        $('.equip-chg').on('click',function(){                  
            var thisTr = $(this).parent().parent().parent();
            var equip_pid = thisTr.children(0).html();
            var child = $('tr[equip-id="'+equip_pid+'"]');
            console.log(equip_pid);
            
            layer.prompt({title: '填写新的设备ID', formType: 3}, function(text, index){
                layer.close(index);
                $.get('<?= url("equip/getOne") ?>',{equip_id:text},function(res){
                    if(res.code==1){                            
                        var data = res.data;
                        data.secure_text = data.secure==0?'标准保':'意外保';                            
                        var html = "";
                        // 
                        var service = '<td><div class="am-form-group am-service"><label class="am-checkbox-inline">';
                        service += '<input type="radio" name="equip['+data['equip_id']+'][secure]"  value="0" data-am-ucheck checked>标准保</label><label class="am-checkbox-inline">';
                        service += '<input type="radio" name="equip['+data['equip_id']+'][secure]" value="1" data-am-ucheck>意外保</label></div></td>';
                        service += '<td><div class="am-form-group am-service">'
                        var service_data = data['goods']['service'];
                        for(var j in service_data){                                
                            service += '<label class="am-checkbox-inline am-secondary">';
                            service += '<input type="checkbox" name="equip['+data['equip_id']+'][service][]" value="'+service_data[j]['service_id']+'" data-am-ucheck>'+service_data[j]['service_name']+'</label>'
                        }                            
                        service += '</div></td>';
                        // 
                        html += "<td data-type='equip_id'><input type='hidden' name='equip[]' value='"+data['equip_id']+"' disabled>"+"┗"+data['equip_id']+"</td>";
                        html += "<td>"+data['goods_name']+"</td>";
                        html += "<td>"+data['spec_value']['spec_value']+"</td>";
                        html += service;
                        html += "<td><input type='hidden' name='equip["+data['equip_id']+"][p_equip_id]' value='"+equip_pid+"'>在库</td>";
                        html += '<td><div class="tpl-table-black-operation"><a class="tpl-table-black-operation-del" href="javascript:;" onclick="delEquip(this)">删除</a></div></td>';
                        // 
                        
                        child.html(html);
                        child.css('display','table-row');                     
                        $('input[type="checkbox"],input[type="radio"]').uCheck();   
                        
                    }else{
                        layer.msg(res.msg);
                    }
                });
            });                    
        })




        // 更改设备状态
        $('.equip-state-chg').on('click',function(){
            var equip_id = $(this).attr('equip-id');
            layer.confirm('请选择设备状态', {
                btn: ['运送中','使用中','维修中','停用'] //按钮
                ,              
                btn1:function(){
                    var state = 20;
                    changeEquipState(equip_id,state);
                },
                btn2:function(){
                    var state = 30;
                    changeEquipState(equip_id,state);
                },
                btn3:function(){
                    var state = 40;
                    changeEquipState(equip_id,state);
                },
                btn4:function(){
                    var state = 50;
                    changeEquipState(equip_id,state);
                }
            });
        })





        // 更改订单状态
        $('#chg_sta').on('click',function(){
            if('<?= $detail['pay_status']['value'] ?>'==10){
                $('#my-confirm').modal({
                    relatedTarget: this,
                    onConfirm: function(options) {
                        $('#state_submit').click();                       
                    },
                    // closeOnConfirm: false,
                    onCancel: function() {
                    
                    }
                });
            }else{
                if('<?= $detail['delivery_status']['value'] ?>'==10){
                    layer.msg('未发货时，不能更改状态');
                }else{
                    // 未付款，未发货，已发货（已送达，设备都变为使用中）
                    layer.confirm('请选择订单状态', {
                        btn: ['取消','未付款','未发货','已送达'] //按钮
                        ,
                        btn1:function(){
                           layer.closeAll();
                        }, 
                        btn2:function(){
                            layer.confirm('将清空所有数据,回到未支付状态', {
                                btn: ['确定','取消'] //按钮
                                }, function(){
                                    // 
                                    $('#chg_data').val('pay');
                                    $('#state_submit').click(); 
                                }, function(){
                                
                            });
                        },
                        btn3:function(){
                            layer.confirm('将还原该订单所有设备状态，回到未发货状态', {
                                btn: ['确定','取消'] //按钮
                                }, function(){
                                    // 
                                    $('#chg_data').val('delivery');
                                    $('#state_submit').click();                                     
                                }, function(){
                                
                            });
                        },
                        btn4:function(){
                            layer.confirm('所有设备均将使用中状态，订单变更为已送达', {
                                btn: ['确定','取消'] //按钮
                                }, function(){
                                    // 
                                    $('#chg_data').val('recieve');
                                    $('#state_submit').click();                                                                         
                                }, function(){
                                
                            });
                        }
                    });
                }
            }
        })
        


        // 
    });

    

    function delEquip(e){
        $(e).parent().parent().parent().css('display','none');
        $(e).parent().parent().parent().html("");
    }

    function changeEquipState(equip_id,state)
    {
        $.post('<?= url("order/changeEquipState") ?>',{
            equip_id:equip_id,
            state:state
        },function(res){            
            window.location.reload();
        })
    }


</script>
