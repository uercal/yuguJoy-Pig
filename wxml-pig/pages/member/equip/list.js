let App = getApp();
let loadMoreView, page, header;
// pages/member/index.js
Page({

    /**
     * 页面的初始数据
     */
    data: {
        system: {},
        data: [],
        dataInfo: {},
        filter: {
            status: null,
            cate_id: null,
            start_time: null,
            end_time: null
        },
        filter_state: false,
        // 
        equip_status: [{
                name: '在库',
                value: 10,
                active: false
            },
            {
                name: '运送中',
                value: 20,
                active: false
            },
            {
                name: '使用中',
                value: 30,
                active: false
            },
            {
                name: '维修中',
                value: 40,
                active: false
            },
            {
                name: '停用',
                value: 50,
                active: false
            }
        ],
        cate_index: null,
        state_index: null,
        start_time: null,
        end_time: null
    },


    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function(options) {
        page = 1;
        loadMoreView = this.selectComponent("#loadMoreView");
        header = this.selectComponent("#memberHeader");
        this.loadOption();
        this.getList();
    },

    /**
     * 生命周期函数--监听页面初次渲染完成
     */
    onReady: function() {
        var that = this
        setTimeout(function() {
            // 延迟获取是为了解决真机上 windowHeight 值不对的问题
            wx.getSystemInfo({
                success: function(res) {
                    console.log(res)
                    that.setData({
                        system: res
                    })
                },
            })
        }, 500)
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function() {
        header.loadData();
    },

    getSearchContent: function(e) {
        let id = e.detail.value;
        App.member_get('member.Equip/serach', {
            equip_id: id
        }, function(res) {
            if (res.data) {
                wx.navigateTo({
                    url: '/pages/member/equip/detail?equip_id=' + id
                })
            } else {
                App.showError('设备不存在');
            }
        });
    },

    loadOption: function() {
        let _this = this;
        App.member_post('member.Equip/option', {}, function(res) {
            let data = res.data.cate_option;
            data.map(function(e) {
                e.active = false;
            });
            _this.setData({
                cate_option: data
            });
        });
    },


    equipDetail: function(e) {
        let equip_id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '/pages/member/equip/detail?equip_id=' + equip_id
        })
    },


    bindDateChange1: function(e) {
        let value = e.detail.value;
        this.setData({
            start_time: value
        });
    },

    bindDateChange2: function(e) {
        let value = e.detail.value;
        let _this = this;
        if (!this.data.start_time) {
            wx.showToast({
                title: '请先选择起始时间',
                icon: 'none',
                duration: 1500,
                mask: true
            })
        } else {
            if (this.data.start_time > value) {
                wx.showToast({
                    title: '不能小于起始时间',
                    icon: 'none',
                    duration: 1500,
                    mask: true
                })
            } else {
                this.setData({
                    end_time: value
                });
            }
        }
    },

    filter_state: function(e) {
        let index = e.currentTarget.dataset.index;

        this.setData({
            state_index: index
        });

        // 多选
        // let state = this.data.equip_status;
        // let target = "equip_status[" + index + "].active";
        // this.setData({
        //     [target]: !state[index].active
        // });
    },

    filter_cate: function(e) {
        let index = e.currentTarget.dataset.index;
        // 单选
        this.setData({
            cate_index: index
        });

        // 多选
        // let cate_option = this.data.cate_option;
        // let target = "cate_option[" + index + "].active";
        // this.setData({
        //     [target]: !cate_option[index].active
        // });
    },

    reset: function() {
        this.setData({
            cate_index: null,
            state_index: null,
            start_time: null,
            end_time: null
        });
    },


    filter_sub: function() {
        let cate_index = this.data.cate_index;
        let state_index = this.data.state_index;

        this.setData({
            filter_state: true,
            filter: {
                status: state_index !== null ? this.data.equip_status[state_index].value : null,
                cate_id: cate_index !== null ? this.data.cate_option[cate_index].category_id : null,
                start_time: this.data.start_time ? this.data.start_time : null,
                end_time: this.data.end_time ? this.data.end_time : null
            }
        })

        this.getList();
        this.hideModal();
    },

    getList: function() {
        let _this = this;
        let filter = JSON.stringify(this.data.filter);
        App.member_post('member.Equip/list', {
            page: page,
            filter: filter
        }, function(res) {
            _this.setData({
                dataInfo: res.data.list
            });
            if (page == 1) {
                _this.setData({
                    data: res.data.list.data
                });
            } else {
                let _data = _this.data.data;
                _data = _data.concat(res.data.list.data);
                _this.setData({
                    data: _data
                });
            }
            loadMoreView.loadMoreComplete(res.data.list);
        });
    },
    _onScrollToLower: function() {
        loadMoreView.loadMore()
    },

    loadMoreListener: function(e) {
        let _this = this;
        page += 1;
        _this.getList();
        // setTimeout(function() {
        //         _this.getNoticeList()
        // }, 1000);
    },

    clickLoadMore: function(e) {
        this.getList()
    },


    /**
     * 
     */
    //显示对话框
    showModal: function() {
        // 显示遮罩层
        var animation = wx.createAnimation({
            duration: 200,
            timingFunction: "linear",
            delay: 0
        })
        this.animation = animation
        animation.translateX(300).step()
        this.setData({
            animationData: animation.export(),
            showModalStatus: true
        });
        setTimeout(function() {
            animation.translateX(0).step()
            this.setData({
                animationData: animation.export()
            })
        }.bind(this), 200)
    },
    //隐藏对话框
    hideModal: function() {
        // 隐藏遮罩层
        var animation = wx.createAnimation({
            duration: 200,
            timingFunction: "linear",
            delay: 0
        })
        this.animation = animation
        animation.translateY(300).step()
        this.setData({
            animationData: animation.export(),
        })
        setTimeout(function() {
            animation.translateY(0).step()
            this.setData({
                animationData: animation.export(),
                showModalStatus: false
            })
        }.bind(this), 200)
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