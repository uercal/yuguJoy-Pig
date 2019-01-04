// pages/member/after/detail.js

let header;
let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        type_index: 1,
        check_type: [
            false, false, false, false
        ],
        equip_0: [],
        equip_1: [],
        equip_2: [],
        equip_3: [],
        checked_equip_ids: [],
        exchange_equip_ids: [],
        new_equip_ids: [],
        back_equip_ids: [],
        // 
        check_text: '',
        check_pics_ids: [0, 0, 0],
        check_pics_paths: ['', '', ''],
        //
        server_price: null,
        source_price: null
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#header");
        this.data.after_id = options.after_id;
        this.data.order_id = options.order_id;
    },

    type: function(e) {
        let index = e.currentTarget.dataset.index;
        this.setData({
            type_index: index
        })
    },
    source_price: function(e) {
        let v1 = e.detail.value;
        this.setData({
            source_price: v1
        })
    },
    server_price: function(e) {
        let v1 = e.detail.value;
        this.setData({
            server_price: v1
        })
    },
    checkType: function(e) {
        let type = e.currentTarget.dataset.type;
        let check_type = this.data.check_type;
        check_type[type] = !check_type[type];

        this.setData({
            check_type: check_type
        });
    },

    scan: function(e) {
        let type = e.currentTarget.dataset.type;
        let check_type = this.data.check_type;
        let _this = this;
        wx.showLoading({
            title: '读取中',
            mask: true
        });
        if (check_type[type]) {
            wx.scanCode({
                onlyFromCamera: true,
                scanType: [],
                success: function(res) {
                    let code = res.result;
                    App.member_post('member.Equip/decrypt', {
                        code: code
                    }, function(res) {
                        if (res.data.detail.order_id == _this.data.order_id || type == 2) {
                            _this.initScanData(res.data.detail, type);
                        } else {
                            App.showError('该设备不属于本售后单！');
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
        } else {

        }
    },


    // 格式化扫描数据
    initScanData: function(data, type) {
        console.log({
            data,
            type
        });


        if (!this.isExistEquip(data)) {
            App.showError('该设备已扫描');
        } else {
            if (type == 0) {
                this.data.checked_equip_ids.push(data.equip_id);
                this.data.equip_0.push(data);
                this.setData({
                    equip_0: this.data.equip_0
                });
            }
            // 
            if (type == 1) {
                this.data.exchange_equip_ids.push(data.equip_id);
                this.data.equip_1.push(data);
                this.setData({
                    equip_1: this.data.equip_1
                });
            }
            // 
            if (type == 2) {
                if (data.order_id == null) {
                    this.data.new_equip_ids.push(data.equip_id);
                    this.data.equip_2.push(data);
                    this.setData({
                        equip_2: this.data.equip_2
                    });
                } else {
                    App.showError('该设备已与其他订单绑定');
                }
            }
            // 
            if (type == 3) {
                this.data.back_equip_ids.push(data.equip_id);
                this.data.equip_3.push(data);
                this.setData({
                    equip_3: this.data.equip_3
                });
            }

        }

    },


    isExistEquip: function(data) {
        let equip_id = data.equip_id;
        if (this.data.checked_equip_ids.find(item => item === data.equip_id)) {
            App.showError('该设备已扫描');
            return false;
        }
        if (this.data.exchange_equip_ids.find(item => item === data.equip_id)) {
            App.showError('该设备已扫描');
            return false;
        }
        if (this.data.new_equip_ids.find(item => item === data.equip_id)) {
            App.showError('该设备已扫描');
            return false;
        }
        if (this.data.back_equip_ids.find(item => item === data.equip_id)) {
            App.showError('该设备已扫描');
            return false;
        }

        return true;
    },


    // 删除设备
    delEquip: function(e) {
        let equip_id = e.currentTarget.dataset.id;
        let type = e.currentTarget.dataset.type;
        let checked_equip_ids = this.data.checked_equip_ids;
        let exchange_equip_ids = this.data.exchange_equip_ids;
        let new_equip_ids = this.data.new_equip_ids;
        let back_equip_ids = this.data.back_equip_ids;

        if (type == 0) {
            this.data.equip_0.splice(this.data.equip_0.findIndex(item => item === equip_id), 1);
            checked_equip_ids.splice(checked_equip_ids.findIndex(item => item.equip_id === equip_id), 1);
            this.setData({
                equip_0: this.data.equip_0,
                checked_equip_ids
            });
        }

        if (type == 1) {
            this.data.equip_1.splice(this.data.equip_1.findIndex(item => item === equip_id), 1);
            exchange_equip_ids.splice(exchange_equip_ids.findIndex(item => item.equip_id === equip_id), 1);
            this.setData({
                equip_1: this.data.equip_1,
                exchange_equip_ids
            });
        }

        if (type == 2) {
            this.data.equip_2.splice(this.data.equip_2.findIndex(item => item === equip_id), 1);
            new_equip_ids.splice(new_equip_ids.findIndex(item => item.equip_id === equip_id), 1);
            this.setData({
                equip_2: this.data.equip_2,
                new_equip_ids
            });
        }

        if (type == 3) {
            this.data.equip_3.splice(this.data.equip_3.findIndex(item => item === equip_id), 1);
            back_equip_ids.splice(back_equip_ids.findIndex(item => item.equip_id === equip_id), 1);
            this.setData({
                equip_3: this.data.equip_3,
                back_equip_ids
            });
        }


    },


    // 
    uploadImg: function(e) {
        let index = e.currentTarget.dataset.index;
        let _this = this;
        wx.chooseImage({
            sizeType: ['original', 'compressed'],
            sourceType: ['album', 'camera'],
            success(res) {
                // tempFilePath可以作为img标签的src属性显示图片
                let tempFilePaths = res.tempFilePaths
                console.log(tempFilePaths);
                // 
                let wxapp_id = App.siteInfo.uniacid;
                wx.uploadFile({
                    url: App.api_root + 'upload/image', //仅为示例，非真实的接口地址
                    filePath: tempFilePaths[0],
                    name: 'file',
                    formData: {
                        wxapp_id: wxapp_id
                    },
                    success: function(res) {
                        let data = JSON.parse(res.data);
                        if (data.code == 0) {
                            App.showError(data.msg);
                            return false;
                        }
                        let api_file_id = data.api_file_id;
                        _this.data.check_pics_ids[index] = api_file_id;
                        _this.data.check_pics_paths[index] = tempFilePaths;

                        _this.setData({
                            check_pics_ids: _this.data.check_pics_ids,
                            check_pics_paths: _this.data.check_pics_paths
                        });
                    }
                })

            }
        });
    },


    // 
    sub: function() {
        // 
        this.validData();
    },


    validData: function() {
        let _this = this;
        let checked_equip_ids = this.data.checked_equip_ids;
        let exchange_equip_ids = this.data.exchange_equip_ids;
        let new_equip_ids = this.data.new_equip_ids;
        let back_equip_ids = this.data.back_equip_ids;
        let server_price = this.data.server_price;
        let source_price = this.data.source_price;
        //                 
        if (this.data.type_index == 2) {
            App.member_post('member.Order/afterDone', {
                type: _this.data.type_index,
                checked_equip_ids: checked_equip_ids,
                exchange_equip_ids: exchange_equip_ids,
                new_equip_ids: new_equip_ids,
                back_equip_ids: back_equip_ids,
                check_text: _this.data.check_text,
                check_pics_ids: _this.data.check_pics_ids,
                server_price: server_price,
                source_price: source_price,
                after_id: _this.data.after_id,
                order_id: _this.data.order_id
            }, function(res) {
                if (res.code == 1) {
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
            });

        } else {
            if (this.data.equip_3.length > 0) {
                // 返修
                if (this.data.check_text == '') {
                    App.showError('故障信息说明不能为空');
                    return false;
                }

                App.member_post('member.Order/afterDone', {
                    type: _this.data.type_index,
                    checked_equip_ids: checked_equip_ids,
                    exchange_equip_ids: exchange_equip_ids,
                    new_equip_ids: new_equip_ids,
                    back_equip_ids: back_equip_ids,
                    check_text: _this.data.check_text,
                    check_pics_ids: _this.data.check_pics_ids,
                    after_id: _this.data.after_id,
                    order_id: _this.data.order_id
                }, function(res) {
                    if (res.code == 1) {
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
                });

            } else {
                // 直接结算
                if (checked_equip_ids.length == 0 && exchange_equip_ids.length == 0 && new_equip_ids.length == 0) {
                    App.showError('维修设备不能为空');
                    return false;
                }
                if (server_price == null || !source_price == null) {
                    App.showError('售后费用不能为空');
                    return false;
                }

                if (!this.isNumber(server_price) || !this.isNumber(source_price)) {
                    App.showError('售后费用无效');
                    return false;
                }

                if (this.data.check_text == '') {
                    App.showError('故障信息说明不能为空');
                    return false;
                }


                App.member_post('member.Order/afterDone', {
                    type: _this.data.type_index,
                    checked_equip_ids: checked_equip_ids,
                    exchange_equip_ids: exchange_equip_ids,
                    new_equip_ids: new_equip_ids,
                    back_equip_ids: back_equip_ids,
                    check_text: _this.data.check_text,
                    check_pics_ids: _this.data.check_pics_ids,
                    server_price: server_price,
                    source_price: source_price,
                    after_id: _this.data.after_id,
                    order_id: _this.data.order_id
                }, function(res) {
                    if (res.code == 1) {
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
                });

            }
        }
    },

    isNumber: function(n) {
        let arr = n.split(".");
        if (arr.length > 2) {
            return false;
        } else {
            // 
            if (arr[0][0] == 0) {
                if (!isNaN(arr[0][1])) {
                    return false;
                } else {
                    return true;
                }
            }
            //
            if (arr.length == 2) {
                return arr[1] == '' ? false : true;
            } else {
                return true;
            }
        }
    },

    check_text: function(e) {
        this.setData({
            check_text: e.detail.value
        });
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
        // this.getDetail();
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