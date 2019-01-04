let App = getApp();

// pages/member/login.js
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

    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {

    },
    saveData: function(e) {
        let _this = this,
            values = e.detail.value;

        // 提交到后端
        App._post_form('member/login', values, function(res) {
            console.log(res);
            if (res.code == 1) {
                wx.setStorageSync('member_id', res.data.member_id);
                wx.setStorageSync('member_token', res.data.member_token);
                wx.redirectTo({
                    url: '/pages/member/index'
                })
            }
        }, false, function(res) {

        });
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

    chgPhone: function(e) {
        let value = e.detail.value;
        if (value == "") {
            this.setData({
                chgPhone: false
            });
        } else {
            this.setData({
                chgPhone: true
            });
        }
        this.allExist();
    },

    chgPass: function(e) {
        let value = e.detail.value;
        if (value == "") {
            this.setData({
                chgPass: false
            });
        } else {
            this.setData({
                chgPass: true
            });
        }
        this.allExist();
    },

    allExist: function() {
        if (this.data.chgPass && this.data.chgPhone) {
            this.setData({
                allExist: true
            });
        } else {
            this.setData({
                allExist: false
            });
        }
    },

    focus: function(e) {
        console.log(e);
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