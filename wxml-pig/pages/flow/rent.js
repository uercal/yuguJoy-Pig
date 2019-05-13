let App = getApp();

// pages/flow/rent.js
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
            options: options
        })
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
        this.initGoodsRent();
        this.getRentDetail();
    },

    initGoodsRent: function() {
        let _this = this;
        App._get('goods/detail', {
            goods_id: _this.options.goods_id
        }, function(result) {
            _this.setData(result.data);
        });

    },
    // 租赁信息
    getRentDetail: function() {
        let _this = this;
        App._get('rent/getList', {
            goods_spec_id:_this.options.goods_spec_id
        }, function(res) {
            console.log(res);
            _this.setData({
                rent_list: res
            });
            _this.initData(res);
        });
    },

    initData: function(rent_list) {
        let rent_id = this.data.options.rent_id;
        let rent_index;
        let rent_info = rent_list.find(function(e, index) {
            if (e.id == rent_id) {
                rent_index = index;
                return e;
            }
        })

        if (rent_index == 0) this.data.rent_limit = 1;
        if (rent_index == 1) this.data.rent_limit = 5;
        if (rent_index == 2) this.data.rent_limit = 6;
        if (rent_index == 3) this.data.rent_limit = 24;

        this.setData({
            rent_info: rent_info,
            rent_index: rent_index,
            rent_num: this.data.options.rent_num,
            rent_limit: this.data.rent_limit,
            startDate: this.data.options.rent_date,
            start_date: new Date().Format("yyyy-MM-dd")
        });
    },

    chooseRent: function(e) {
        let index = e.currentTarget.dataset.index;
        if (index == 0) this.data.rent_num = 1, this.data.rent_limit = 1;
        if (index == 1) this.data.rent_num = 1, this.data.rent_limit = 5;
        if (index == 2) this.data.rent_num = 6, this.data.rent_limit = 6;
        if (index == 3) this.data.rent_num = 12, this.data.rent_limit = 24;
        this.setData({
            rent_index: index,
            rent_info: this.data.rent_list[index],
            rent_num: this.data.rent_num,
            rent_limit: this.data.rent_limit
        })
    },

    downRent: function() {
        if (this.data.rent_index == 1) {
            // limit 为上限
            if (this.data.rent_num <= this.data.rent_limit && this.data.rent_num > 1) {
                this.setData({
                    rent_num: --this.data.rent_num
                })
            }
        } else {
            // limit 为下限
            if (this.data.rent_num > this.data.rent_limit) {
                this.setData({
                    rent_num: --this.data.rent_num
                })
            }
        }

    },

    upRent: function() {
        if (this.data.rent_index == 1) {
            if (this.data.rent_num < this.data.rent_limit) {
                this.setData({
                    rent_num: ++this.data.rent_num
                })
            }
        } else {
            this.setData({
                rent_num: ++this.data.rent_num
            })
        }

    },

    chooseRentNum: function(e) {
        let num = e.currentTarget.dataset.num;
        this.setData({
            rent_num: num
        })
    },

    // 
    // 改变时间
    changeDate: function(e) {
        this.setData({
            startDate: e.detail.value
        })
    },

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
            ex_rent_id = this.data.rent_info.id,
            ex_rent_num = this.data.rent_num,
            ex_rent_date = this.data.startDate
        App._post_form('cart/exchangeRent', {
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
            ex_rent_id: ex_rent_id,
            ex_rent_num: ex_rent_num,
            ex_rent_date: ex_rent_date
        }, function(result) {
            wx.navigateBack();
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