// pages/member/order/sendDone.js

let header;
let App = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        pic_ids: [0, 0, 0],
        pic_paths: ['', '', '', ]
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        header = this.selectComponent("#header");
        this.data.id = options.id;
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
        header.loadData();
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
                        _this.data.pic_ids[index] = api_file_id;
                        _this.data.pic_paths[index] = tempFilePaths;

                        _this.setData({
                            pic_ids: _this.data.pic_ids,
                            pic_paths: _this.data.pic_paths
                        });
                    }
                })

            }
        });
    },

    sub: function(e) {
        console.log(e);
        let _this = this;
        let send_content = e.detail.value.send_content;
        let send_pic_ids = this.data.pic_ids;
        if (send_content == '') {
            App.showError('文字说明不能为空');
            return false;
        }
        if (send_pic_ids[0] == 0 && send_pic_ids[1] == 0 && send_pic_ids[2] == 0) {
            App.showError('图片不能为空');
            return false;
        }
        App.member_post('member.Order/sendDone', {
            id: _this.data.id,
            order_id: _this.data.order_id,
            send_content: send_content,
            send_pic_ids: send_pic_ids
        }, function(res) {
            if (res.code == 1 && res.data == 'success') {
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