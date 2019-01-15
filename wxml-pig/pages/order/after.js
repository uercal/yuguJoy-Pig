let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        dataType: 'all',
        list: [],
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        this.data.dataType = options.type || 'rent';
        this.setData({
            dataType: this.data.dataType
        });
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
        if (this.data.dataType == 'rent') {
            // 获取订单列表
            this.getOrderList('doing');
        } else {
            this.getAfterList(this.data.dataType);
        }

    },

    /**
     * 获取订单列表
     */
    getOrderList: function(dataType) {
        let _this = this;
        App._get('user.order/lists', {
            dataType
        }, function(result) {
            _this.setData(result.data);
            result.data.list.length && wx.pageScrollTo({
                scrollTop: 0
            });
        });
    },

    /**
     * 获取售后订单
     */
    getAfterList: function(dataType) {
        let _this = this;
        App._get('user.order/afterList', {
            dataType
        }, function(result) {
            _this.setData(result.data);
            result.data.list.length && wx.pageScrollTo({
                scrollTop: 0
            });
        });
    },

    afterDetail: function(e) {
        let after_id = e.currentTarget.dataset.id;
        let status = e.currentTarget.dataset.status;
        let pay_status = e.currentTarget.dataset.payStatus;
        if (status == 40 || pay_status != 10) {
            wx.navigateTo({
                url: '/pages/order/afterDetail?after_id=' + after_id
            })
        }
    },

    /**
     * 切换标签
     */
    bindHeaderTap: function(e) {
        wx.showLoading();
        this.setData({
            dataType: e.target.dataset.type,
            list: []
        });
        // 获取订单列表
        if (this.data.dataType == 'rent') {
            // 获取订单列表
            this.getOrderList('doing');
        } else {
            this.getAfterList(e.target.dataset.type);
        }
        wx.hideLoading();
    },


    /**
     * 发起付款
     */
    payAfter: function(e) {
        let after_id = e.currentTarget.dataset.id;
        wx.redirectTo({
            url: '/pages/pay/index?from=after&id=' + after_id
        });
    },

    afterOrder: function(e) {
        let order_id = e.currentTarget.dataset.id;
        let after_status = e.currentTarget.dataset.afterStatus;
        if (after_status == 0) {
            wx.redirectTo({
                url: '../order/afterSend?order_id=' + order_id
            });
        } else {
            App.showError('当前订单有正在进行的售后');
        }
    },



    onPullDownRefresh: function() {
        wx.stopPullDownRefresh();
    }


});