// pages/member/setting/index.js

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
        this.loadUser();
    },

    loadUser: function() {
        let _this = this;
        App._get('user.index/detail', {}, function(result) {
            _this.setData(result.data);
        });
        App.member_get('member.index/detail', {}, function(result) {
            _this.setData(result.data);
        });
        wx.getStorage({
            key: 'member_id',
            success: function(res) {
                _this.setData({
                    member_id: res.data
                })
            },
        })
    },

    resetPass: function() {
        wx.navigateTo({
            url: '/pages/member/setting/password'
        })
    },
    exit: function(e) {
        wx.showModal({
            title: '确认',
            content: '是否确认退出员工端？',
            showCancel: true,
            cancelText: '取消',
            confirmText: '确认',
            success: function(res) {
                if (res.confirm) {
                    wx.removeStorage({
                        key: 'member_info'
                    });
                    wx.removeStorage({
                        key: 'member_id'
                    });
                    wx.removeStorage({
                        key: 'member_token'
                    });
                    wx.switchTab({
                        url: '/pages/user/index'
                    })
                }
            }
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