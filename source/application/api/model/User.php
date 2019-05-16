<?php

namespace app\api\model;

use app\common\model\User as UserModel;
use app\api\model\Goods as GoodsModel;
//use app\api\model\Wxapp;
use app\common\library\wechat\WxUser;
use app\common\exception\BaseException;
use app\api\model\AccountMoney;
use think\Cache;
use think\Request;
use think\Db;


/**
 * 用户模型类
 * Class User
 * @package app\api\model
 */
class User extends UserModel
{
    private $token;
    protected $error;
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    /**
     * 获取用户信息
     * @param $token
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function getUser($token)
    {
        return self::detail(['open_id' => Cache::get($token)['openid']]);
    }

    /**
     * 用户登录
     * @param array $post
     * @return string
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login($post)
    {
        // 微信登录 获取session_key
        $session = $this->wxlogin($post['code']);
        // 自动注册用户
        $userInfo = json_decode(htmlspecialchars_decode($post['user_info']), true);
        $user_id = $this->register($session['openid'], $userInfo);
        // 生成token (session3rd)
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::set($this->token, $session, 86400 * 7);
        return $user_id;
    }

    /**
     * 获取token
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 微信登录
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    private function wxlogin($code)
    {
        // 获取当前小程序信息
        $wxapp = Wxapp::detail();
        // 微信登录 (获取session_key)
        $WxUser = new WxUser($wxapp['app_id'], $wxapp['app_secret']);
        if (!$session = $WxUser->sessionKey($code))
            throw new BaseException(['msg' => 'session_key 获取失败']);
        return $session;
    }

    /**
     * 生成用户认证的token
     * @param $openid
     * @return string
     */
    private function token($openid)
    {
        return md5($openid . self::$wxapp_id . 'token_salt');
    }

    /**
     * 自动注册用户
     * @param $open_id
     * @param $userInfo
     * @return mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    private function register($open_id, $userInfo)
    {
        if (!$user = self::get(['open_id' => $open_id])) {
            $user = $this;
            $userInfo['open_id'] = $open_id;
            $userInfo['wxapp_id'] = self::$wxapp_id;
        }
        $userInfo['nickName'] = preg_replace('/[\xf0-\xf7].{3}/', '', $userInfo['nickName']);
        if (!$user->allowField(true)->save($userInfo)) {
            throw new BaseException(['msg' => '用户注册失败']);
        } else {
            $account = new AccountMoney;
            if (!$account::get($user['user_id'])) {
                $account->save([
                    'user_id' => $user['user_id']
                ]);
            }
        }
        return $user['user_id'];
    }





    /**
     * 登陆情况下 更新手机号码
     */
    public function updatePhoneNumber($open_id, $phoneNumber)
    {
        Db::startTrans();
        try {
            $this->save(['phone' => $phoneNumber], ['open_id' => $open_id]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }




    // 添加收藏
    public function doFavorite($input)
    {
        $user_id = $input['user_id'];
        $favorite_goods_ids = explode(',', $input['favorite_goods_ids']);
        $favorite_goods_ids = json_encode($favorite_goods_ids);
        $obj = Db::name('user_favorite')->where('user_id', $user_id)->find();
        if (!$obj) {
            $res = Db::name('user_favorite')->insert([
                'user_id' => $user_id,
                'favorite_goods_ids' => $favorite_goods_ids
            ]);
        } else {
            $res = Db::name('user_favorite')->where('user_id', $user_id)->update([
                'favorite_goods_ids' => $favorite_goods_ids
            ]);
        }
        return $res;
    }

    // 获取收藏
    public function getFavorite($user_id)
    {
        $ids = Db::name('user_favorite')->where('user_id', $user_id)->value('favorite_goods_ids');
        $ids = json_decode($ids, true);
        return $ids;
    }



    /**
     * userlist
     */
    public function getTypeList($type, $page)
    {
        switch ($type) {
                // 认证用户
            case 1:
                return $this->with(['address', 'addressDefault'])->where('user_id', 'IN', function ($query) {
                    $query->name('exam')->where(['status' => 20, 'type' => 10])->field('user_id');
                })->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
                // 非认证用户
            case 2:
                return $this->with(['address', 'addressDefault'])->where('user_id', 'NOT IN', function ($query) {
                    $query->name('exam')->where(['status' => 20, 'type' => 10])->field('user_id');
                })->order('create_time', 'desc')->paginate(5, false, ['page' => $page, 'list_rows' => 5]);
                break;
        }
    }


    public function getDetail($user_id)
    {
        return $this->with(['address', 'addressDefault'])->where('user_id', $user_id)->find()->append(['license', 'idcard', 'other']);
    }
}
