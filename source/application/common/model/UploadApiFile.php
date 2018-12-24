<?php

namespace app\common\model;

use think\Request;

/**
 * 文件库模型
 * Class UploadFile
 * @package app\common\model
 */
class UploadApiFile extends BaseModel
{

    protected $name = 'upload_api_file';
    protected $updateTime = false;
    protected $deleteTime = false;
    protected $append = ['file_path'];

    /**
     * 获取图片完整路径
     * @param $value
     * @param $data
     * @return string
     */
    public function getFilePathAttr($value, $data)
    {
        return self::$base_url . 'uploads/' . $data['file_name'];
    }

    /**
     * 查询文件id
     * @param $fileId
     * @return mixed
     */
    public static function getFilePath($fileId)
    {
        $data = self::where(['file_id' => $fileId])->find();
        return self::$base_url . 'uploads/' . $data['file_name'];
    }


    public static function getFilesPath($filesIds)
    {
        $data = self::whereIn('file_id', $filesIds)->select()->toArray();
        foreach ($data as $key => $value) {
            $data[$key] = self::$base_url . 'uploads/' . $value['file_name'];
        }
        return $data;
    }


    public function addInfo($data)
    {
        if ($this->save($data)) return $this->file_id;
    }

}
