<?php

// 应用公共函数库文件

use think\Request;

/**
 * 打印调试函数
 * @param $content
 * @param $is_die
 */
function pre($content, $is_die = true)
{
    header('Content-type: text/html; charset=utf-8');
    echo '<pre>' . print_r($content, true);
    $is_die && die();
}

/**
 * 驼峰命名转下划线命名
 * @param $str
 * @return string
 */
function toUnderScore($str)
{
    $dstr = preg_replace_callback('/([A-Z]+)/', function ($matchs) {
        return '_' . strtolower($matchs[0]);
    }, $str);
    return trim(preg_replace('/_{2,}/', '_', $dstr), '_');
}

/**
 * 生成密码hash值
 * @param $password
 * @return string
 */
function yoshop_hash($password)
{
    return md5(md5($password) . 'yoshop_salt_SmTRx');
}

/**
 * 获取当前域名及根路径
 * @return string
 */
function base_url()
{
    $request = Request::instance();
    $subDir = str_replace('\\', '/', dirname($request->server('PHP_SELF')));
    return $request->scheme() . '://' . $request->host() . $subDir . ($subDir === '/' ? '' : '/');
}

/**
 * 写入日志
 * @param string|array $values
 * @param string $dir
 * @return bool|int
 */
function write_log($values, $dir)
{
    if (is_array($values))
        $values = print_r($values, true);
    // 日志内容
    $content = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $values . PHP_EOL . PHP_EOL;
    try {
        // 文件路径
        $filePath = $dir . '/logs/';
        // 路径不存在则创建
        !is_dir($filePath) && mkdir($filePath, 0755, true);
        // 写入文件
        return file_put_contents($filePath . date('Ymd') . '.log', $content, FILE_APPEND);
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * curl请求指定url
 * @param $url
 * @param array $data
 * @return mixed
 */
function curl($url, $data = [])
{
    // 处理get数据
    if (!empty($data)) {
        $url = $url . '?' . http_build_query($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

if (!function_exists('array_column')) {
    /**
     * array_column 兼容低版本php
     * (PHP < 5.5.0)
     * @param $array
     * @param $columnKey
     * @param null $indexKey
     * @return array
     */
    function array_column($array, $columnKey, $indexKey = null)
    {
        $result = array();
        foreach ($array as $subArray) {
            if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 多维数组合并
 * @param $array1
 * @param $array2
 * @return array
 */
function array_merge_multiple($array1, $array2)
{
    $merge = $array1 + $array2;
    $data = [];
    foreach ($merge as $key => $val) {
        if (isset($array1[$key])
            && is_array($array1[$key])
            && isset($array2[$key])
            && is_array($array2[$key])) {
            $data[$key] = array_merge_multiple($array1[$key], $array2[$key]);
        } else {
            $data[$key] = isset($array2[$key]) ? $array2[$key] : $array1[$key];
        }
    }
    return $data;
}



/**
 * 删除数组中指定值的元素
 * @param array $arr 数组
 * @param string $val 值
 * @return boolean
 */
function array_del(&$arr, $val)
{
    if (empty($val)) return false;
    $key = array_search($val, $arr);
    $keys = array_keys($arr);
    $position = array_search($key, $keys);
    if (false !== $position) {
        $r = array_splice($arr, $position, 1);
        return true;
    }
    return false;
}






/**
 * 成员状态获取 by arr
 */
function getMemeberStatus($arr)
{    
    if (!empty($arr)) {
        // 区分 派送和售后        
        $data = [];
        $data['order'] = [];
        $data['after'] = [];
        foreach ($arr as $key => $value) {
            if(!empty($value['order_id'])){
                $data['order'][$value['order_id']][] = $value;
            }
            if(!empty($value['after_id'])){
                $data['after'][$value['after_id']][] = $value;
            }            
        }        
        // 筛掉已完成        
        foreach ($data['order'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v['status'] == 20) {
                    // 已完成
                    unset($data['order'][$key]);
                }
            }
        }
        foreach ($data['after'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v['status'] == 20) {
                    // 已完成
                    unset($data['after'][$key]);
                }
            }
        }

        $res = [];
        foreach ($data['order'] as $key => $value) {
            foreach ($value as $k => $v) {
                $res[] = $v;
            }
        }
        foreach ($data['after'] as $key => $value) {
            foreach ($value as $k => $v) {
                $res[] = $v;
            }
        }
        // 
        if (!empty($res)) {            
            // 
            usort($res, function ($a, $b) {
                return $a['create_time'] > $b['create_time'];
            });
            // 
            $data = array_values($res);
            if(!empty($data[0]['order_id'])) $msg = '配送中';
            if(!empty($data[0]['after_id'])) $msg = '维修中';            
            $code = 1;
        } else {
            $msg = '空闲';
            $code = 0;
        }
    } else {
        $msg = '空闲';
        $code = 0;
    }
    return compact('msg', 'code');
}