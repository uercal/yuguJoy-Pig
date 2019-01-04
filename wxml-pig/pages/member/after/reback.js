// pages/member/after/detail.js

let header;
let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        temp_pics: [{
                file_path: '/images/member/upload.png'
            },
            {
                file_path: '/images/member/upload.png'
            },
            {
                file_path: '/images/member/upload.png'
            }
        ]
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#header");
        this.data.order_member_id = options.id;
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
                        _this.data.temp_pics[index]['file_path'] = tempFilePaths;

                        _this.setData({
                            check_pics_ids: _this.data.check_pics_ids,
                            temp_pics: _this.data.temp_pics
                        });
                    }
                })

            }
        });
    },


    // 
    sub: function() {
        let back = this.data.back_equip;
        //
        for (let i in back) {
            console.log(back[i]);
            if (back[i].status == 40) {
                App.showError('设备未修复，不能结单');
                return false;
            }
        }

        // 
        this.validData();
    },


    validData: function() {
        let _this = this;
        let back_equip_ids = this.data.back_equip_ids;
        let server_price = this.data.server_price;
        let source_price = this.data.source_price;
        //结算               
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

        App.member_post('member.Order/rebackDone', {
            back_equip_ids: back_equip_ids,
            check_text: _this.data.check_text,
            check_pics_ids: _this.data.check_pics_ids,
            server_price: server_price,
            source_price: source_price,
            after_id: _this.data.id,
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
        this.getDetail();
    },


    getDetail: function() {
        let order_member_id = this.data.order_member_id;
        let _this = this;
        App.member_get('member.Order/afterReback', {
            order_member_id: order_member_id
        }, function(res) {
            if (res.data.check_pics.length > 0) {
                _this.initTempPic(res.data.check_pics);
            }
            _this.setData(res.data);
        });
    },

    initTempPic: function(data) {
        let _this = this;
        data.map(function(e, index) {
            _this.data.temp_pics[index] = e;
        })
        this.setData({
            temp_pics: this.data.temp_pics
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