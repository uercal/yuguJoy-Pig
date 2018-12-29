let App = getApp(),
        wxParse = require("../../wxParse/wxParse.js");

Page({

        /**
         * 页面的初始数据
         */
        data: {
                nav_select: false, // 快捷导航

                indicatorDots: true, // 是否显示面板指示点
                autoplay: true, // 是否自动切换
                interval: 3000, // 自动切换时间间隔
                duration: 800, // 滑动动画时长

                currentIndex: 1, // 轮播图指针
                floorstatus: false, // 返回顶部
                showView: true, // 显示商品规格

                detail: {}, // 商品详情信息
                goods_price: 0, // 商品价格
                // line_price: 0, // 划线价格
                stock_num: 0, // 库存数量

                goods_num: 1, // 商品数量
                goods_sku_id: 0, // 规格id
                cart_total_num: 0, // 购物车商品总数量
                specData: {}, // 多规格信息

                is_favorite: false,
                secure_status: 0,
                rent_index: 3,

                // 租赁时间参数
                rent_num: 1,
                rent_limit: 0,

                startDate: "请选择时间"
        },

        // 记录规格的数组
        goods_spec_arr: [],

        /**
         * 生命周期函数--监听页面加载
         */
        onLoad: function(options) {
                let _this = this;
                // 商品id
                _this.data.goods_id = options.goods_id;
                // 获取商品信息
                _this.getGoodsDetail();
                // 获取租赁模式
                _this.getRentDetail();
        },
        onShow: function() {                
                this.setData({                       
                        start_date: new Date().Format("yyyy-MM-dd"),
                        startDate: new Date().Format("yyyy-MM-dd")
                });
        },
        // 是否包含
        is_contains: function(a, b) {
                for (let item in a) {
                        if (a[item] == b) {
                                return true;
                        }
                }
                return false;
        },
        /**
         * 获取商品信息
         */
        getGoodsDetail: function() {
                let _this = this;
                App._get('goods/detail', {
                        goods_id: _this.data.goods_id
                }, function(result) {
                        // 初始化商品详情数据
                        let data = _this.initGoodsDetailData(result.data);
                        _this.setData(data);
                });
        },

        /**
         * 初始化商品详情数据
         */
        initGoodsDetailData: function(data) {
                let _this = this;
                // 富文本转码
                if (data.detail.content.length > 0) {
                        wxParse.wxParse('content', 'html', data.detail.content, _this, 0);
                }
                // 商品价格/划线价/库存
                data.goods_sku_id = data.detail.spec[0].spec_sku_id;
                data.goods_price = data.detail.spec[0].goods_price;
                data.goods_spec_id = data.detail.spec[0].goods_spec_id
                // data.line_price = data.detail.spec[0].line_price;
                data.stock_num = data.detail.spec[0].stock_num;
                // 初始化商品多规格
                if (data.detail.spec_type === 20) {
                        data.specData = _this.initManySpecData(data.specData);
                }
                data.service = data.detail.service;

                return data;
        },

        /**
         * 初始化商品多规格
         */
        initManySpecData: function(data) {
                for (let i in data.spec_attr) {
                        for (let j in data.spec_attr[i].spec_items) {
                                if (j < 1) {
                                        data.spec_attr[i].spec_items[0].checked = true;
                                        this.goods_spec_arr[i] = data.spec_attr[i].spec_items[0].item_id;
                                        this.setData({
                                                secure_price: data.spec_list[i].form.secure_price,
                                                choose_goods_value: data.spec_attr[i].spec_items[0].spec_value
                                        })
                                }
                        }
                }
                return data;
        },
        /**
         * 获取租赁模式列表
         */
        getRentDetail: function() {
                let _this = this;
                App._get('rent/getList', {}, function(res) {
                        _this.setData({
                                // rent_list: res,
                                // rentInfo: res[0],
                                // rent_limit: 1
                                rent_list: res,
                                rentInfo: res[3],
                                rent_limit: 24,
                                rent_num: 12
                        });
                        _this.rentPrice(12, res[3]);
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
                        rentInfo: this.data.rent_list[index],
                        rent_num: this.data.rent_num,
                        rent_limit: this.data.rent_limit
                })
                this.rentPrice(this.data.rent_num, this.data.rent_list[index]);
        },

        rentPrice: function(rent_num, rent_info) {
                let map = rent_info.map;
                let price = 0;
                if (!map) {
                        price = 18;
                } else {
                        map.map(function(e) {
                                if (!e.max) {
                                        price = 90;
                                } else {
                                        if (rent_num <= e.max && rent_num >= e.min) {
                                                price = e.price;
                                        }
                                }
                        })
                }
                this.setData({
                        rentPrice: price
                });
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
                this.rentPrice(this.data.rent_num, this.data.rentInfo);
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
                this.rentPrice(this.data.rent_num, this.data.rentInfo);
        },

        chooseRentNum: function(e) {
                let num = e.currentTarget.dataset.num;
                this.setData({
                        rent_num: num
                })
        },

        secure: function(e) {
                let status = e.currentTarget.dataset.status;
                this.setData({
                        secure_status: status
                })
        },

        /**
         * 点击切换不同规格
         */
        modelTap: function(e) {
                let attrIdx = e.currentTarget.dataset.attrIdx,
                        itemIdx = e.currentTarget.dataset.itemIdx,
                        specData = this.data.specData;
                for (let i in specData.spec_attr) {
                        for (let j in specData.spec_attr[i].spec_items) {
                                if (attrIdx == i) {
                                        specData.spec_attr[i].spec_items[j].checked = false;
                                        if (itemIdx == j) {
                                                specData.spec_attr[i].spec_items[itemIdx].checked = true;
                                                this.goods_spec_arr[i] = specData.spec_attr[i].spec_items[itemIdx].item_id;
                                                // 更新保修金
                                                this.setData({
                                                        secure_price: specData.spec_list[i].form.secure_price,
                                                        choose_goods_value: specData.spec_attr[i].spec_items[itemIdx].spec_value
                                                })
                                        }
                                }
                        }
                }

                this.setData({
                        specData
                });
                // 更新商品规格信息
                this.updateSpecGoods();
        },

        /**
         * 更新商品规格信息
         */
        updateSpecGoods: function() {
                let spec_sku_id = this.goods_spec_arr.join('_');

                // 查找skuItem
                let spec_list = this.data.specData.spec_list,
                        skuItem = spec_list.find((val) => {
                                return val.spec_sku_id == spec_sku_id;
                        });

                // 记录goods_sku_id
                // 更新商品价格、划线价、库存
                if (typeof skuItem === 'object') {
                        this.setData({
                                goods_sku_id: skuItem.spec_sku_id,
                                goods_price: skuItem.form.goods_price,
                                goods_spec_id: skuItem.goods_spec_id,
                                // line_price: skuItem.form.line_price,
                                stock_num: skuItem.form.stock_num,
                        });
                }
        },
        /**
         * 收藏
         */
        do_favorite: function() {
                let _this = this;
                let goods_id = _this.data.goods_id;
                let favorite_goods = wx.getStorageSync('favorite_goods');
                if (_this.data.is_favorite) {
                        // 已收藏                        
                        let index = favorite_goods.indexOf(String(goods_id));
                        favorite_goods.splice(index, 1);
                        wx.setStorageSync('favorite_goods', favorite_goods);
                        _this.setData({
                                is_favorite: false
                        });
                } else {
                        // 未收藏
                        if (!favorite_goods) {
                                wx.setStorageSync('favorite_goods', [goods_id]);
                        } else {
                                favorite_goods.push(goods_id);
                                wx.setStorageSync('favorite_goods', favorite_goods);
                        }
                        _this.setData({
                                is_favorite: true
                        });
                        wx.showToast({
                                title: '收藏成功',
                                icon: 'succes',
                                duration: 1000,
                                mask: true
                        });
                }
                _this.post_favorite();
                _this.is_favorite = !_this.is_favorite;
        },

        // 收藏post
        post_favorite: function() {
                App._post_form('user/doFavorite', {
                        user_id: wx.getStorageSync('user_id'),
                        favorite_goods_ids: wx.getStorageSync('favorite_goods')
                }, function(result) {
                        // success                        
                }, function(result) {
                        // fail
                }, function() {

                })
        },


        goHome: function() {
                wx.switchTab({
                        url: '/pages/index/index',
                })
        },


        /**
         * 设置轮播图当前指针 数字
         */
        setCurrent: function(e) {
                this.setData({
                        currentIndex: e.detail.current + 1
                });
        },

        /**
         * 控制商品规格/数量的显示隐藏
         */
        onChangeShowState: function() {
                this.setData({
                        showView: !this.data.showView
                });
        },

        /**
         * 返回顶部
         */
        goTop: function(t) {
                this.setData({
                        scrollTop: 0
                });
        },

        /**
         * 显示/隐藏 返回顶部按钮
         */
        scroll: function(e) {
                this.setData({
                        floorstatus: e.detail.scrollTop > 200
                })
        },


        // 改变时间
        changeDate: function(e) {
                this.setData({
                        startDate: e.detail.value
                })
        },



        /**
         * 增加商品数量
         */
        up: function() {
                this.setData({
                        goods_num: ++this.data.goods_num
                })
        },

        /**
         * 减少商品数量
         */
        down: function() {
                if (this.data.goods_num > 1) {
                        this.setData({
                                goods_num: --this.data.goods_num
                        });
                }
        },

        /**
         * 跳转购物车页面
         */
        flowCart: function() {
                wx.switchTab({
                        url: "../flow/index"
                });
        },

        /**
         * 快捷导航 显示/隐藏
         */
        commonNav: function() {
                this.setData({
                        nav_select: !this.data.nav_select
                });
        },

        nav: function(e) {
                let index = e.currentTarget.dataset.index;
                "home" == index ? wx.switchTab({
                        url: "../index/index"
                }) : "fenlei" == index ? wx.switchTab({
                        url: "../category/index"
                }) : "cart" == index ? wx.switchTab({
                        url: "../flow/index"
                }) : "profile" == index && wx.switchTab({
                        url: "../user/index"
                });
        },

        /**
         * 加入购物车and立即购买
         */
        submit: function(e) {
                let _this = this,
                        submitType = e.currentTarget.dataset.type,
                        choose_service = this.data.choose_service ? this.data.choose_service.join(',') : 0;
                if (this.data.startDate == "请选择时间") {
                        wx.showToast({
                                title: '请选择租赁时间',
                                icon: 'none',
                                duration: 1500,
                                mask: true
                        })
                        return false;
                }



                if (submitType === 'buyNow') {
                        // 立即购买
                        wx.navigateTo({
                                url: '../flow/checkout?' + App.urlEncode({
                                        order_type: 'buyNow',
                                        goods_id: _this.data.goods_id,
                                        goods_num: _this.data.goods_num,
                                        goods_sku_id: _this.data.goods_sku_id,
                                        // 租赁信息
                                        rent_id: _this.data.rentInfo.id,
                                        rent_num: _this.data.rent_num,
                                        rent_date: _this.data.start_date,
                                        // 保险
                                        secure: _this.data.secure_status,
                                        secure_price: _this.data.secure_price,
                                        //增值服务
                                        service_ids: choose_service
                                })
                        });
                } else if (submitType === 'addCart') {
                        // 加入购物车
                        App._post_form('cart/add', {
                                goods_id: _this.data.goods_id,
                                goods_num: _this.data.goods_num,
                                goods_spec_id: _this.data.goods_spec_id,
                                // 租赁信息
                                rent_id: _this.data.rentInfo.id,
                                rent_num: _this.data.rent_num,
                                rent_date: _this.data.start_date,
                                // 保险
                                secure: _this.data.secure_status,
                                secure_price: _this.data.secure_price,
                                //增值服务
                                service_ids: choose_service
                        }, function(result) {
                                App.showSuccess(result.msg);
                                _this.setData(result.data);
                        });
                }
        },

        showPick: function(e) {
                let _type = e.currentTarget.dataset.type;
                this.showModal(_type);
        },

        //显示对话框
        showModal: function(_type) {
                // 显示遮罩层
                var animation = wx.createAnimation({
                        duration: 200,
                        timingFunction: "linear",
                        delay: 0
                })
                this.animation = animation
                animation.translateY(300).step()
                this.setData({
                        animationData: animation.export(),
                        showModalStatus: _type
                });
                setTimeout(function() {
                        animation.translateY(0).step()
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
        service_tap: function(e) {
                let index = e.currentTarget.dataset.index;
                let checked = this.data.service[index].checked;
                let res = checked ? !checked : true;
                let str = "service[" + index + "].checked";
                //                
                this.setData({
                        [str]: res
                })

                let choose_service = [];
                this.data.service.map(function(e) {
                        if (e.checked) {
                                choose_service.push(e.goods_service_id);
                        }
                })
                // choose_service 排序
                choose_service = choose_service.sort(function(a, b) {
                        return a - b;
                })
                // 
                this.setData({
                        choose_service: choose_service
                })

        }

})