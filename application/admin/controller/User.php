<?php
namespace app\admin\controller;
use app\admin\model\User as UserModel;
use app\admin\validate\User as UserValidate;
use app\admin\controller\Base;
use think\auth\Auth;
use think\Db;

class User extends Base
{
    protected $is_check_auth = ['add','insertuser','updateuser','userupdate','userdelete','userinsert','userlist','adminupdateuser','adminuserupdate'];
    protected $is_check_login = ['*'];
    public function index()
    {
        $user_id = input('session.user_id');
        $this->assign('user_id',$user_id);
     return $this->fetch();
    }
    public function list()
    {
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
    public function  adv()
    {
        return $this->fetch();
    }
    public function  book()
    {
        return $this->fetch();
    }
    public function  cate()
    {
        return $this->fetch();
    }
    public function  cateEdit()
    {
        return $this->fetch();
    }
    public function  pass()
    {
        return $this->fetch();
    }
    public function  page()
    {
        return $this->fetch();
    }
    public function  tips()
    {
        return $this->fetch();
    }
    public function  info()
    {
        return $this->fetch();
    }
    public function  column()
    {
        return $this->fetch();
    }
    public function  insertUser()
    {
        return $this->fetch();
    }

    //用户的增删改查方法
    public function  updateUser()
    {
        $id = input('get.id');
        $data = UserModel::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function  userList()
    {
//        $data = UserModel::all();
//        $this->assign('data',$data);
//        return $this->fetch();
        //通过分页显示列表
        $data = UserModel::paginate(3);
        $page = $data->render();
        $this->assign('data',$data);
        $this->assign('page',$page);
        return $this->fetch();
    }
    public function userInsert()
    {
            $data = input('post.');
            $val = new UserValidate();
            if (!($val->check($data)))
            {
                $this->error($val->getError());
                exit;
            }
            $user = new UserModel($data);
            $ret = $user->allowField(true)->save();

            if($ret)
            {
                //分配用户权限
                $giveAuth = ['uid'=>$user->id, 'group_id'=>input('post.user_status')];
                if( Db::table('think_auth_group_access')->insert($giveAuth)) {
                    $this->success('新增员工成功！', 'User/userlist');
                } else {
                    $this->error('分配权限失败');
                }
            } else {
                $this->error('新增员工失败!');
            }
    }
    public  function userUpdate()
    {
        $data = input('post.');
        $id = input('post.id');
        $val = new UserValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $user = new UserModel();
        $ret = $user->allowField(true)->save($data,['id'=>$id]);
        if($ret)
        {
            $this->success('修改员工信息成功！','User/userlist');
        } else {
            $this->error('修改用户信息失败!');
        }
    }
    public  function userDelete()
    {
        $id = input('get.id');
        $ret = UserModel::destroy($id);
        if($ret)
        {
            $this->success('删除员工信息成功！','User/userlist');
        } else {
            $this->error('删除用户信息失败!');
        }
    }
    public function  adminUpdateUser()
    {
        $id = input('get.id');
        $data = UserModel::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }
    public  function adminUserUpdate()
    {
        $data = input('post.');
        $id = input('post.id');
        $val = new UserValidate();
        if (!($val->check($data)))
        {
            $this->error($val->getError());
            exit;
        }
        $user = new UserModel();
        $ret = $user->allowField(true)->save($data,['id'=>$id]);
        if($ret) {
            if (Db::table('think_auth_group_access')->where('uid',$id)->setField('group_id',input('post.user_status'))) {
                $this->success('修改员工信息成功！', 'User/userlist');
            } else {
                $this->error('分配权限失败');
            }
        }
        else {
            $this->error('修改用户信息失败!');
        }
    }






}