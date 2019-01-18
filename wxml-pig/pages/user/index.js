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
    checkStatus: function(e) {
        wx.navigateTo({
            url: '/pages/user/apply'
        })        
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
        wx.navigateTo({
            url: '/pages/recharge/choose'
        })
    },
    getPhoneNumber: function(e) {
        let _this = this;
        wx.showLoading({
            title: "正在获取",
            mask: true
        });
        wx.login({
            success: function(res) {
                // 发送用户信息
                App._post_form('user/phone', {
                    encrypted_data: encodeURIComponent(e.detail.encryptedData),
                    code: res.code,
                    encrypted_data: e.detail.encryptedData,
                    iv: e.detail.iv,
                    token: wx.getStorageSync('token')
                }, function(result) {
                    // 记录token user_id                       
                    let bind = result.data.bind_phone;
                    if (bind == 1) {
                        let str = "userInfo.phone";
                        _this.setData({
                            [str]: 1
                        });
                        wx.showToast({
                            title: '绑定成功',
                        });
                    } else {
                        //                                                
                    }
                }, false, function() {
                    wx.hideLoading();
                });
            }
        });
    },

})