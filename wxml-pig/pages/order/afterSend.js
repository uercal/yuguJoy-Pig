// pages/order/afterSend.js
let App = getApp();

Page({

        /**
         * 页面的初始数据
         */
        data: {
                request_pics_ids: [0, 0, 0],
                request_pics_paths: ['', '', '']
        },

        /**
         * 生命周期函数--监听页面加载
         */
        onLoad: function(options) {
                this.data.order_id = options.order_id;
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
                this.getOrderDetail();
        },


        getOrderDetail: function() {
                let _this = this;
                let order_id = this.data.order_id;
                App._get('user.order/detail', {
                        order_id: order_id
                }, function(result) {
                        _this.setData(result.data);
                });
        },


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
                                                _this.data.request_pics_ids[index] = api_file_id;
                                                _this.data.request_pics_paths[index] = tempFilePaths;

                                                _this.setData({
                                                        request_pics_ids: _this.data.request_pics_ids,
                                                        request_pics_paths: _this.data.request_pics_paths
                                                });
                                        }
                                })

                        }
                });
        },

        sub: function(e) {
                console.log(e);
                let _this = this;
                let request_text = e.detail.value.request_text;
                let request_pics_ids = this.data.request_pics_ids;
                if (request_text == '') {
                        App.showError('文字说明不能为空');
                        return false;
                }
                App._post_form('user.Order/after', {
                        order_id: _this.data.order_id,                        
                        request_text: request_text,
                        request_pics_ids: request_pics_ids
                }, function(res) {
                        if (res.code == 1) {
                                wx.showModal({
                                        title: '提示',
                                        content: '操作成功',
                                        showCancel: false,
                                        success(res) {
                                                if (res.confirm) {
                                                        wx.navigateTo({
                                                                url: '/pages/order/index'
                                                        })
                                                }
                                        }
                                })
                        } else {
                                App.showError('提交失败');
                        }
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