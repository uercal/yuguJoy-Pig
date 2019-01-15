let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        order_id: null,
        order: {},
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        this.data.order_id = options.order_id;
        this.getOrderDetail(options.order_id);
    },

    /**
     * 获取订单详情
     */
    getOrderDetail: function(order_id) {
        let _this = this;
        App._get('user.order/detail', {
            order_id
        }, function(result) {
            let goods_list = result.data.order.deduct;
            let _goods_list = [];
            for (var value of goods_list) {
                value.detailed = false;
                _goods_list.push(value);
            }
            result.data.order.deduct = _goods_list;
            _this.setData(result.data);
        });
    },


    chgDetail: function(e) {
        let index = e.currentTarget.dataset.index;
        this.data.order.deduct[index].detailed = !this.data.order.deduct[index].detailed;
        this.setData({
            order: this.data.order
        });
    },



    /**
     * 
     */
    //显示对话框
    showModal: function() {
        // 显示遮罩层
        var animation = wx.createAnimation({
            duration: 200,
            timingFunction: "linear",
            delay: 0
        })
        this.animation = animation
        animation.translateX(300).step()
        this.setData({
            animationData: animation.export(),
            showModalStatus: true
        });
        setTimeout(function() {
            animation.translateX(0).step()
            this.setData({
                animationData: animation.export()
            })
        }.bind(this), 200)
    },
    //隐藏对话框
    hideModal: function() {
        // 隐藏遮罩层
        var animation = wx.createAnimation({
            duration: 200,
            timingFunction: "linear",
            delay: 0
        })
        this.animation = animation
        animation.translateY(300).step()
        this.setData({
            animationData: animation.export(),
        })
        setTimeout(function() {
            animation.translateY(0).step()
            this.setData({
                animationData: animation.export(),
                showModalStatus: false
            })
        }.bind(this), 200)
    },



    /**
     * 跳转到商品详情
     */
    goodsDetail: function(e) {
        let goods_id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '../goods/index?goods_id=' + goods_id
        });
    },

    /**
     * 取消订单
     */
    cancelOrder: function(e) {
        let _this = this;
        let order_id = _this.data.order_id;
        wx.showModal({
            title: "提示",
            content: "确认取消订单？",
            success: function(o) {
                if (o.confirm) {
                    App._post_form('user.order/cancel', {
                        order_id
                    }, function(result) {
                        wx.navigateBack();
                    });
                }
            }
        });
    },

    /**
     * 发起付款
     */
    payOrder: function(e) {
        let order_id = this.data.order_id;
        wx.navigateTo({
            url: '/pages/pay/index?from=order&id=' + order_id
        });
    },

    /**
     * 确认收货
     */
    receipt: function(e) {
        let _this = this;
        let order_id = _this.data.order_id;
        wx.showModal({
            title: "提示",
            content: "确认收到商品？",
            success: function(o) {
                if (o.confirm) {
                    App._post_form('user.order/receipt', {
                        order_id
                    }, function(result) {
                        _this.getOrderDetail(order_id);
                    });
                }
            }
        });
    },


});