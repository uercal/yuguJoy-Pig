let App = getApp();


// pages/flow/service.js
Page({

        /**
         * 页面的初始数据
         */
        data: {

        },

        /**
         * 生命周期函数--监听页面加载
         */
        onLoad: function(options) {
                this.setData({
                        options: options,
                        secure: options.secure
                })
                console.log(options);
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
                // 获取该商品对应服务
                this.initGoodsService();
                // 渲染相应数据                
        },

        initGoodsService: function() {
                let _this = this;
                App._get('goods/detail', {
                        goods_id: _this.options.goods_id
                }, function(result) {
                        let goods_spec_id = _this.data.options.goods_spec_id;
                        // 获取当前保修金额
                        let obj = result.data.specData.spec_list.find(function(e) {
                                return e.goods_spec_id == goods_spec_id
                        })
                        let choose_service = _this.data.options.service_ids.split(',');
                        result.data.detail.service.map(function(e) {
                                for (let i in choose_service) {
                                        if (choose_service[i] == e.goods_service_id) {
                                                e.checked = true;
                                                break;
                                        }
                                }
                        })
                        _this.setData(result.data);
                        _this.setData({
                                ['options.secure_price']: obj.form.secure_price,
                                choose_service: choose_service
                        });
                });
        },

        secure: function(e) {
                let status = e.currentTarget.dataset.status;
                this.setData({
                        secure: status
                })
        },


        service_tap: function(e) {
                let index = e.currentTarget.dataset.index;
                let checked = this.data.detail.service[index].checked;
                let res = checked ? !checked : true;
                let str = "detail.service[" + index + "].checked";
                //显示                
                this.setData({
                        [str]: res
                })

                let choose_service = [];
                this.data.detail.service.map(function(e) {
                        if (e.checked) {
                                choose_service.push(e.goods_service_id);
                        }
                })

                choose_service = choose_service.sort(function(a, b) {
                        return a - b;
                })

                this.setData({
                        choose_service: choose_service
                })

        },


        // 
        exchange: function() {
                let _this = this,
                        goods_id = this.data.options.goods_id,
                        goods_spec_id = this.data.options.goods_spec_id,
                        total_num = this.data.options.total_num,
                        rent_id = this.data.options.rent_id,
                        rent_num = this.data.options.rent_num,
                        rent_date = this.data.options.rent_date,
                        secure = this.data.options.secure,
                        service_ids = this.data.options.service_ids,
                        ex_secure = this.data.secure,
                        ex_service_ids = this.data.choose_service.join(',');
                App._post_form('cart/exchange', {
                        goods_id: goods_id,
                        goods_spec_id: goods_spec_id,
                        total_num: total_num,
                        // 租赁信息
                        rent_id: rent_id,
                        rent_num: rent_num,
                        rent_date: rent_date,
                        // 保险
                        secure: secure,
                        //增值服务
                        service_ids: service_ids,
                        // exchange
                        ex_secure: ex_secure,                       
                        ex_service_ids: ex_service_ids
                }, function(result) {
                        wx.navigateBack();
                });


        },


        /**
         * 
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