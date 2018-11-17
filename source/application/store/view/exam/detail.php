<style>
.am-btn{
    font-size : 1.4rem;    
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
                                                <input type="text" class="tpl-form-input"
                                                    value="<?= $v ?>" disabled="disabled">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php elseif ($key == "image") : ?>
                                    <?php foreach ($item as $k => $v) : ?>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-u-lg-2 am-form-label"> <?= $map[$k] ?> :</label>
                                            <div class="am-u-sm-9 am-u-end">                                                
                                                <a href="<?= $v ?>" title="点击查看大图" target="_blank">
                                                    <img src="<?= $v ?>" width="72" height="72" alt="">
                                                </a>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </form>          

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
                            </div>
                        </fieldset>
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
        $('.j-submit').on('click',function(){
            
            var type = $(this).attr('data-type');
            var value = 10;
            switch (type) {
                case 'pass':
                    value = 20;
                    break;
            
                case 'failed':
                    value = 30;
                    break;
            }


            $.post("<?= url('exam/examine') ?>",{
                id:$('#id').val(),
                status:value
            },function(res){
                console.log(res);
                layer.msg(res.msg, {time: 1500, anim: 1}, function () {
                    var url = "<?= url('exam/list') ?>";
                    window.location.href = url;
                });
            })

            return false;
        })


        
               
       
    });
    
</script>
