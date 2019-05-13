// pages/member/order/send.js

let header;
let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        isSend: false,
        addEquip: [],
        addEquipIds: [],
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
        // 

    },

    getDetail: function() {
        let id = this.data.id;
        let _this = this;
        App.member_get('member.order/detail', {
            id: id,
            type: 'order'
        }, function(result) {
            _this.setData(result.data);
        });
    },

    getSendDetail: function() {
        let id = this.data.id;
        let _this = this;
        App.member_get('member.order/sendOrderDetail', {
            id: id
        }, function(result) {
            _this.setData(result.data);
        });
    },



    moreEquip: function(e) {
        let index = e.currentTarget.dataset.index;
        let res = this.data.detail.equip[index].more;
        // let name = 'detail.equip[' + index + '].more';
        this.setData({
            ['detail.equip[' + index + '].more']: !res
        });
    },

    sub: function() {
        wx.navigateTo({
            url: '/pages/member/order/sendDone?id=' + this.data.id + '&order_id=' + this.data.detail.data.order_id
        })
    },

    subSend: function() {
        if (this.data.addMemberIds.length == 0) {
            App.showError('请添加配送人员');
            return false;
        }
        let _this = this;
        App.member_post('member.Order/delivery', {
            order_id: _this.data.id,
            equip: JSON.stringify(_this.data.addEquip),
            member_ids: _this.data.addMemberIds
        }, function(res) {
            if (res.code == 1) {
                wx.showModal({
                    title: '提示',
                    content: '操作成功',
                    showCancel: false,
                    success(res) {
                        if (res.confirm) {
                            wx.reLaunch({
                                url: '/pages/member/index'
                            })
                        }
                    }
                })
            }
        })
    },


    // send

    addEquip: function() {
        let _this = this;
        wx.showLoading({
            title: '读取中',
            mask: true
        });

        wx.scanCode({
            onlyFromCamera: true,
            scanType: [],
            success: function(res) {
                let code = res.result;
                App.member_post('member.Equip/decrypt', {
                    code: code
                }, function(res) {
                    if (!res.data.detail.order_id) {
                        _this.initScanData(res.data.detail);
                    } else {
                        App.showError('该设备非在库状态！');
                    }
                });
            },
            fail: function() {
                App.showError('扫描错误');
            },
            complete: function() {
                wx.hideLoading;
            }
        })

    },

    // 格式化扫描数据
    initScanData: function(data) {

        if (!this.isExistEquip(data)) {
            App.showError('该设备已扫描');
        } else {
            this.data.addEquip.push(data);
            this.data.addEquipIds.push(data.equip_id);
            this.setData({
                addEquip: this.data.addEquip,
                addEquipIds: this.data.addEquipIds
            })
        }
    },


    isExistEquip: function(data) {
        if (this.data.addEquipIds.find(item => item === data.equip_id)) {
            App.showError('该设备已扫描');
            return false;
        }
        return true;
    },


    // 删除设备
    delEquip: function(e) {
        let equip_id = e.currentTarget.dataset.id;
        let addEquipIds = this.data.addEquipIds;
        let addEquip = this.data.addEquip;

        // 
        addEquip.splice(this.data.addEquip.findIndex(item => item.equip_id === equip_id), 1);
        addEquipIds.splice(addEquipIds.findIndex(item => item === equip_id), 1);
        this.setData({
            addEquipIds: this.data.addEquipIds,
            addEquip: this.data.addEquip
        });
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

    secure: function(e) {
        let index = e.currentTarget.dataset.index;
        let value = e.currentTarget.dataset.value;
        let str = "addEquip[" + index + "].secure";
        this.setData({
            [str]: value
        });
    },

    service: function(e) {
        let pindex = e.currentTarget.dataset.pIndex;
        let index = e.currentTarget.dataset.index;
        let checked = this.data.addEquip[pindex].goods.service[index].checked;
        let str = "addEquip[" + pindex + "].goods.service[" + index + "].checked";
        this.setData({
            [str]: checked ? !checked : true
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

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function() {

    }
})