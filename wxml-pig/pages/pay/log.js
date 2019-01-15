let App = getApp();
let loadMoreView, page;
// pages/member/index.js
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
        this.getList();
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


    detail: function(e) {
        let index = e.currentTarget.dataset.index;
        let detailed = this.data.data[index].detailed;
        let str = "data[" + index + "].detailed";
        this.setData({
            [str]: !detailed
        });
    },



    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {

    },

    getList: function() {
        let _this = this;
        App._get('user.Order/payLogList', {
            page: page
        }, function(res) {
            res.data.list.data.map(function(e) {
                e.detailed = false;
            });
            // 
            _this.setData({
                dataInfo: res.data.list
            });

            if (page == 1) {
                _this.setData({
                    data: res.data.list.data
                });
            } else {
                let _data = _this.data.data;
                _data = _data.concat(res.data.list.data);
                _this.setData({
                    data: _data
                });
            }
            loadMoreView.loadMoreComplete(res.data.list);
        });
    },
    _onScrollToLower: function() {
        loadMoreView.loadMore()
    },

    loadMoreListener: function(e) {
        let _this = this;
        page += 1;
        _this.getList();
        // setTimeout(function() {
        //         _this.getNoticeList()
        // }, 1000);
    },

    clickLoadMore: function(e) {
        this.getList()
    },


    /**
     * 生命周期函数-监听页面隐藏
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