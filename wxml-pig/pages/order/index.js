let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        dataType: 'all',
        list: [],
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        this.data.dataType = options.type || 'all';
        this.setData({
            dataType: this.data.dataType
        });
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
        // 获取订单列表
        this.getOrderList(this.data.dataType);
    },

    /**
     * 获取订单列表
     */
    getOrderList: function(dataType) {
        let _this = this;
        App._get('user.order/lists', {
            dataType
        }, function(result) {
            _this.setData(result.data);
            result.data.list.length && wx.pageScrollTo({
                scrollTop: 0
            });
        });
    },

    /**
     * 切换标签
     */
    bindHeaderTap: function(e) {
        this.setData({
            dataType: e.target.dataset.type
        });
        // 获取订单列表
        this.getOrderList(e.target.dataset.type);
    },

    /**
     * 取消订单
     */
    cancelOrder: function(e) {
        let _this = this;
        let order_id = e.currentTarget.dataset.id;
        wx.showModal({
            title: "提示",
            content: "确认取消订单？",
            success: function(o) {
                if (o.confirm) {
                    App._post_form('user.order/cancel', {
                        order_id
                    }, function(result) {
                        _this.getOrderList(_this.data.dataType);
                    });
                }
            }
        });
    },


    cancelReOrder: function(e) {
        let _this = this;
        let id = e.currentTarget.dataset.id;
        wx.showModal({
            title: "提示",
            content: "确认取消订单？",
            success: function(o) {
                if (o.confirm) {
                    App._post_form('user.recharge/cancel', {
                        id
                    }, function(result) {
                        _this.getOrderList(_this.data.dataType);
                    });
                }
            }
        });
    },



    /**
     * 确认收货
     */
    receipt: function(e) {
        let _this = this;
        let order_id = e.currentTarget.dataset.id;
        wx.showModal({
            title: "提示",
            content: "确认收到商品？",
            success: function(o) {
                if (o.confirm) {
                    App._post_form('user.order/receipt', {
                        order_id
                    }, function(result) {
                        _this.getOrderList(_this.data.dataType);
                    });
                }
            }
        });
    },

    /**
     * 发起付款
     */
    payOrder: function(e) {
        let _this = this;
        let order_id = e.currentTarget.dataset.id;

        wx.navigateTo({
            url: '/pages/pay/index?from=order&id=' + order_id
        });

    },

    payReOrder: function(e) {
        let _this = this;
        let id = e.currentTarget.dataset.id;
        // 显示loading
        wx.showLoading({
            title: '正在处理...',
        });
        App._post_form('user.Recharge/rechargePay', {
            id
        }, function(result) {
            if (result.code === -10) {
                App.showError(result.msg);
                return false;
            }
            console.log(result);
            // 发起微信支付
            wx.requestPayment({
                timeStamp: result.data.timeStamp,
                nonceStr: result.data.nonceStr,
                package: 'prepay_id=' + result.data.prepay_id,
                signType: 'MD5',
                paySign: result.data.paySign,
                success: function(res) {
                    // 跳转到已付款订单
                    wx.redirectTo({
                        url: '/pages/recharge/index',
                    })
                },
                fail: function() {
                    App.showError('订单未支付');
                },
            });
        });
    },


    afterOrder: function(e) {
        let order_id = e.currentTarget.dataset.id;
        let after_status = e.currentTarget.dataset.afterStatus;
        if (after_status == 0) {
            wx.navigateTo({
                url: '../order/afterSend?order_id=' + order_id
            });
        } else {
            App.showError('当前订单有正在进行的售后');
        }
    },


    /**
     * 跳转订单详情页
     */
    detail: function(e) {
        let order_id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '../order/detail?order_id=' + order_id
        });
    },

    onPullDownRefresh: function() {
        wx.stopPullDownRefresh();
    }


});