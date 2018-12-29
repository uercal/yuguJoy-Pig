let App = getApp();
let loadMoreView, page, header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        type_index: 1,
        order_type_index: 1,
        menu_index: 1,
        system: {},
        data: [],
        dataInfo: {},
        isShowOrder: false,
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        page = 1;
        loadMoreView = this.selectComponent("#loadMoreView");
        header = this.selectComponent("#memberHeader");
        // this.getList();
        this.getMemberDetail();
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
        header.loadData();
    },


    getMemberDetail: function() {
        let _this = this;
        App.member_get('member.index/detail', {}, function(result) {
            _this.setData(result.data);
            if (result.data.menu.order) {
                _this.setData({
                    isShowOrder: true
                });
                _this.getOrderList();
            } else {
                _this.getList();
            }
        });
    },


    scanEquip: function() {
        wx.scanCode({
            onlyFromCamera: true,
            success(res) {
                console.log(res)
            }
        })
    },

    type: function(e) {
        let _this = this;
        let index = e.currentTarget.dataset.id;
        page = 1;
        this.setData({
            type_index: index,
            data: []
        });
        // 伪加载
        loadMoreView.loadMoreVirtual();
        // 真加载
        this.getList();
    },


    order_type: function(e) {
        let _this = this;
        let index = e.currentTarget.dataset.id;
        page = 1;
        this.setData({
            order_type_index: index,
            data: []
        });
        // 伪加载
        loadMoreView.loadMoreVirtual();
        // 真加载
        this.getOrderList();
    },


    menu: function(e) {
        let index = e.currentTarget.dataset.menu;
        if (index == 2) {
            wx.navigateTo({
                url: '/pages/member/notice/list'
            })
        }
        this.setData({
            menu_index: index
        });
    },

    // detail
    orderDetail: function(e) {
        let id = e.currentTarget.dataset.id;
        let type = e.currentTarget.dataset.type;
        let after_status = e.currentTarget.dataset.afterStatus;
        if (type == 'order') {
            wx.navigateTo({
                url: '/pages/member/order/send?id=' + id
            });
        }
        if (type == 'after') {
            if (after_status == 20) {
                wx.navigateTo({
                    url: '/pages/member/after/detail?id=' + id
                });
            }
            if (after_status == 30) {
                wx.navigateTo({
                    url: '/pages/member/after/reback?id=' + id
                });
            }
            if (after_status == 40) {
                wx.navigateTo({
                    url: '/pages/member/after/detail?id=' + id
                });
            }
        }
    },


    getList: function() {
        let _this = this;
        let type = this.data.type_index;
        App.member_get('member.Order/list', {
            page: page,
            type: type
        }, function(res) {
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


    // 需
    sendDetail: function(e) {
        let id = e.currentTarget.dataset.id;
        let order_type = e.currentTarget.dataset.orderType;
        if (order_type == 1) {
            //派送
            wx.navigateTo({
                url: '/pages/member/order/send?id=' + id + '&from=send'
            })
        }
        if (order_type == 2) {
            // 售后派遣
            wx.navigateTo({
                url: '/pages/member/after/detail?id=' + id + '&from=send'
            })
        }
    },


    getOrderList: function() {
        let _this = this;
        let type = this.data.order_type_index;
        App.member_get('member.Order/orderList', {
            page: page,
            type: type
        }, function(res) {
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