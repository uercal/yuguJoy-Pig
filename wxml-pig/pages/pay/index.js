// pages/pay/index.js

let App = getApp();


Page({

    /**
     * 页面的初始数据
     */
    data: {

    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        if (options.from) {
            this.setData({
                dataType: options.from,
                id: options.id
            });
            if (options.from == 'order') {
                this.payOrder(options.id);
            }
            if (options.from == 'after') {
                this.payAfter(options.id);
            }
        }
    },

    payOrder: function(order_id) {
        let _this = this;
        App._get('user.order/detail', {
            order_id
        }, function(result) {
            result.data.order.pay.goods_all_price = result.data.order.pay.goods_all_price.toFixed(2);
            result.data.order.pay.rent_all_price = result.data.order.pay.rent_all_price.toFixed(2);
            _this.setData(result.data);
        });
    },

    payAfter: function(after_id) {
        let _this = this;
        App._get('user.order/afterDetail', {
            after_id: after_id
        }, function(result) {
            _this.setData(result.data);
        });
    },


    // 确认支付
    sub: function() {
        let type = this.data.dataType;
        let id = this.data.id;
        wx.showLoading({
            title: '支付中',
            mask: true
        })
        App._post_form('user.Order/doPay', {
            id: id,
            type: type
        }, function(res) {
            if (res.code == 1) {
                wx.showModal({
                    title: '结果',
                    content: '成功',
                    showCancel: false,
                    confirmText: '确认',
                    success: function(res) {
                        if (res.confirm) {
                            wx.switchTab({
                                url: '/pages/user/index'
                            })
                        }
                    }
                })
            } else {
                App.showError('支付失败', function() {
                    wx.switchTab({
                        url: '/pages/user/index'
                    })
                });
            }
        })
    },

    recharge: function() {
        wx.redirectTo({
            url: '/pages/user/recharge'
        })
    },
    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {

    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
        this.getDetail();
    },

    getDetail: function() {

    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function() {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function() {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh: function() {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function() {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    }
})