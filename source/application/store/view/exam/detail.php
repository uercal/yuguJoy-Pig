<style>
    .am-btn {
        font-size: 1.4rem;
    }
</style>

<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body">
                    <fieldset>
                        <form id="my-form" class="am-form tpl-form-line-form" method="post">

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">审核信息</div>
                            </div>
                            <?php foreach ($data as $key => $item) : ?>
                            <?php if ($key == 'input') : ?>
                            <?php foreach ($item as $k => $v) : ?>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label"> <?= $map[$k] ?> :</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="<?= $k ?>" value="<?= $v ?>" disabled="disabled">
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php elseif ($key == "image") : ?>
                            <?php foreach ($item as $k => $v) : ?>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label"> <?= $map[$k] ?> :</label>
                                <?php if ($k == 'order_id') : ?>
                                <div class="tpl-table-black-operation" style="padding-top: .8rem;">
                                    <a href="<?= url('order/detail', ['order_id' => $v]) ?>" style="margin-left: 10px;" class="tpl-table-black-operation">点击跳转</a>
                                </div>

                                <?php else : ?>
                                <div class="am-u-sm-9 am-u-end">
                                    <?php if (is_array($v)) : foreach ($v as $c) : ?>
                                    <a href="<?= $c ?>" title="点击查看大图" target="_blank" style="margin-right:10px;">
                                        <img name="<?= $k ?>" src="<?= $c ?>" width="72" height="72" alt="">
                                    </a>
                                    <?php endforeach;
                            else : ?>
                                    <a href="<?= $v ?>" title="点击查看大图" target="_blank">
                                        <img name="<?= $k ?>" src="<?= $v ?>" width="72" height="72" alt="">
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php endforeach; ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ($type == 10) : ?>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">发放额度</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" style="background-color:#fff;" value="<?= $status == 10 ? '' : $info['quota']['quota_money'] ?>" <?= $status == 10 ? '' : 'disabled="disabled"' ?> id="quota_money">
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($type == 30 && $status == 10) : ?>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">该用户可用余额</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" style="background-color:#fff;" value="<?= $info['user']['account_money']['actual_money'] ?>" disabled="disabled" ?>
                                </div>
                            </div>
                            <?php endif; ?>

                        </form>

                        <?php if ($status == 10) : ?>
                        <div class="widget-head am-cf">
                            <div class="widget-title am-fl">审核</div>
                        </div>
                        <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-1 am-margin-top-lg">
                                <button class="j-submit am-btn am-btn-success" style="margin-right:40px;" data-type="pass">通过
                                </button>
                                <button class="j-submit am-btn am-btn-danger" data-type="failed">驳回
                                </button>
                            </div>
                            <input type="hidden" id="id" value="<?= $id ?>">
                            <input type="hidden" id="content" value='<?= $content ?>'>
                        </div>
                        <?php endif; ?>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('.j-submit').on('click', function() {
            var post_data = [];
            $('input').map(function() {
                var name = $(this).attr('name');
                var value = $(this).val();
                if (!name || value == "") {
                    return false;
                }
                var data = {
                    'name': name,
                    'value': value
                };
                post_data.push(data);
            })
            $('img').map(function() {
                console.log($(this));
            })
            console.log(post_data);
            var type = $(this).attr('data-type');
            var status = 10;
            switch (type) {
                case 'pass':
                    status = 20;
                    break;

                case 'failed':
                    status = 30;
                    break;
            }

            var quota_money = $('#quota_money').val();

            if (status == 20 && quota_money == '') {
                quota_money = 0;
            }

            var content = JSON.parse($('#content').val());

            $.post("<?= url('exam/examine') ?>", {
                id: $('#id').val(),
                status: status,
                quota_money: quota_money,
                content: content
            }, function(res) {
                console.log(res);
                layer.msg(res.msg, {
                    time: 1500,
                    anim: 1
                }, function() {
                    var url = "<?= url('exam/index') ?>&type=<?= $type ?>";
                    window.location.href = url;
                });
            })

            return false;
        })





    });
</script> 