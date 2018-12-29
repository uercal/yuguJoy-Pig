// pages/member/after/detail.js

let header;
let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        isSend: false,
        addMember: [],
        addMemberIds: []
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#header");
        this.data.id = options.id;
        this.setData({
            isSend: options.from == 'send'
        })
        if (options.from == 'send') {
            this.getSendDetail();
        } else {
            this.getDetail();
        }
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


    getDetail: function() {
        let id = this.data.id;
        let _this = this;
        App.member_get('member.order/detail', {
            id: id,
            type: 'after'
        }, function(result) {
            _this.setData(result.data);
        });
    },


    getSendDetail: function() {
        let id = this.data.id;
        let _this = this;
        App.member_get('member.order/sendAfterDetail', {
            id: id
        }, function(result) {
            _this.setData(result.data);
        });
    },

    sub: function(e) {
        let after_id = this.data.detail.data.after.id;
        let order_id = this.data.detail.data.after.order_id;
        wx.navigateTo({
            url: '/pages/member/after/edit?after_id=' + after_id + '&order_id=' + order_id
        })
    },


    delMember: function(e) {
        let id = e.currentTarget.dataset.id;
        let addMemberIds = this.data.addMemberIds;
        let addMember = this.data.addMember;

        // 
        addMember.splice(this.data.addMember.findIndex(item => item.id === id), 1);
        addMemberIds.splice(addMemberIds.findIndex(item => item === id), 1);
        this.setData({
            addMemberIds: this.data.addMemberIds,
            addMember: this.data.addMember
        });
    },



    addMember: function() {
        wx.navigateTo({
            url: '/pages/member/member/list?from=choose'
        })
    },

    subSend: function() {
        let _this = this;
        App.member_post('member.Order/sendAfter', {
            after_id: _this.data.id,            
            member_ids: _this.data.addMemberIds
        }, function(res) {
            if (res.code == 1) {
                wx.showModal({
                    title: '提示',
                    content: '操作成功',
                    showCancel: false,
                    success(res) {
                        if (res.confirm) {
                            wx.navigateTo({
                                url: '/pages/member/index'
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

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    }
})