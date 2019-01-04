let App = getApp();
let header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        content: ''
    },


    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#memberHeader");
        this.data.equip_id = options.equip_id;
        this.getDetail(options.equip_id);
        this.getCheckDetail(options.equip_id);
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
        header.loadData();
    },

    content: function(e) {
        this.setData({
            content: e.detail.value
        })
    },

    getDetail: function(equip_id) {
        let _this = this;
        App.member_get('member.Equip/detail', {
            equip_id: equip_id
        }, function(res) {
            _this.setData(res.data);
        })
    },

    getCheckDetail: function(equip_id) {
        let _this = this;
        App.member_get('member.Equip/checkDetail', {
            equip_id: equip_id
        }, function(res) {
            _this.setData(res.data);
        })
    },


    checkDone: function() {
        let equip_id = this.data.equip_id;
        let content = this.data.content;
        wx.showActionSheet({
            itemList: ['维修成功', '维修失败'],
            itemColor: '',
            success: function(res) {
                let result = res.tapIndex;
                wx.showModal({
                    title: '确认',
                    content: result == 0 ? '确认维修成功？' : '确认维修失败？',
                    showCancel: true,
                    cancelText: '取消',
                    confirmText: '确认',
                    success: function(_res) {
                        if (_res.confirm) {
                            App.member_post('member.Equip/checkingRes', {
                                equip_id: equip_id,
                                result: result,
                                content: content
                            }, function(__res) {
                                if (__res.code == 1) {
                                    wx.showModal({
                                        title: '提示',
                                        content: '操作成功',
                                        showCancel: false,
                                        success(res) {
                                            wx.reLaunch({
                                                url: '/pages/member/index'
                                            })
                                        }
                                    })
                                } else {
                                    App.showError('提交失败');
                                }
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
        this._onScrollToLower();
    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    }
})