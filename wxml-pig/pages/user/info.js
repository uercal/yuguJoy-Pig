let App = getApp();

Page({

        /**
         * 页面的初始数据
         */
        data: {
                disabled: true,
                nav_select: false, // 快捷导航
                region: '',
                name: '',
                company: "",
                error: '',
                org_code: false,
                org_code_path: "",
                license: false,
                license_path: "",
                idcard_p: false,
                idcard_n: false,
                idcard_p_path: "",
                idcard_n_path: "",
                org_code_id: "",
                license_id: "",
                idcard_ids: [0, 0],
                other_content: "",
                other_ids: [0, 0, 0, 0, 0, 0],
                other_paths: ['', '', '', '', '', '']
        },

        /**
         * 生命周期函数--监听页面加载
         */
        onLoad: function(options) {

        },
        name: function(e) {
                let value = e.detail.value;
                this.setData({
                        name: value
                });
                if (this.validation(this.data)) {
                        this.setData({
                                disabled: false
                        });
                }
        },
        company: function(e) {
                let value = e.detail.value;
                this.setData({
                        company: value
                });
                if (this.validation(this.data)) {
                        this.setData({
                                disabled: false
                        });
                }
        },
        other_content: function(e) {
                let value = e.detail.value;
                this.setData({
                        other_content: value
                });
                if (this.validation(this.data)) {
                        this.setData({
                                disabled: false
                        });
                }
        },
        /**
         * 表单提交
         */
        saveData: function(e) {
                let _this = this,
                        values = e.detail.value;

                // 表单验证
                if (!_this.validation(values)) {
                        App.showError(_this.data.error);
                        return false;
                }

                // 按钮禁用
                _this.setData({
                        disabled: true
                });

                // 
                values.other_ids = _this.data.other_ids;
                values.license_id = _this.data.license_id;
                values.idcard_ids = _this.data.idcard_ids;

                // 个人认证
                values.type = 10;


                // 提交到后端
                App._post_form('exam/userInfoExam', values, function(result) {
                        App.showSuccess('提交成功', function() {
                                wx.navigateBack();
                        });
                }, false, function() {
                        // 解除禁用
                        _this.setData({
                                disabled: false
                        });
                });
        },

        uploadImg: function(e) {
                let u_type = e.currentTarget.dataset.type;
                let _this = this;
                let count = 1;
                wx.chooseImage({
                        count: count,
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
                                                switch (u_type) {
                                                        case "other_ids":
                                                                let index;
                                                                _this.data.other_ids.find(function(e, _index) {
                                                                        if (e == 0) {
                                                                                index = _index;
                                                                                return e == 0;
                                                                        }
                                                                })
                                                                console.log(index);
                                                                _this.data.other_ids[index] = api_file_id;
                                                                _this.data.other_paths[index] = tempFilePaths;

                                                                _this.setData({
                                                                        other_ids: _this.data.other_ids,
                                                                        other_paths: _this.data.other_paths
                                                                });
                                                                break;
                                                        case "license":
                                                                _this.setData({
                                                                        license_path: tempFilePaths,
                                                                        license: true,
                                                                        license_id: api_file_id
                                                                })
                                                                break;
                                                        case "idcard_n":
                                                                _this.data.idcard_ids[0] = api_file_id;
                                                                _this.setData({
                                                                        idcard_n_path: tempFilePaths,
                                                                        idcard_n: true,
                                                                        idcard_ids: _this.data.idcard_ids
                                                                })
                                                                break;
                                                        case "idcard_p":
                                                                _this.data.idcard_ids[1] = api_file_id;
                                                                _this.setData({
                                                                        idcard_p_path: tempFilePaths,
                                                                        idcard_p: true,
                                                                        idcard_ids: _this.data.idcard_ids
                                                                })
                                                                break;
                                                }
                                                // 
                                                console.log(_this.validation(_this.data));
                                                if (_this.validation(_this.data)) {
                                                        _this.setData({
                                                                disabled: false
                                                        });
                                                }
                                        }
                                })

                        }
                });

        },

        /**
         * 表单验证
         */
        validation: function(values) {
                if (values.name === '' && values.company === "" && this.data.license_id == "" && this.data.other_ids[0] == 0 && this.data.other_content == "" &&
                        this.data.idcard_ids[0] == 0 && this.data.idcard_ids[1] == 0) {
                        this.data.error = '所有信息不能为空';
                        return false;
                }
                if (this.data.idcard_ids[0] != 0 && this.data.idcard_ids[1] == 0) {
                        this.data.error = '身份证信息不能只传1张';
                        return false;
                }
                if (this.data.idcard_ids[0] == 0 && this.data.idcard_ids[1] != 0) {
                        this.data.error = '身份证信息不能只传1张';
                        return false;
                }
                // if (values.company === "") {
                //         this.data.error = '公司名称不能为空';
                //         return false;
                // }

                // if (this.data.org_code_id == "") {
                //         this.data.error = '请上传组织机构代码证';
                //         return false;
                // }
                // if (this.data.license_id == "") {
                //         this.data.error = '请上传组织机构代码证';
                //         return false;
                // }
                // if (this.data.idcard_ids[0] == 0 || this.data.idcard_ids[1] == 0) {
                //         this.data.error = '请上传身份证正反两面';
                //         return false;
                // }

                return true;
        },

        /**
         * 修改地区
         */
        bindRegionChange: function(e) {
                this.setData({
                        region: e.detail.value
                })
        },


        /**
         * 快捷导航 显示/隐藏
         */
        commonNav: function() {
                this.setData({
                        nav_select: !this.data.nav_select
                });
        },

        /**
         * 快捷导航跳转
         */
        nav: function(e) {
                let url = '';
                switch (e.currentTarget.dataset.index) {
                        case 'home':
                                url = '../index/index';
                                break;
                        case 'fenlei':
                                url = '../category/index';
                                break;
                        case 'cart':
                                url = '../flow/index';
                                break;
                        case 'profile':
                                url = '../user/index';
                                break;
                }
                wx.switchTab({
                        url
                });
        },

})