let App = getApp();
let header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        system: {},
        isCheck: false
    },


    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#memberHeader");
        this.data.equip_id = options.equip_id;
        this.getDetail(options.equip_id);
        if (options.from == 'scan') {
            this.getCheckPermission();
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

    getCheckPermission: function() {
        let _this = this;
        App.member_get('member.Equip/checkPermission', {}, function(res) {
            if (res.data.check == 1) {
                _this.setData({
                    isCheck: true
                })
            }
        })
    },

    go2order: function(e) {
        console.log(e);
        let order_id = e.currentTarget.dataset.orderId;
        wx.navigateTo({
            url: '/pages/member/order/detail?order_id=' + order_id
        });
    },


    changeEquip: function() {
        let status = this.data.detail.status;
        let equip_id = this.data.detail.equip_id;
        if (status == 10 || status == 50) {
            wx.showModal({
                title: '状态变更',
                content: status == 10 ? '设备状态将变更为停用' : '设备状态将变更为在库',
                showCancel: true,
                cancelText: '取消',
                confirmText: '确认',
                success: function(res) {
                    if (res.confirm) {
                        console.log('用户点击确定')
                        App.member_post('member.Equip/changeStatus', {
                            equip_id: equip_id
                        }, function(r) {
                            if (r.code == 1) {
                                wx.showModal({
                                    content: '成功',
                                    showCancel: false,
                                    success: function(res) {
                                        wx.reLaunch({
                                            url: '/pages/member/index'
                                        })
                                    }
                                })
                            }
                        })
                    } else if (res.cancel) {
                        console.log('用户点击取消')
                    }
                }
            })
        } else {
            App.showError('该设备状态无法变更');
        }
    },

    checkEquip: function() {
        let _this = this;
        let status = this.data.detail.status;
        if (status != 10 && status != 40) {
            App.showError('该状态无法进行维修');
            return false;
        }
        let equip_id = this.data.detail.equip_id;
        // 查看是否维修进行中
        App.member_get('member.Equip/isChecking', {
            equip_id: equip_id
        }, function(res) {
            if (res.data == false) {
                // 非进行中
                console.log('un');
                _this.startCheck(equip_id);
            } else {
                // 进行中
                console.log('ing');
                _this.continueCheck(equip_id);
            }
        })
    },

    startCheck: function(equip_id) {
        wx.showModal({
            title: '确认',
            content: '是否开始维修设备',
            showCancel: true,
            cancelText: '取消',
            confirmText: '开始',
            success: function(res) {
                if (res.confirm) {
                    App.member_post('member.Equip/startCheck', {
                        equip_id: equip_id
                    }, function(r) {
                        if (r.code == 1) {
                            wx.redirectTo({
                                url: '/pages/member/equip/checking?equip_id=' + equip_id
                            })
                        }
                    })
                }
            }
        })
    },

    continueCheck: function(equip_id) {
        wx.redirectTo({
            url: '/pages/member/equip/checking?equip_id=' + equip_id
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