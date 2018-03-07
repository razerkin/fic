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
use think\Request;
use app\admin\validate\User as UserValidate;
use think\Db;
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


    public function login()
    {
        $data = input('post.');
        $user = new User();
        $result = $user->where('name', $data['name'])->find();
        if ($result) {
            if ($result['password'] === md5($data['password'])) {
                session('name', $data['name']);
                session('user_id', $result['id']);
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
    public function sign() {
        return $this->fetch();
    }

    public function signIn()
    {

        $data = input('post.');
        if (captcha_check($data['code'])) {
            $val = new UserValidate();

            if (!($val->check($data)))
            {
                $this->error($val->getError());
                exit;
            }
            $user = new User($data);
            $ret = $user->allowField(true)->save();

            if($ret)
            {
                //分配用户权限
                $giveAuth = ['uid'=>$user->id, 'group_id'=>'3'];
                if( Db::table('think_auth_group_access')->insert($giveAuth)) {
                    $this->success('新增员工成功！', '/');
                } else {
                    $this->error('分配权限失败');
                }
            } else {
                $this->error('新增员工失败!');
            }
        } else {
            $this->error('验证码不正确');
        }
    }

    public function logout()
    {
        session(null);
        $this->success('退出登录成功','/');
    }







}
