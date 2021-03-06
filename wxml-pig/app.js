App({

    /**
     * 全局变量
     */
    globalData: {
        user_id: null,
        category_id: null
    },

    api_root: '', // api地址
    siteInfo: require('siteinfo.js'),

    /**
     * 生命周期函数--监听小程序初始化
     */
    onLaunch: function() {
        // 设置api地址
        this.setApiRoot();
    },

    /**
     * 当小程序启动，或从后台进入前台显示，会触发 onShow
     */
    onShow: function(options) {
        // 获取小程序基础信息
        this.getWxappBase(function(wxapp) {
            // 设置navbar标题、颜色
            wx.setNavigationBarColor({
                frontColor: wxapp.navbar.top_text_color.text,
                backgroundColor: wxapp.navbar.top_background_color
            })
        });

        // 
        Date.prototype.Format = function(fmt) { //author: meizz 
            var o = {
                "M+": this.getMonth() + 1, //月份 
                "d+": this.getDate(), //日 
                "h+": this.getHours(), //小时 
                "m+": this.getMinutes(), //分 
                "s+": this.getSeconds(), //秒 
                "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
                "S": this.getMilliseconds() //毫秒 
            };
            if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o)
                if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            return fmt;
        }
    },

    /**
     * 设置api地址
     */
    setApiRoot: function() {
        this.api_root = this.siteInfo.siteroot + 'index.php?s=/api/';
    },

    /**
     * 获取小程序基础信息
     */
    getWxappBase: function(callback) {
        let App = this;
        App._get('wxapp/base', {}, function(result) {
            // 记录小程序基础信息
            wx.setStorageSync('wxapp', result.data.wxapp);
            callback && callback(result.data.wxapp);
        }, false, false);
    },

    /**
     * 执行用户登录
     */
    doLogin: function() {
        // 保存当前页面
        let pages = getCurrentPages();
        if (pages.length) {
            let currentPage = pages[pages.length - 1];
            "pages/login/login" != currentPage.route &&
                wx.setStorageSync("currentPage", currentPage);
        }
        // 跳转授权页面
        wx.navigateTo({
            url: "/pages/login/login"
        });
    },

    /**
     * 当前用户id
     */
    getUserId: function() {
        return wx.getStorageSync('user_id');
    },

    /**
     * 显示成功提示框
     */
    showSuccess: function(msg, callback) {
        wx.showToast({
            title: msg,
            icon: 'success',
            success: function() {
                callback && (setTimeout(function() {
                    callback();
                }, 1500));
            }
        });
    },

    /**
     * 显示失败提示框
     */
    showError: function(msg, callback) {
        wx.showModal({
            title: '友情提示',
            content: msg,
            showCancel: false,
            success: function(res) {
                // callback && (setTimeout(function() {
                //   callback();
                // }, 1500));
                callback && callback();
            }
        });
        wx.hideLoading();
    },

    /**
     * get请求
     */
    _get: function(url, data, success, fail, complete, check_login) {
        wx.showNavigationBarLoading();
        let App = this;
        // 构造请求参数
        data = data || {};
        data.wxapp_id = App.siteInfo.uniacid;

        // if (typeof check_login === 'undefined')
        //   check_login = true;

        // 构造get请求
        let request = function() {
            data.token = wx.getStorageSync('token');
            wx.request({
                url: App.api_root + url,
                header: {
                    'content-type': 'application/json'
                },
                data: data,
                success: function(res) {
                    if (res.statusCode !== 200 || typeof res.data !== 'object') {
                        console.log(res);
                        App.showError('网络请求出错');
                        return false;
                    }
                    if (res.data.code === -1) {
                        // 登录态失效, 重新登录
                        wx.hideNavigationBarLoading();
                        App.doLogin();
                    } else if (res.data.code === 0) {
                        App.showError(res.data.msg);
                        return false;
                    } else {
                        success && success(res.data);
                    }
                },
                fail: function(res) {
                    // console.log(res);
                    App.showError(res.errMsg, function() {
                        fail && fail(res);
                    });
                },
                complete: function(res) {
                    wx.hideNavigationBarLoading();
                    complete && complete(res);
                },
            });
        };
        // 判断是否需要验证登录
        check_login ? App.doLogin(request) : request();
    },

    /**
     * post提交
     */
    _post_form: function(url, data, success, fail, complete) {
        wx.showNavigationBarLoading();
        let App = this;
        data.wxapp_id = App.siteInfo.uniacid;
        data.token = wx.getStorageSync('token');
        wx.request({
            url: App.api_root + url,
            header: {
                'content-type': 'application/x-www-form-urlencoded',
            },
            method: 'POST',
            data: data,
            success: function(res) {
                if (res.statusCode !== 200 || typeof res.data !== 'object') {
                    App.showError('网络请求出错');
                    return false;
                }
                if (res.data.code === -1) {
                    // 登录态失效, 重新登录
                    App.doLogin(function() {
                        App._post_form(url, data, success, fail);
                    });
                    return false;
                } else if (res.data.code === 0) {
                    App.showError(res.data.msg, function() {
                        fail && fail(res);
                    });
                    return false;
                }
                success && success(res.data);
            },
            fail: function(res) {
                // console.log(res);
                App.showError(res.errMsg, function() {
                    fail && fail(res);
                });
            },
            complete: function(res) {
                wx.hideLoading();
                wx.hideNavigationBarLoading();
                complete && complete(res);
            }
        });
    },



    /**
     * 封装员工端 请求 GET
     */
    member_get: function(url, data, success, fail, complete, check_login) {
        wx.showNavigationBarLoading();
        let App = this;
        // 构造请求参数
        data = data || {};
        data.wxapp_id = App.siteInfo.uniacid;

        // 构造get请求
        let request = function() {
            data.member_token = wx.getStorageSync('member_token');
            wx.request({
                url: App.api_root + url,
                header: {
                    'content-type': 'application/json'
                },
                data: data,
                success: function(res) {
                    if (res.statusCode !== 200 || typeof res.data !== 'object') {
                        console.log(res);
                        App.showError('网络请求出错');
                        return false;
                    }
                    if (res.data.code === -1) {
                        // 登录态失效, 重新登录
                        wx.hideNavigationBarLoading();
                        App.doMemberLogin();
                    } else if (res.data.code === 0) {
                        App.showError(res.data.msg);
                        return false;
                    } else {
                        success && success(res.data);
                    }
                },
                fail: function(res) {
                    // console.log(res);
                    App.showError(res.errMsg, function() {
                        fail && fail(res);
                    });
                },
                complete: function(res) {
                    wx.hideNavigationBarLoading();
                    complete && complete(res);
                },
            });
        };
        // 判断是否需要验证登录
        check_login ? App.doMemberLogin(request) : request();
    },

    /**
     * 封装员工端 请求POST         
     */
    member_post: function(url, data, success, fail, complete) {
        wx.showNavigationBarLoading();
        let App = this;
        data.wxapp_id = App.siteInfo.uniacid;
        data.member_token = wx.getStorageSync('member_token');
        wx.request({
            url: App.api_root + url,
            header: {
                'content-type': 'application/x-www-form-urlencoded',
            },
            method: 'POST',
            data: data,
            success: function(res) {
                if (res.statusCode !== 200 || typeof res.data !== 'object') {
                    App.showError('网络请求出错');
                    return false;
                }
                if (res.data.code === -1) {
                    // 登录态失效, 重新登录
                    App.doMemberLogin(function() {
                        App._post_form(url, data, success, fail);
                    });
                    return false;
                } else if (res.data.code === 0) {
                    App.showError(res.data.msg, function() {
                        fail && fail(res);
                    });
                    return false;
                }
                success && success(res.data);
            },
            fail: function(res) {
                // console.log(res);
                App.showError(res.errMsg, function() {
                    fail && fail(res);
                });
            },
            complete: function(res) {
                wx.hideLoading();
                wx.hideNavigationBarLoading();
                complete && complete(res);
            }
        });
    },

    doMemberLogin: function() {
        // 保存当前页面
        let pages = getCurrentPages();
        if (pages.length) {
            let currentPage = pages[pages.length - 1];
            "pages/member/login" != currentPage.route &&
                wx.setStorageSync("currentPage", currentPage);
        }
        // 跳转授权页面
        wx.redirectTo({
            url: "/pages/member/login"
        });
    },


    /**
     * 验证是否存在user_info
     */
    validateUserInfo: function() {
        let user_info = wx.getStorageSync('user_info');
        return !!wx.getStorageSync('user_info');
    },

    /**
     * 对象转URL
     */
    urlEncode: function urlencode(data) {
        var _result = [];
        for (var key in data) {
            var value = data[key];
            if (value.constructor == Array) {
                value.forEach(function(_value) {
                    _result.push(key + "=" + _value);
                });
            } else {
                _result.push(key + '=' + value);
            }
        }
        return _result.join('&');
    },

    /**
     * 设置当前页面标题
     */
    setTitle: function() {
        let App = this,
            wxapp;
        if (wxapp = wx.getStorageSync('wxapp')) {
            wx.setNavigationBarTitle({
                title: wxapp.navbar.wxapp_title
            });
        } else {
            App.getWxappBase(function() {
                App.setTitle();
            });
        }
    },

});