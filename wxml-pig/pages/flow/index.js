let App = getApp();

Page({

        /**
         * 页面的初始数据
         */
        data: {
                goods_list: [], // 商品列表
                order_total_num: 0,
                order_total_price: 0,
                is_all: false,
        },

        /**
         * 生命周期函数--监听页面加载
         */
        onLoad: function(options) {

        },

        /**
         * 生命周期函数--监听页面显示
         */
        onShow: function() {
                this.getCartList();
                this.setData({
                        is_all: false
                })
        },

        /**
         * 获取购物车列表
         */
        getCartList: function() {
                let _this = this;
                App._get('cart/lists', {}, function(result) {
                        let list = result.data.goods_list;
                        for (let i in list) {
                                let map = JSON.parse(list[i].rent_info.map);
                                console.log(map);
                                let rent_num = list[i].rent_num;
                                if (!map) {
                                        list[i].rent_price = list[i].rent_info.price;
                                }
                                for (let j in map) {
                                        if (rent_num >= map[j].min && rent_num <= map[j].max) {
                                                list[i].rent_price = map[j].price;
                                                break;
                                        }
                                        if (rent_num >= map[j].min && !map[j].max) {
                                                list[i].rent_price = map[j].price;
                                                break;
                                        }
                                }
                        }
                        result.data.goods_list = list;
                        _this.setData(result.data);
                });
        },

        // 勾选
        checkIndex: function(e) {
                let index = e.currentTarget.dataset.index;
                this.data.goods_list[index].checked = this.data.goods_list[index].checked ? !this.data.goods_list[index].checked : true;
                let is_all = this.isAll();
                this.setData({
                        goods_list: this.data.goods_list,
                        is_all: is_all
                })
        },
        isAll: function() {
                let list = this.data.goods_list;
                let state = true;
                let total_price = 0;
                for (let i in list) {
                        if (!list[i].checked) {
                                state = false;
                        } else {
                                total_price += Number(list[i].all_total_price);
                        }
                }
                this.setData({
                        order_total_price: total_price.toFixed(2)
                });
                return state;
        },
        allCheck: function() {
                let list = this.data.goods_list;
                let is_all = this.data.is_all;
                if (!is_all) {
                        for (let i in list) {
                                list[i].checked = true;
                        }
                } else {
                        for (let i in list) {
                                list[i].checked = false;
                        }
                }
                is_all = !is_all;
                this.isAll();
                this.setData({
                        goods_list: list,
                        is_all: is_all
                });
        },
        /**
         * 递增指定的商品数量
         */
        addCount: function(e) {
                console.log(e);
                let _this = this,
                        index = e.currentTarget.dataset.index,
                        goods_spec_id = e.currentTarget.dataset.goods_spec_id,
                        goods = _this.data.goods_list[index],
                        rent_id = e.currentTarget.dataset.rent_id,
                        rent_num = e.currentTarget.dataset.rent_num,
                        rent_date = e.currentTarget.dataset.rent_date,
                        secure = e.currentTarget.dataset.secure,
                        service_ids = e.currentTarget.dataset.service_ids;
                // 后端同步更新
                wx.showLoading({
                        title: '加载中',
                        mask: true
                })
                App._post_form('cart/add', {
                        goods_id: goods.goods_id,
                        goods_num: 1,
                        goods_spec_id: goods_spec_id,
                        rent_id: rent_id,
                        rent_num: rent_num,
                        rent_date: rent_date,
                        secure: secure,
                        service_ids: service_ids
                }, function() {
                        goods.total_num++;
                        goods.all_total_price = Number(goods.total_price) * goods.total_num;
                        goods.all_total_price = goods.all_total_price.toFixed(2);
                        _this.setData({
                                ['goods_list[' + index + ']']: goods,
                        });
                        _this.isAll();
                });
        },

        /**
         * 递减指定的商品数量
         */
        minusCount: function(e) {
                let _this = this,
                        index = e.currentTarget.dataset.index,
                        goods_spec_id = e.currentTarget.dataset.goods_spec_id,
                        goods = _this.data.goods_list[index],
                        rent_id = e.currentTarget.dataset.rent_id,
                        rent_num = e.currentTarget.dataset.rent_num,
                        rent_date = e.currentTarget.dataset.rent_date,
                        secure = e.currentTarget.dataset.secure,
                        service_ids = e.currentTarget.dataset.service_ids;
                if (goods.total_num > 1) {
                        // 后端同步更新
                        wx.showLoading({
                                title: '加载中',
                                mask: true
                        })
                        App._post_form('cart/sub', {
                                goods_id: goods.goods_id,
                                goods_spec_id: goods_spec_id,
                                rent_id: rent_id,
                                rent_num: rent_num,
                                rent_date: rent_date,
                                secure: secure,
                                service_ids: service_ids
                        }, function() {
                                goods.total_num--;
                                goods.all_total_price = Number(goods.total_price) * goods.total_num;
                                goods.all_total_price = goods.all_total_price.toFixed(2);
                                goods.total_num > 0 &&
                                        _this.setData({
                                                ['goods_list[' + index + ']']: goods,
                                        });
                                _this.isAll();
                        });

                }
        },

        /**
         * 删除商品
         */
        del: function(e) {
                let _this = this,
                        goods_id = e.currentTarget.dataset.goodsId,
                        goods_spec_id = e.currentTarget.dataset.goods_spec_id,
                        rent_id = e.currentTarget.dataset.rent_id,
                        rent_num = e.currentTarget.dataset.rent_num,
                        rent_date = e.currentTarget.dataset.rent_date,
                        secure = e.currentTarget.dataset.secure,
                        service_ids = e.currentTarget.dataset.service_ids;
                wx.showModal({
                        title: "提示",
                        content: "您确定要移除当前商品吗?",
                        success: function(e) {
                                e.confirm && App._post_form('cart/delete', {
                                        goods_id,
                                        goods_spec_id: goods_spec_id,
                                        rent_id: rent_id,
                                        rent_num: rent_num,
                                        rent_date: rent_date,
                                        secure: secure,
                                        service_ids: service_ids
                                }, function(result) {
                                        _this.getCartList();
                                });
                        }
                });
        },

        /**
         * 购物车结算
         */
        submit: function(t) {
                let is_empty = true;
                let order_goods_list = new Array();
                for (let i in this.data.goods_list) {
                        if (this.data.goods_list[i].checked) {
                                is_empty = false;
                                order_goods_list.push(this.data.goods_list[i]);
                        }
                }
                if (is_empty) {
                        wx.showToast({
                                title: '请勾选商品',
                                icon: 'none'
                        })
                        return false;
                }
                App.globalData.order_goods_list = order_goods_list;

                wx.navigateTo({
                        url: '../flow/checkout?order_type=cart'
                });
        },

        /**
         * 加法
         */
        mathadd: function(arg1, arg2) {
                return (Number(arg1) + Number(arg2)).toFixed(2);
        },

        /**
         * 减法
         */
        mathsub: function(arg1, arg2) {
                return (Number(arg1) - Number(arg2)).toFixed(2);
        },


        /**
         * 更换服务
         */
        changeService: function(e) {
                let _this = this,
                        goods_id = e.currentTarget.dataset.goodsId,
                        goods_spec_id = e.currentTarget.dataset.goods_spec_id,
                        total_num = e.currentTarget.dataset.total_num,
                        rent_id = e.currentTarget.dataset.rent_id,
                        rent_num = e.currentTarget.dataset.rent_num,
                        rent_date = e.currentTarget.dataset.rent_date,
                        secure = e.currentTarget.dataset.secure,
                        service_ids = e.currentTarget.dataset.service_ids;

                wx.navigateTo({
                        url: './service?' + App.urlEncode({
                                goods_id: goods_id,
                                goods_spec_id: goods_spec_id,
                                total_num: total_num,
                                // 租赁信息
                                rent_id: rent_id,
                                rent_num: rent_num,
                                rent_date: rent_date,
                                // 保险
                                secure: secure,
                                //增值服务
                                service_ids: service_ids
                        })
                });
        },

        /**
         * 更换租赁信息
         */
        changeRent: function(e) {
                let _this = this,
                        goods_id = e.currentTarget.dataset.goodsId,
                        goods_spec_id = e.currentTarget.dataset.goods_spec_id,
                        total_num = e.currentTarget.dataset.total_num,
                        rent_id = e.currentTarget.dataset.rent_id,
                        rent_num = e.currentTarget.dataset.rent_num,
                        rent_date = e.currentTarget.dataset.rent_date,
                        secure = e.currentTarget.dataset.secure,
                        service_ids = e.currentTarget.dataset.service_ids;

                wx.navigateTo({
                        url: './rent?' + App.urlEncode({
                                goods_id: goods_id,
                                goods_spec_id: goods_spec_id,
                                total_num: total_num,
                                // 租赁信息
                                rent_id: rent_id,
                                rent_num: rent_num,
                                rent_date: rent_date,
                                // 保险
                                secure: secure,
                                //增值服务
                                service_ids: service_ids
                        })
                });




        },


        /**
         * 去购物
         */
        goShopping: function() {
                wx.switchTab({
                        url: '../index/index',
                });
        },

})