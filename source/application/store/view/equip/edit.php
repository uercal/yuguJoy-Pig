
<style>


.am-btn{
    font-size : 1.4rem;    
}



</style>

<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">基本信息</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">设备ID </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           value="<?= $model['equip_id'] ?>" disabled="disabled">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">设备SN码 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           value="<?= $model['sn_code'] ?>" name="equip[sn_code]">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属产品 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="equip[goods_id]" required id="goods_id"
                                            data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择产品'}">
                                        <option value=""></option>
                                        <?php if (isset($goods)) : foreach ($goods as $first) : ?>
<option value="<?= $first['goods_id'] ?>" <?php if ($first['goods_id'] == $model['goods_id']) : ?> selected <?php endif; ?> ><?= $first['goods_name'] ?></option>                                            
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                    <small class="am-margin-left-xs">
                                        <a href="<?= url('goods/add') ?>">去添加</a>
                                    </small>
                                </div>
                            </div>      
                            

                            <div id="sku">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属型号 </label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <select name="equip[spec_value_id]" required
                                                data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择型号'}">
                                            <option value=""></option>
                                            <?php if (isset($spec_list[0])) : foreach ($spec_list[0]['spec_items'] as $first) : ?>                                                
    <option value="<?= $first['spec_value_id'] ?>" <?php if ($first['spec_value_id'] == $model['spec_value_id']) : ?> selected <?php endif; ?> ><?= $first['spec_value'] ?></option>                                            
                                            <?php endforeach;
                                            endif; ?>
                                        </select>                                      
                                    </div>
                                </div> 
                            </div> 
                            
                            


                                                       
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">其他</div>
                            </div>                            
                            
                            <!-- 状态   在库 -->
                            
                            <!--  -->
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设备排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="equip[equip_sort]"
                                           value="<?= $model['sort'] ?>" required>
                                    <small>数字越小越靠前</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
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
        

        $('#my-form').superForm({
            // form data
            buildData: function () {
                
            },
            // 自定义验证
            validation: function () {                            
                return true;       
            }
        });


                
    

        $('#goods_id').on('change',function(){            
            var goods_id = $(this).find('option').eq(this.selectedIndex).val();
            $.get("<?= url('equip/getSku') ?>&goods_id="+goods_id,function(res){
                console.log(res);
                var html = "";
                var parent = "";
                var items = res[0].spec_items;
                var group_name = res[0].group_name;                                
                $('#sku').html("");

                parent += '<div class="am-form-group">';
                parent += '<label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">'+ group_name +'</label>';
                parent += '<div class="am-u-sm-9 am-u-end">';
                parent += '<select name="equip[spec_value_id]" required id="spec_value_id"';
                parent += 'data-am-selected="{searchBox: 1, btnSize: "sm",  placeholder:"情选择'+ group_name +'"}">';                                        
                
                for (let index = 0; index < items.length; index++) {  
                    html += '<option value=""></option>';
                    html += '<option value="'+items[index].spec_value_id;
                    html += '">'+items[index].spec_value;
                    html += '</option>';                        
                }

                parent += html;
                parent += "</select></div></div>";
                        
                                            
                $('#sku').html(parent);
                $('select').selected();                                 

            });
            
        })
        
    });
    
</script>
