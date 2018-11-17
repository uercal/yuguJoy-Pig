<style>


.am-btn{
    font-size : 1.4rem;    
}

.demonstration{
    font-size : 1.4rem;
}

</style>
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<div class="row-content am-cf" id="app">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">                
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">设备ID</div>
                            </div>
                            <div class="am-form-group am-scrollable-horizontal">
                                <div class="am-u-sm-9 am-u-sm-push-12 am-margin-top-lg">
                                    <el-input size="small" v-model="equip_id" placeholder="请输入设备ID" style="width:30%;"></el-input>
                                    <button type="default" class="j-submit am-btn am-btn-sm am-btn-success" @click="checkEquip">
                                        查询确认
                                    </button>
                                    <p>
                                        查询结果：
                                    </p>
                                    <table v-if="equip_info" v-show="equip_info" class="regional-table am-table am-table-bordered am-table-centered
                                am-text-nowrap am-margin-bottom-xs" style="display:none;width:50rem;">
                                        <tbody>
                                        <tr>                                            
                                            <th>订单ID</th>
                                            <th>订单单号</th>       
                                            <th>设备ID</th>                                                                               
                                            <th>设备所属产品</th>
                                            <th>设备型号</th>
                                            <th>设备订单状态</th>  
                                        </tr>                                                                       
                                        <tr v-if="equip_info.order">
                                            <td v-html="equip_info.order.order_id"></td>
                                            <td v-html="equip_info.order.order_no"></td>
                                            <td v-html="equip_info.equip_id"></td>
                                            <td v-html="equip_info.goods_name"></td>                                            
                                            <td v-html="equip_info.spec_value.spec_value"></td>                                            
                                            <td v-html="equip_info.status_text"></td>                                            
                                        </tr>                     
                                        <tr v-else>
                                            <td></td>
                                            <td></td>
                                            <td v-html="equip_info.equip_id"></td>
                                            <td v-html="equip_info.goods_name"></td>                                            
                                            <td v-html="equip_info.spec_value.spec_value"></td>                                            
                                            <td v-html="equip_info.status_text"></td>                                            
                                        </tr>                                                                                             
                                        </tbody>
                                    </table> 
                                </div>
                            </div>                             



                             <div class="widget-head am-cf">
                                <div class="widget-title am-fl">维修用户</div>
                            </div>
                            <div class="am-form-group am-scrollable-horizontal">
                                <div class="am-u-sm-9 am-u-sm-push-12 am-margin-top-lg">
                                    <el-input size="small" v-model="member_phone" placeholder="用户手机号码" style="width:30%;"></el-input>
                                    <button type="default" class="j-submit am-btn am-btn-sm am-btn-success" @click="checkMember">
                                        查询确认
                                    </button>
                                    <p v-if="member_info" v-show="member_info" style="display:none;">
                                        查询结果：<a href="javascript:;">{{member_info.name}}</a> <br>
                                        角色：<a href="javascript:;">{{member_info.role[0].role_name}}</a>
                                    </p>                                    
                                </div>
                            </div>  


                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">其他</div>
                            </div>                                                                                    
                            <div class="am-form-group">                                
                                <div class="am-u-sm-9 am-u-end">                                    
                                    <span class="demonstration">维修状态</span>
                                    <el-select v-model="post.check_status" placeholder="请选择">
                                        <el-option
                                        v-for="item in status"
                                        :key="item.value"
                                        :label="item.label"
                                        :value="item.value">
                                        </el-option>
                                    </el-select>
                                </div>
                                <div class="am-u-sm-9 am-u-end" style="margin-top:2rem;">     
                                    <span class="demonstration">维修时间</span>
                                    <el-date-picker
                                    v-model="post.check_time"
                                    type="datetime"
                                    placeholder="选择日期时间">
                                    </el-date-picker>
                                </div>
                            </div>
                            
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="default" class="j-submit am-btn am-btn-secondary" @click="doPost">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/vue/vue.min.js"></script>
<script src="https://cdn.bootcss.com/vue-resource/1.5.1/vue-resource.min.js"></script>
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>
    var vue = new Vue({
        el:'#app',
        data:{
            equip_id:'',
            member_phone:'',
            check_status:'',
            check_time:'',
            equip_info:null,
            member_info:null,
            status: [{
                value: '10',
                label: '修复中'
            },{
                value: '20',
                label: '已修复'
            },{
                value: '30',
                label: '停用'
            }],
            post:{
                equip_id:'',
                check_member_id:'',
                check_status:'',
                check_time:'',
                order_id:'',
            }
        },
        methods:{
            checkEquip:function(){
                this.$http.post('<?= url("equip/checkAjax") ?>',{
                    type:'equip',
                    equip_id:this.equip_id
                }).then(function(res){
                    if(res.data){
                        this.equip_info = res.data;
                        this.post.order_id = res.data.order.order_id;
                        this.post.equip_id = res.data.equip_id;
                    }else{
                        this.equip_info = null;
                        layer.msg('设备不存在或该设备在库状态');
                    }                    
                })
            },
            checkMember:function(){
                this.$http.post('<?= url("equip/checkAjax") ?>',{
                    type:'member',
                    member_phone:this.member_phone
                }).then(function(res){
                    console.log(res.data);
                    if(res.data){
                        this.member_info = res.data;
                        this.post.check_member_id = res.data.id;
                    }else{
                        this.member_info = null;
                        layer.msg('该手机号码的员工不存在!');
                    } 
                })
            },
            doPost:function(){
                
                if(this.equip_info==null){
                    layer.msg('请查询确认设备');
                    return false;
                }
                if(this.member_info==null){
                    layer.msg('请查询确认维修用户');
                    return false;
                }
                if(this.post.check_status==''){
                    layer.msg('请选择维修状态');
                    return false;
                }
                if(this.post.check_time==''){
                    layer.msg('请选择维修时间');
                    return false;
                }
                
                this.$http.post('<?= url("equip/checkAdd") ?>',{
                    post:this.post
                }).then(function(res){
                    layer.msg(res.data.msg);
                    if(res.data.code==1){
                        setTimeout(() => {
                            window.location.href = "<?= url('equip/checkLog') ?>";
                        }, 1000);
                    }
                })
            }
        }
    })
</script>
