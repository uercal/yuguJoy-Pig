// pages/member/setting/password.js

let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        origin: '',
        password: '',
        _password: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {

    },

    origin: function(e) {
        let value = e.detail.value;
        this.setData({
            origin: value
        });
    },

    password: function(e) {
        let value = e.detail.value;
        this.setData({
            password: value
        });
    },

    _password: function(e) {
        let value = e.detail.value;
        this.setData({
            _password: value
        });
    },

    sub: function() {
        let origin = this.data.origin;
        let password = this.data.password;
        let _password = this.data._password;
        if (origin == '' || password == '' || _password == '') {
            App.showError('不能为空');
            return false;
        }

        if (password.length < 6) {
            App.showError('新密码长度不能小于6位');
            return false;
        }


        if (password != _password) {
            App.showError('两次密码不一致');
            return false;
        }

        if (password == origin) {
            App.showError('新密码与原密码一致');
            return false;
        }

        App.member_post('member.Member/resetPass', {
            origin: origin,
            password: password
        }, function(res) {
            if (res.code == 1) {
                wx.showModal({
                    title: '结果',
                    content: '修改成功',
                    showCancel: false,
                    confirmText: '确认',
                    success: function(res) {
                        if (res.confirm) {
                            wx.reLaunch({
                                url: '/pages/member/index'
                            })
                        }
                    }
                })

            }
        });

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