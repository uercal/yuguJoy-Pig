let App = getApp();
let loadMoreView, page, header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        cate_id: null,
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#memberHeader");
        this.loadCateData();
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

    loadCateData: function() {
        let _this = this;
        App.member_get('member.Count/getCateList', {}, function(res) {
            res.data.list.map(function(e, index) {
                e.checked = index == 0 ? true : false
            });
            _this.setData(res.data);
            _this.setData({
                cate_id: res.data.list[0].category_id
            });
            _this.loadDetail();
        });
    },


    loadDetail: function() {
        let cate_id = this.data.cate_id;
        let _this = this;
        App.member_post('member.Count/getDetail', {
            cate_id: cate_id
        }, function(res) {
            _this.setData(res.data);
        });
    },

    cate: function(e) {
        let cate_id = e.currentTarget.dataset.id;
        this.setData({
            cate_id: cate_id
        });        
        this.loadDetail();
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