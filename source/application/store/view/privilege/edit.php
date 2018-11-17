<link rel="stylesheet" href="assets/store/css/goods.css">
<link rel="stylesheet" href="assets/store/plugins/umeditor/themes/default/css/umeditor.css">
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
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">权限名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="privilege[privilege_name]"
                                           value="<?= $model['privilege_name'] ?>" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">接口菜单集合 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <?php foreach($menu as $key => $item): ?>
                                    <div class="am-form-group">
                                        <h3><?= $item['name'] ?></h3>
                                        <?php if(!isset($item['submenu'])):  ?>
                                        <label class="am-checkbox-inline">                                            
                                            <input type="checkbox"  value="<?= $item['index'] ?>" <?= $item['checked']==true?'checked':'' ?> name="privilege[api_menu_id][]" data-am-ucheck><?= $item['name'] ?>
                                        </label>
                                        <?php else : foreach($item['submenu'] as $sub):?>
                                        <?php if(isset($sub['index'])): ?>
                                        <label class="am-checkbox-inline">
                                            <input type="checkbox" value="<?= $sub['index'] ?>" <?= $sub['checked']==true?'checked':'' ?> name="privilege[api_menu_id][]" data-am-ucheck><?= $sub['name'] ?>
                                        </label>
                                        <?php else: ?>
                                        <?php foreach($sub['submenu'] as $c):?>
                                            <label class="am-checkbox-inline">
                                                <input type="checkbox" value="<?= $c['index'] ?>" <?= $c['checked']==true?'checked':'' ?> name="privilege[api_menu_id][]" data-am-ucheck><?= $c['name'] ?>
                                            </label>
                                        <?php endforeach;?>
                                        <?php endif;?>
                                        <?php endforeach; endif; ?>                                                                          
                                    </div>
                                    <?php endforeach; ?>                                                                  
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">角色排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="privilege[sort]"
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

    });
</script>
