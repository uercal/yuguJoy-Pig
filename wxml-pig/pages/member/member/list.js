let App = getApp();
let loadMoreView, page, header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        choose: false,
        status: 1,
        system: {},
        data: [],
        dataInfo: {},
        choose_member: [],
        choose_member_ids: []
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        page = 1;
        loadMoreView = this.selectComponent("#loadMoreView");
        header = this.selectComponent("#memberHeader");
        if (options.from == 'choose') {
            let pages = getCurrentPages();
            let prevPage = pages[pages.length - 2]; //上一个页面
            this.setData({
                choose: true,
                choose_member: prevPage.data.addMember,
                choose_member_ids: prevPage.data.addMemberIds

            })
        }
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

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
        header.loadData();
    },

    type: function(e) {
        let _this = this;
        let index = e.currentTarget.dataset.id;
        page = 1;
        this.setData({
            status: index,
            data: []
        });
        // 伪加载
        loadMoreView.loadMoreVirtual();
        // 真加载
        this.getList();
    },

    getList: function() {
        let _this = this;
        let type = this.data.status;
        let choose_member_ids = this.data.choose_member_ids;
        App.member_get('member.Member/list', {
            page: page,
            type: type
        }, function(res) {
            _this.setData({
                dataInfo: res.data.list
            });

            if (choose_member_ids.length > 0 && res.data.list.data) {
                res.data.list.data.map(function(e) {
                    if (choose_member_ids.find(item => item == e.id)) {
                        e.checked = true;
                    } else {
                        e.checked = false;
                    }
                });
            }

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


    getSearchContent: function(e) {
        let phone = e.detail.value;
        App.member_get('member.Member/search', {
            phone: phone
        }, function(res) {
            if (res.data) {
                wx.navigateTo({
                    url: '/pages/member/member/search?phone=' + phone
                })
            }
        });
    },

    detail: function(e) {
        let user_id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '/pages/member/user/detail?user_id=' + user_id
        })
    },

    // 

    choose: function(e) {
        let index = e.currentTarget.dataset.index;
        let checked = this.data.data[index].checked;
        let str = "data[" + index + "].checked";
        this.setData({
            [str]: checked ? !checked : true
        })
        let pages = getCurrentPages();
        let prevPage = pages[pages.length - 2]; //上一个页面
        let member = prevPage.data.addMember;
        let member_ids = prevPage.data.addMemberIds;
        if (!checked) {
            member.push(this.data.data[index]);
            member_ids.push(this.data.data[index].id);
        } else {
            member.splice(member.findIndex(item => item.id === this.data.data[index].id), 1);
            member_ids.splice(member_ids.findIndex(item => item === this.data.data[index].id), 1);
        }
        prevPage.setData({
            addMember: member,
            addMemberIds: member_ids
        });
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