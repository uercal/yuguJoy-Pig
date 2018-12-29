let App = getApp();
let header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        system: {}
    },


    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#memberHeader");
        this.data.equip_id = options.equip_id;
        this.getDetail(options.equip_id);
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

    getDetail: function(equip_id) {
        let _this = this;
        App.member_get('member.Equip/detail', {
            equip_id: equip_id
        }, function(res) {
            _this.setData(res.data);
            let detail = res.data.detail;
            wx.setNavigationBarTitle({
                title: detail.goods_name + '(' + detail.status_text + ')'
            });
        })
    },

    go2order: function(e) {
        console.log(e);
        let order_id = e.currentTarget.dataset.orderId;
        wx.navigateTo({
            url: '/pages/member/order/detail?order_id=' + order_id
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
        this._onScrollToLower();
    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    }
})