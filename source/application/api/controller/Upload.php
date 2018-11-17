<?php

namespace app\api\controller;

use app\common\model\UploadApiFile;
use app\common\library\storage\Driver as StorageDriver;
use app\store\model\Setting as SettingModel;
use think\Request;
use think\Db;


/**
 * 文件库管理
 * Class Upload
 * @package app\store\controller
 */
class Upload extends Controller
{
    private $config;

    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();
        // 存储配置信息
        $this->config = SettingModel::getItem('storage');
    }

    /**
     * 图片上传接口
     * @param int $group_id
     * @return array
     * @throws \think\Exception
     */
    public function image()
    {                
        // 实例化存储驱动
        $StorageDriver = new StorageDriver($this->config);
        // 上传图片
        if (!$StorageDriver->upload('api'))
            return json(['code' => 0, 'msg' => '图片上传失败' . $StorageDriver->getError()]);
        // 图片上传路径
        $fileName = $StorageDriver->getFileName();
        // 图片信息
        $fileInfo = $StorageDriver->getFileInfo();
        // 添加api文件库记录
        $uploadFile = $this->addUploadFile('user/' . $fileName, $fileInfo, input('wxapp_id'));
        // 图片上传成功
        return json(['api_file_id' => $uploadFile]);
    }

    /**
     * 添加文件库上传记录
     * @param $group_id
     * @param $fileName
     * @param $fileInfo
     * @param $fileType
     * @return UploadApiFile
     */
    private function addUploadFile($fileName, $fileInfo, $wxapp_id)
    {        
        // 添加文件库记录
        $model = new UploadApiFile;
        $obj = $model->addInfo([
            'file_name' => $fileName,
            'extension' => pathinfo($fileInfo['name'], PATHINFO_EXTENSION),
            'wxapp_id' => $wxapp_id
        ]);
        return $obj;
    }

}
