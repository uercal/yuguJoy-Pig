// pages/user/recharge.js

let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        disabled: false,
        pay_price: '',
        hasError: false,
        error: '',
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        // 获取当前用户信息
        this.getUserDetail();
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {

    },

    getUserDetail: function() {
        let _this = this;
        App._get('user.index/detail', {}, function(result) {
            _this.setData(result.data);
        });
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {

    },

    value: function(e) {
        this.setData({
            pay_price: e.detail.value
        });
    },

    /**
     * 发起付款
     */
    submitOrder: function() {
        let _this = this;

        if (_this.data.pay_price == '') {
            App.showError('充值金额不能为空');
            return false;
        }


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
                        url: '../recharge/index',
                    });
                });
                return false;
            }
            // 发起微信支付
            wx.requestPayment({
                timeStamp: result.data.payment.timeStamp,
                nonceStr: result.data.payment.nonceStr,
                package: 'prepay_id=' + result.data.payment.prepay_id,
                signType: 'MD5',
                paySign: result.data.payment.paySign,
                success: function(res) {
                    // 跳转到订单详情
                    wx.redirectTo({
                        url: '../recharge/index',
                    });
                },
                fail: function() {
                    App.showError('充值订单未支付', function() {
                        // 跳转到未付款订单
                        wx.redirectTo({
                            url: '../recharge/index',
                        });
                    });
                },
            });
        };

        // 按钮禁用, 防止二次提交
        _this.data.disabled = true;

        // 显示loading
        wx.showLoading({
            title: '正在处理...'
        });

        // 创建订单-立即购买

        App._post_form('user.recharge/pay', {
            price: _this.data.pay_price
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