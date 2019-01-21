let App = getApp();

Page({
    data: {
        searchColor: "rgba(0,0,0,0.4)",
        searchSize: "15",
        searchName: "请输入租赁商品的名称",
        filter_index: 0,
        curNav: true,
        curIndex: 0,
        filter1_name: '最热',
        filter1_value: 'hot',
        filter3_name: 'desc',
        list: [],
        choose_list: [],
        from_index: false
    },

    onLoad: function(options) {
        let _this = this;
        if (options.from == 'index') {
            this.setData({
                from_index: true
            })
        } else {
            // 获取分类列表
            this.getCategoryList();
        }

    },

    /**
     * 获取分类列表
     */
    getCategoryList: function() {
        let _this = this;
        App._get('category/lists', {}, function(result) {
            console.log(result);
            let pick_arr = [];
            result.data.list.map(function(e) {
                pick_arr.push(e.name);
            });
            console.log(pick_arr);
            _this.setData({
                pick_arr: pick_arr,
                list: result.data.list,
                choose_list: result.data.list[0],
                curNav: result.data.list[0].category_id
            });
        });
    },

    bindPickerChange: function(e) {
        let index = e.detail.value;
        let list = this.data.list[index];
        this.setData({
            choose_list: list
        });
    },

    chg_filter: function(e) {
        let index = e.currentTarget.dataset.index;
        let value = e.currentTarget.dataset.value;
        this.setData({
            filter_index: index
        });
        //  最新 最热
        if (index == 1) {
            // 最新    最热
            if (value == 'hot') {
                let child = this.data.choose_list.child;
                let new_child = child.sort(
                    (a, b) => {
                        return a.create_time < b.create_time
                    }
                );
                this.data.choose_list.child = new_child;
                this.setData({
                    filter1_value: 'new',
                    filter1_name: '最新',
                    choose_list: this.data.choose_list
                });
            } else {
                let child = this.data.choose_list.child;
                let new_child = child.sort(
                    (a, b) => {
                        return a.goods_sort > b.goods_sort
                    }
                );
                this.data.choose_list.child = new_child;
                this.setData({
                    filter1_value: 'hot',
                    filter1_name: '最热',
                    choose_list: this.data.choose_list
                })
            }
        }
        // 类别



        // 价格 
        if (index == 3) {
            let value = this.data.filter3_name == 'desc' ? 'asc' : 'desc';
            let child = this.data.choose_list.child;
            let new_child = child.sort(
                (a, b) => {
                    if (value == 'asc') {
                        return a.spec[0].goods_price < b.spec[0].goods_price
                    } else {
                        return a.spec[0].goods_price > b.spec[0].goods_price
                    }
                }
            );
            this.data.choose_list.child = new_child;
            this.setData({
                filter3_name: value,
                choose_list: this.data.choose_list
            })
        }
    },

    /**
     * 选中分类
     */
    selectNav: function(t) {
        let curNav = t.target.dataset.id,
            curIndex = parseInt(t.target.dataset.index);
        this.setData({
            curNav,
            curIndex,
            scrollTop: 0
        });
        console.log(this.data);
    },

    onPullDownRefresh: function() {
        wx.stopPullDownRefresh();
    },

    /**
     * 设置分享内容
     */
    onShareAppMessage: function() {
        return {
            title: "全部分类",
            desc: "",
            path: "/pages/category/index"
        };
    }

});