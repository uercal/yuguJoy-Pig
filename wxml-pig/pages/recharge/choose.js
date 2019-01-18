let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        userInfo: {},
        orderCount: {},
        infoStatus: {}
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
        // 获取当前用户信息
        this.getUserDetail();
    },

    /**
     * 获取当前用户信息
     */
    getUserDetail: function() {
        let _this = this;
        App._get('user.index/detail', {}, function(result) {
            _this.setData(result.data);
            App.globalData.favorite_goods = result.data.userInfo.favorite_goods_ids;
            console.log(result.data.userInfo);
        });
    },

    recharge: function() {
        wx.navigateTo({
            url: '/pages/user/recharge'
        })
    },

    cash: function() {
        // 
        App._get('user.Recharge/applyCash', {},
            function(res) {
                if (res.code == 1) {
                    wx.navigateTo({
                        url: '/pages/user/cash'
                    })
                }
            });

    },


})