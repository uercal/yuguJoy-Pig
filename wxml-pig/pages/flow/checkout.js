let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        nav_select: false, // 快捷导航
        options: {}, // 当前页面参数

        address: null, // 默认收货地址
        exist_address: false, // 是否存在收货地址
        goods: {}, // 商品信息

        disabled: false,

        hasError: false,
        error: '',
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        // 当前页面参数
        this.data.options = options;
        console.log(options);
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
        // 获取当前订单信息
        this.getOrderData();
        // 获取地址信息
        this.initAddress();

        // 

    },

    initAddress: function() {
        let _this = this;
        App._get('address/lists', {}, function(result) {
            let address = result.data.list.find(function(e) {
                return e.address_id == result.data.default_id;
            });
            if (address) {
                _this.data.exist_address = true;
                _this.setData({
                    address: address
                });
            }


        });
    },


    /**
     * 获取当前订单信息
     */
    getOrderData: function() {
        let _this = this,
            options = _this.data.options;

        // 获取订单信息回调方法
        let callback = function(result) {
            if (result.code !== 1) {
                App.showError(result.msg);
                return false;
            }
            // 显示错误信息
            if (result.data.has_error) {
                _this.data.hasError = true;
                _this.data.error = result.data.error_msg;
                App.showError(_this.data.error);
            }
            console.log(result.data);
            let goods_list = result.data.goods_list;
            let _goods_list = [];
            for (var value of goods_list) {
                value.detailed = false;
                _goods_list.push(value);
            }
            result.data.goods_list = _goods_list;
            _this.setData(result.data);
        };

        // 立即购买
        if (options.order_type === 'buyNow') {
            console.log(options);
            App._get('order/buyNow', {
                goods_id: options.goods_id,
                goods_num: options.goods_num,
                goods_sku_id: options.goods_sku_id,
                rent_id: options.rent_id,
                rent_num: options.rent_num,
                rent_date: options.rent_date,
                secure: options.secure,
                service_ids: options.service_ids,
            }, function(result) {
                callback(result);
            });
        }

        // 购物车结算
        else if (options.order_type === 'cart') {
            let order_total_num = 0;
            let order_total_price = 0;
            let data = new Object;
            let result = new Object;
            let goods_list = App.globalData.order_goods_list;
            result.code = 1;
            for (let i in goods_list) {
                order_total_num += Number(goods_list[i].total_num);
                order_total_price += Number(goods_list[i].all_total_price);
            }
            data.goods_list = goods_list;
            data.order_total_num = order_total_num;
            data.order_total_price = order_total_price.toFixed(2);
            // 
            result.data = data;
            callback(result);
        }

    },

    /**
     * 选择收货地址
     */
    selectAddress: function() {
        wx.navigateTo({
            url: '../address/' + (this.data.exist_address ? 'index?from=flow' : 'create')
        });
    },


    chgDetail: function(e) {
        let index = e.currentTarget.dataset.index;
        this.data.goods_list[index].detailed = !this.data.goods_list[index].detailed;
        this.setData({
            goods_list: this.data.goods_list
        });
    },




    /**
     * 订单提交
     */
    submitOrder: function() {
        let _this = this,
            options = _this.data.options;

        if (_this.data.disabled) {
            return false;
        }

        if (_this.data.hasError) {
            App.showError(_this.data.error);
            return false;
        }

        // 订单创建成功后回调--微信支付
        let callback = function(result) {
            if (result.code === -10) {
                App.showError(result.msg, function() {
                    // 跳转到未付款订单
                    wx.redirectTo({
                        url: '../order/index?type=payment',
                    });
                });
                return false;
            }

            wx.redirectTo({
                url: '/pages/pay/index?from=order&id=' + result.data.order_id
            });
            
        };

        // 按钮禁用, 防止二次提交
        _this.data.disabled = true;

        // 显示loading
        wx.showLoading({
            title: '正在处理...'
        });

        // 创建订单-立即购买
        if (options.order_type === 'buyNow') {
            App._post_form('order/buyNow', {
                goods_id: options.goods_id,
                goods_num: options.goods_num,
                goods_sku_id: options.goods_sku_id,
                rent_id: options.rent_id,
                rent_num: options.rent_num,
                rent_date: options.rent_date,
                secure: options.secure,
                service_ids: options.service_ids
            }, function(result) {
                // success
                console.log('success');
                callback(result);
            }, function(result) {
                // fail
                console.log('fail');
            }, function() {
                // complete
                console.log('complete');
                // 解除按钮禁用
                _this.data.disabled = false;
            });
        }

        // 创建订单-购物车结算
        else if (options.order_type === 'cart') {
            let goods_list = _this.data.goods_list;
            let choose_key_list = [];
            goods_list.map(function(e) {
                if (e.checked) choose_key_list.push(e.key);
            })
            choose_key_list = choose_key_list.join('#');
            console.log(choose_key_list);
            App._post_form('order/cart', {
                choose_key_list: choose_key_list
            }, function(result) {
                // success
                console.log('success');
                callback(result);
            }, function(result) {
                // fail
                console.log('fail');
            }, function() {
                // complete
                console.log('complete');
                // 解除按钮禁用
                _this.data.disabled = false;
            });
        }

    },

    /**
     * 快捷导航 显示/隐藏
     */
    commonNav: function() {
        this.setData({
            nav_select: !this.data.nav_select
        });
    },

    /**
     * 快捷导航跳转
     */
    nav: function(e) {
        let url = '';
        switch (e.currentTarget.dataset.index) {
            case 'home':
                url = '../index/index';
                break;
            case 'fenlei':
                url = '../category/index';
                break;
            case 'cart':
                url = '../flow/index';
                break;
            case 'profile':
                url = '../user/index';
                break;
        }
        wx.switchTab({
            url
        });
    }


});