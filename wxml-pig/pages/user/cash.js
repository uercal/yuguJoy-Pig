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
        let actual_money = this.data.userInfo.account_money.actual_money;

        if (_this.data.pay_price == '') {
            App.showError('提现金额不能为空');
            return false;
        }

        if (Number(_this.data.pay_price) <= 0) {
            App.showError('请填写有效数字');
            return false;
        }

        if (Number(_this.data.pay_price) >= Number(actual_money)) {
            App.showError('提现金额不能大于账户余额');
            return false;
        }


        if (_this.data.disabled) {
            return false;
        }

        if (_this.data.hasError) {
            App.showError(_this.data.error);
            return false;
        }

        // 按钮禁用, 防止二次提交
        _this.data.disabled = true;

        // 显示loading
        wx.showLoading({
            title: '正在处理...'
        });

        App._post_form('user.Recharge/examCash', {
            price: _this.data.pay_price
        }, function(res) {
            if (res.code == 1) {
                wx.showModal({
                    title: '提示',
                    content: '申请成功,工作人员稍候将与您联系',
                    showCancel: false,
                    success(res) {
                        if (res.confirm) {
                            wx.reLaunch({
                                url: '/pages/user/index'
                            })
                        }
                    }
                })
            }
        })

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