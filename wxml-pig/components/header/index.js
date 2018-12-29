// components/load-more/index.js

let App = getApp();


Component({
    /**
     * 组件的属性列表
     */
    properties: {

    },

    /**
     * 组件的初始数据
     */
    data: {
        memberInfo: {}
    },

    /**
     * 组件的方法列表
     */
    methods: {
        exit: function(e) {
            console.log(e);
            wx.removeStorage({
                key: 'member_info'
            });
            wx.removeStorage({
                key: 'member_id'
            });
            wx.removeStorage({
                key: 'member_token',
                success: function() {
                    wx.navigateTo({
                        url: '/pages/member/login'
                    })
                },
            });
        },
        loadData: function() {
            let _this = this;
            App.member_get('member.index/detail', {}, function(result) {
                let _res = result;
                let member_info = wx.getStorage({
                    key: 'member_info',
                    success: function(res) {
                        console.log('get_member');
                        _this.setData({
                            memberInfo: res.data
                        });
                    },
                    fail: function(res) {
                        console.log('not member');
                        wx.setStorage({
                            key: 'member_info',
                            data: _res.data.memberInfo,
                        });
                        _this.setData({
                            memberInfo: _res.data.memberInfo
                        });
                    }
                })
                // if()
                // _this.setData(result.data);
            });
        },
        //加载更多的入口方法, 直接在page中使用时请在onReachBottom() 方法中调用这个方法, 并实现loadMoreListener方法去获取数据
        // loadMore: function() {
        //         if (!this.properties.hasMore) {
        //                 console.log('load more finish')
        //                 return
        //         }
        //         if (this.data.isLoading) {
        //                 console.log('loading ...')
        //                 return
        //         }
        //         this.setData({
        //                 isLoading: true
        //         })
        //         this.triggerEvent('loadMoreListener')
        // }
    }
})