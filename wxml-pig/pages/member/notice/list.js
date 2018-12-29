let App = getApp();
let loadMoreView, page;
// pages/member/notice/list.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        system: {},
        data: [],
        dataInfo: {}
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        page = 1;
        loadMoreView = this.selectComponent("#loadMoreView");
        this.getNoticeList();
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {
        var that = this
        setTimeout(function() {
            // 延迟获取是为了解决真机上 windowHeight 值不对的问题
            wx.getSystemInfo({
                success: function(res) {
                    console.log(res)
                    that.setData({
                        system: res
                    })
                },
            })
        }, 500)
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

    read: function(e) {
        let _this = this;
        let id = e.currentTarget.dataset.id;
        let is_read = e.currentTarget.dataset.isread;
        if (is_read == 0) {
            App.member_get('member.Notice/read', {
                id: id
            }, function(res) {
                console.log(res);
                let obj = _this.data.data.find(function(e) {
                    return e.id == id;
                });
                obj.is_read = 1;
                _this.setData({
                    data: _this.data.data
                });
                wx.navigateTo({
                    url: '/pages/member/notice/detail?id=' + id
                })
            })
        } else {
            wx.navigateTo({
                url: '/pages/member/notice/detail?id=' + id
            })
        }

    },

    getNoticeList: function() {
        let _this = this;
        App.member_get('member.Notice/list', {
            page: page
        }, function(res) {
            _this.setData({
                dataInfo: res.data.data
            });
            if (page == 1) {
                _this.setData({
                    data: res.data.data.data
                });
            } else {
                let _data = _this.data.data;
                _data = _data.concat(res.data.data.data);
                _this.setData({
                    data: _data
                });
            }
            loadMoreView.loadMoreComplete(res.data.data);

        });
    },

    _onScrollToLower: function() {
        loadMoreView.loadMore()
    },

    loadMoreListener: function(e) {
        let _this = this;
        page += 1;
        _this.getNoticeList();
        // setTimeout(function() {
        //         _this.getNoticeList()
        // }, 1000);
    },

    clickLoadMore: function(e) {
        this.getNoticeList()
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