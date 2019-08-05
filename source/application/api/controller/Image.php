<?php

namespace app\api\controller;

use Imagine\Gd\Imagine;
use think\Request;

class Image extends Controller
{
    /**
     * 渲染压缩图片     
     */
    public function render(Request $request)
    {        
        $q = $request->get('q',100);
        $file = $request->get('path');

        $option = [];
        $option['jpeg_quality'] = $q;

        $imagine = new Imagine();
        $imagine->open($file)->show('jpg',$option);
        exit;

    }
}
