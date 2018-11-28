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
                            <input type="hidden" name="member[id]" value="<?= $model['id'] ?>">
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">员工姓名 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="member[name]"
                                           value="<?=$model['name']?>" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">员工电话 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="member[phone]"
                                           value="<?=$model['phone']?>" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">职称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="member[function]"
                                           value="<?=$model['function'] ?>" required>
                                </div>
                            </div>
                           
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">角色选择 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="member[role_id]" required
                                            data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择角色'}">
                                        <option value=""></option>
                                        <?php if (isset($role_list)): foreach ($role_list as $first): ?>
		<option value="<?=$first['id']?>" <?php if ($model['role_id'] == $first['id']): ?> selected <?php endif;?>   ><?=$first['role_name']?></option>
                                        <?php endforeach;endif;?>
                                    </select>
                                    <small class="am-margin-left-xs">
                                        <a href="<?=url('role/add')?>">去添加</a>
                                    </small>
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
