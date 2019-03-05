<style>

.layui-form{
    margin:0 auto !important;
         
}

.layui-table-body{
    overflow-x:hidden !important;
}

</style>
<link rel="stylesheet" href="assets/layui/css/layui.css"  media="all">
                
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
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">标题 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="notice[title]"
                                           value="" required>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">内容 </label>
                                <div class="am-u-sm-9 am-u-end">                                    
                                    <textarea name="notice[content]" id="" cols="30" rows="10" required></textarea>
                                </div>
                            </div>                            

                            <input type="hidden" name="notice[member_ids]" id="member_ids">
                            
                            <div class="am-form-group" style="display:none;">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" id="sub"  class="j-submit am-btn am-btn-secondary">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
                <div class="widget-body  am-margin-bottom-lg">
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">人员选择</div>
                    </div>                                                                                                                
                    <div class="am-form-group">                            
                        <table class="layui-table" lay-data="{width: 540, height:'full', url:'<?= url('notice/getMemberAjax') ?>', page:true, id:'idTest'}" lay-filter="demo">
                            <thead>
                                <tr>
                                <th lay-data="{type:'checkbox', width:60 }"></th>
                                <th lay-data="{field:'id', width:80, sort: true}">ID</th>
                                <th lay-data="{field:'name', width:160}">用户名</th>
                                <th lay-data="{field:'phone', width:160, sort: true}">手机号</th>
                                <th lay-data="{field:'role_name', width:80}">角色</th>                                                                                                     
                                </tr>
                            </thead>
                        </table>                    
                    </div>
                </div>

                <div class="am-form-group">
                    <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                        <button type="submit" class="j-submit am-btn am-btn-secondary" id="_sub">提交
                        </button>
                    </div>
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

        $('#member_ids').attr('value','');
        $('#member_ids').attr('value',member_ids);

        console.log($('#member_ids'));
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
            // form data
            buildData: function () {
                
            },
            // 自定义验证
            validation: function () {
                return true;
            }
        });

    });


    $('#_sub').on('click',function(){
        $('#sub').click();
    })
</script>
