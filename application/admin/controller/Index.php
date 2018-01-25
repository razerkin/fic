<?php
/**
 * Created by PhpStorm.
 * User: Akin
 * Date: 2018/1/22
 * Time: 20:11
 */
namespace app\admin\controller;
use think\Controller;
use app\admin\model\User;
//use think\captcha\Captcha;

class Index extends Controller
{
    public function index()
    {
//        $config =array('fontSize' => 30, 'length' => 4);
//        $captcha = new Captcha();
//        $captcha->entry($config);
        return $this->fetch('login');
    }


    public function checkLogin()
    {
        $data = input('post.');
        $user = new User();
        $result = $user->where('name', $data['name'])->find();
        if ($result) {
            if ($result['password'] === md5($data['password'])) {
                session('name', $data['name']);
            } else {
                $this->error('密码不正确');
            }
        } else {
            $this->error('用户名不存在');
        }
        if (captcha_check($data['code'])) {
            $this->success('登陆成功', 'User/Index');
        } else {
            $this->error('验证码不正确');
        }

    }

    public function logout()
    {
        session(null);
        $this->success('退出登录成功','Index/index');
    }







}
