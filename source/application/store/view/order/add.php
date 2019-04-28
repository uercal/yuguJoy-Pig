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

.am-border{
    border:1px solid #ccc;
}
.am-btn{
    font-size:1.2rem;
}

.el-input-number__decrease,.el-input-number__increase{
    border-top:1px solid #dcdfe6;
}

.rent_num{
    display:flex;
    justify-content:center;
    width:10rem;
    align-items: center;
}

</style>
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<div class="row-content am-cf" id="app">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-body  am-margin-bottom-lg">                    
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">基本信息</div>
                    </div>                    
                    <div class="widget-head am-cf" style="position:relative;">
                        <div class="widget-title am-fl">商品信息(大于3月先收3月租金)</div>
                        <div style="right:0;position:absolute;">                                                                                                                                                                                
                            <button class="am-btn am-btn-default am-btn-success am-radius" @click="add">添加商品</button>                            
                        </div>
                    </div>    
                    <table class="regional-table am-table am-table-bordered am-table-centered
                        am-text-nowrap am-margin-bottom-xs">
                        <tbody>
                        <tr>
                            <th>产品</th>
                            <th>型号/类型</th>
                            <th>租赁模式</th>                                
                            <th>起租时间</th>
                            <th>起租时长</th>
                            <th>增值服务</th>                        
                            <th>押金</th>
                            <th>购买数量</th>                            
                            <th>操作</th>
                        </tr>                                                                       
                        <tr v-for="(item,index) in data" v-show="data" style="display:none;">
                            <td>                                           
                                <el-select size="small" @change="goods($event,item)" :value="item.goods_choose">                                                                      
                                    <el-option-group :label="c.name" v-for="(c,i) in item.goods_list">                                                                                                                     
                                        <el-option v-for="d in c.goods" :value="[i,d.goods_id]"  :label="d.goods_name"></el-option>                                                                                    
                                    </el-option-group>
                                </el-select>        
                              
                            </td>                                                                                                                                      
                            <td>                            
                                <el-select size="small" @change="spec_value($event,item)" :value="item.spec_choose">
                                    <el-option v-for="(c,i) in item.spec_list" :value="i" :label="c.spec_value"></el-option>
                                </el-select>                            
                            </td>                                          
                            <td style="width:10%;">
                                <el-select size="small" @change="rent_id($event,item)" v-model="item.rent_id">                                    
                                    <el-option :value="r.id" v-for="(r,i) in item.rent_list" :label="r.name" :key="i">
                                    </el-option>
                                </el-select>
                            </td>                               
                            <td>
                                <el-date-picker style="width:12rem;" size="small" v-model="item.rent_date" type="date" placeholder="选择日期">
                                </el-date-picker>
                            </td>
                            <td>
                                <div v-if="item.rent_obj">     
                                    <!-- 日租 -->
                                    <div v-if="item.rent_obj.is_static==0">
                                        <el-input-number size="mini" v-model="item.rent_num" :min="1" :max="999" label="">
                                        </el-input-number>
                                        <small>天</small>                                                                             
                                    </div>
                                    <div v-if="item.rent_obj.is_static==1&&item.rent_obj.map[0].min==1">
                                        <el-input-number size="mini" v-model="item.rent_num" :min="1" :max="5" label="">
                                        </el-input-number>
                                        <small>月</small>                                                                             
                                    </div>
                                    <div v-if="item.rent_obj.is_static==1&&item.rent_obj.map[0].min==6">
                                        <el-input-number size="mini" v-model="item.rent_num" :min="6" :max="99" label="">
                                        </el-input-number>                                        
                                        <small>月</small>                                                                             
                                    </div>
                                    <div v-if="item.rent_obj.is_static==1&&item.rent_obj.map[0].min==12">
                                        <el-radio-group size="small" v-model="item.rent_num">
                                            <el-radio-button label="12">1年</el-radio-button>
                                            <el-radio-button label="24">2年</el-radio-button>                                            
                                        </el-radio-group>
                                    </div>
                                </div>                            
                            </td>
                            <td>
                                <el-radio-group v-model="item.secure" style="display:flex;">
                                    <el-radio :label="0">标准保</el-radio>
                                    <el-radio :label="1">意外保</el-radio>                                    
                                </el-radio-group>
                                <br>          
                                <div style="display:flex;">
                                    <el-checkbox v-for="i in item.service_list" :label="i.service_name" :checked="service(i,item.service)" @change="chg_service(i,item.service)"></el-checkbox>
                                </div>
                                
                            </td>                              
                            <td id="goods_price" v-html="item.goods_price"></td>
                            <td>                                                                                                   
                                <el-input-number size="small" v-model="item.total_num" @change="changeTotalNum" :min="1" :max="99" label="购买数量"></el-input-number>                             
                            </td>                           
                            <td>
                                <button class="am-btn am-btn-default am-btn-danger am-radius" @click="del(index)">删除</button>
                            </td>
                        </tr>  
                        
                        <tr>
                            <td colspan="9" class="am-text-right" >
                                <div class="rent-num-div am-input-group am-input-group-sm" style="display:flex;justify-content:flex-end;"> 
                                    <span style="color:#777;font-size:12px;">实付金额：￥</span>   
                                    <el-input size="small" type="number" v-model="pay_price" placeholder="实付金额" style="width:10rem;"></el-input>
                                </div>                          
                            </td>                                                                    
                        </tr>                            
                        </tbody>
                    </table>                        

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">收货信息</div>
                    </div>
                    <div>
                        <table class="regional-table am-table am-table-bordered am-table-centered
                            am-text-nowrap am-margin-bottom-xs">
                            <tbody>
                            <tr>
                                <th>收货人</th>
                                <th>收货电话</th>
                                <th>省市区</th>
                                <th>详细地址</th>
                            </tr>
                            <tr>


                                <td>
                                    <el-input v-model="address.name" placeholder="请输入收货人"></el-input>
                                </td>
                                <td>
                                    <el-input type="number" v-model="address.phone" placeholder="请输入收货电话" @change="phoneNum"></el-input>
                                </td>
                                <td>
                                <el-cascader
                                    expand-trigger="hover"
                                    :options="region_data"
                                    v-model="region_option"
                                    @change="handleChange">
                                </el-cascader>
                                </td>
                                <td>                                    
                                    <el-input v-model="address.detail" placeholder="请输入详细地址"></el-input>
                                </td>


                                
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">归属用户ID</div>
                    </div>
                    <div class="am-form-group am-scrollable-horizontal">
                        <div class="am-u-sm-9 am-u-sm-push-12 am-margin-top-lg">
                            <el-input size="small" v-model="user_id" placeholder="请输入用户ID" style="width:30%;"></el-input>
                            <button type="default" class="j-submit am-btn am-btn-sm am-btn-success" @click="checkUser">
                                查询
                            </button>
                            <p>
                                查询结果：
                            </p>
                            <table v-show="user_detail.user_id" class="regional-table am-table am-table-bordered am-table-centered
                        am-text-nowrap am-margin-bottom-xs" style="display:none;width:50rem;">
                                <tbody>
                                <tr>
                                    <th>用户ID</th>
                                    <th>微信头像</th>
                                    <th>微信昵称</th>                                
                                    <th>手机号码</th>                                    
                                </tr>                                                                       
                                <tr>
                                    <td v-html="user_detail.user_id"></td>
                                    <td>
                                        <a :href="user_detail.avatarUrl" title="点击查看大图" target="_blank">
                                            <img :src="user_detail.avatarUrl" width="72" height="72" alt="">
                                        </a>
                                    </td>
                                    <td v-html="user_detail.nickName"></td>
                                    <td v-html="user_detail.phone"></td>
                                </tr>                                                                                             
                                </tbody>
                            </table> 
                        </div>
                    </div>     
                  
                    <div class="widget-head am-cf">
                        <div class="widget-title am-fl">操作</div>
                    </div>                                                                                                     
                    <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                            <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary" @click="doPost">
                                确认
                            </button>
                        </div>
                    </div>                                                  
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/vue/vue.min.js"></script>
<script src="https://cdn.bootcss.com/vue-resource/1.5.1/vue-resource.min.js"></script>
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>

Vue.http.get('<?= url("order/addAjax") ?>').then(function(res){
    console.log(res);
    var goods_list =res.data.goods_list;
    var rent_list = res.data.rent_list;
    rent_list.map(function(e){
        e.map = $.parseJSON(e.map);
    });    
    var region_data = res.data.regionData;
    var vue = new Vue({
        el:'#app',
        data:{
            data:[                
            ],   
            region_data:region_data,
            region_option:'',
            address:{
                'name':'',
                'phone':'',
                'detail':''
            },
            user_id:'',
            order_total_price:0,
            pay_price:0,
            user_detail:{
                user_id:null,
                avatarUrl:null,
                nickName:null,
                phone:null
            }
        },       
        methods:{
            add:function(){             
                var obj = {                    
                    'goods_list':goods_list,
                    'rent_list':rent_list,
                    'rent_date':'',
                    'goods_choose':'',
                    'goods_price':0,
                    'spec_choose':'',
                    'spec_list':[],
                    'service_list':[],
                    'service':[],
                    'rent_num':1,
                    'secure':0,
                    'total_num':0
                };
                this.data.push(obj);   
                console.log(this.data);
            },
            goods:function(event,item){ 
                var index = event[0];
                var goods_id = event[1];
                var obj = item.goods_list[index].goods.find(function(e){
                   return e.goods_id == goods_id;
                })                
                item.goods_id = obj.goods_id;
                item.goods_choose = obj.goods_name;               
                item.spec = obj.spec;    
                item.spec_list = obj.spec_rel;    
                item.spec_value_id = '',item.spec_choose = '';
                // 
                item.service_list = obj.service;
                console.log(this.data);
            },
            spec_value:function(i,item){
                item.spec_value_id = item.spec_list[i].spec_value_id;
                item.spec_choose = item.spec_list[i].spec_value;     
                var spec_obj = item.spec.find(function(e){
                    return e.spec_sku_id == item.spec_value_id;
                })          
                item.spec_obj = spec_obj;               
                item.goods_price = spec_obj.goods_price;
                item.secure_price = spec_obj.secure_price;
                console.log(this.data);
            },
            rent_id:function(e,i){
                i.rent_id = e;                
                var rent_obj = i.rent_list.find(function(el){
                    return el.id == e;
                })
                i.rent_obj = rent_obj;    
                i.rent_num = '';
                // console.log(i);
            },            
            service:function(i,service){
                var index = service.indexOf(i);
                if(index==-1){
                    return false;
                }else{
                    return true;
                }                
            },
            chg_service:function(i,service){
                var index = service.indexOf(i.service_id);
                if(index==-1){
                    service.push(i.service_id);
                }else{
                    service.splice(index,1);
                }
                console.log(service);
            },
            phoneNum:function(value){                
                if(!(/^1[34578]\d{9}$/.test(value))){ 
                    layer.msg('输入手机号码有误,请重新输入') 
                    return false; 
                } 
            },
            del:function(index){                
                this.data.splice(index,1);
            },
            checkUser:function(){                
                this.$http.post('<?= url("order/checkUser") ?>',{
                    user_id:this.user_id
                }).then(function(res){
                    if(res.data.data.code==0){
                        layer.msg(res.data.data.msg);
                    }else{
                        this.user_detail = res.data.data.detail;
                    }                    
                });
            },
            doPost:function(){
                var check = this.isCheck();
                if(!check[0]){
                    if(check[1]!='') layer.msg(check[1]+"不能为空!");                    
                } else{
                    var order = {
                        region_option:this.region_option,
                        address:this.address,
                        user_id:this.user_id,
                        pay_price:this.pay_price,              
                    };                
                    this.$http.post('<?= url("order/add") ?>',{
                        data:this.data,
                        order:order
                    }).then(function(res){                    
                        // console.log(res.data);
                        layer.msg(res.data.msg)
                        if(res.data.data.code==1){
                            window.location.href = "<?= url('order/detail') ?>"+"&order_id="+res.data.data.order_id;
                        }
                    });
                }
                
            },
            isCheck:function(){
                // 商品信息
                var data = this.data;
                if(data.length==0) return [false,'商品信息'];
                for(var j in data){
                    for(var i in data[j]){                                       
                        if(data[j][i]===''){
                            console.log(i);
                            s = [false,'商品信息'];
                            return s;
                        }                        
                    }
                    if(!data[j].rent_obj){                            
                        s = [false,'租赁模式'];
                        return s;
                    }
                }                                
                // 订单信息 + 收货信息
                if(this.pay_price==0) return [false,'付款金额'];
                if(this.region_option==='') return [false,'地区'];
                var address = this.address;
                for(var i in address){
                    if(address[i]==''){
                        return [false,'收货信息'];
                    }                                   
                }

                // 
                if(!(/^1([38][0-9]|4[579]|5[0-3,5-9]|6[6]|7[0135678]|9[89])\d{8}$/.test(address.phone))){ 
                    layer.msg('输入手机号码有误,请重新输入') 
                    return [false,'']; 
                }

                // 用户
                if(this.user_id == 0) return [false,'用户ID'];

                
                return [true];

            },
            // 
            handleChange:function(value){
                console.log(value);
                this.region_option = value;
            },
            changeTotalNum:function(value){
                console.log(value);
            },
            initPrice:function(){
                this.data.map(function(item){
                    // 租金
                    if(item.rent_obj && item.rent_num){
                        var rent_obj = item.rent_obj;
                        if(rent_obj.is_static==0){
                            item.rent_total_price = item.rent_num * 18;
                        }else{
                            if(rent_obj.map[0].min==12){
                                if(item.rent_num==12){
                                    item.rent_total_price = 3 * 200;
                                }else{
                                    item.rent_total_price = 3 * 150;
                                }
                            }
                            if(rent_obj.map[0].min==6){
                                item.rent_total_price = 270;
                            }
                            if(rent_obj.map[0].min==1){
                                if(item.rent_num<3){
                                    item.rent_total_price = item.rent_num * 183;
                                }else if(item.rent_num>=3&&item.rent_num<=5){
                                    item.rent_total_price = item.rent_num * 164;
                                }
                            }
                        }
                    }else{
                        item.rent_total_price = 0;
                    }
                    // 服务金
                    if(item.secure==0){
                        item.secure_total_price = 0;
                    }else{
                        item.secure_total_price = item.secure_price;
                    } 
                    var service_total_price = 0;
                    item.service.map(function(e){
                        var _service = item.service_list.find(function(s){
                            return s.service_id == e;
                        });
                        service_total_price += Number(_service.service_price);
                    })
                    // 
                    item.total_price = Number(service_total_price)+Number(item.secure_total_price)+Number(item.rent_total_price)+Number(item.goods_price);
                    item.all_total_price = Number(item.total_price) * item.total_num;                
                })
            }
        },
        mounted:function(){            

        },
        updated: function () {
            // 
            
        }
    })
    
})




</script>
<script>
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('.my-form').superForm();
        
        


        $('#addGoods').on('click',function(){                                   
            var key = new Date().getTime();
            
            





            $('#equip').append(html);   
            $('input[type="checkbox"],input[type="radio"]').uCheck();             
                                 
        });
        








    });



</script>
