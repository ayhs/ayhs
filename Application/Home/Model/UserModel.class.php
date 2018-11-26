<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model
{

    protected $tableName = 'user';
    // 对象的数据表
    protected $trueTableName = 'b_user';
    
    // 字段限定信息
    protected $_validate = array(
        array(
            'uemail',
            'require',
            '用户名必须！'
        ), // 默认情况下用正则进行验证
        array(
            'upwd',
            'require',
            '密码必须！'
        ),
        array(
            'uemail',
            '',
            '帐号名称已经存在！',
            0,
            'unique',
            1
        ),
    ); // 在新增的时候验证name字段是否唯一
      // array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
      // array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
      // array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    /**
     * 自动完成
     */
    protected $_auto = array (
        array('upwd', 'md5', 3, 'function') , // 对password字段在新增和编辑的时候使md5函数处理
    );
    public function getUserIdByUserName($nickname)
    {
        $cond['uemail']=$nickname;
        $r = $this->where($cond)->getField('uid');
        return $r;
    }
    public function getUserTypeByUserName($nickname)
    {
        $cond['uemail']=$nickname;
        $r = $this->where($cond)->getField('utype');
        return $r;
    }
    public function getImgByUid($uid)
    {
        $cond['uid']=$uid;
        $r = $this->where($cond)->getField('userimgurl');
        return $r;
    }
    /**
     * *
     *
     * @param string $nickname            
     * @param string $pwd            
     * @param string $userimg            
     * @return bool
     */
    public function doUserRegister($nickname, $pwd, $realname)
    {
        $data['uemail'] = $nickname;
        $data['upwd'] = $pwd;
        $data['uname'] = $realname;
        // return $this->add($data);
        // create用来验证
        if ($this->create($data)) {
            return $this->add();
        }
        echo $this->getError();
        return false;
    }

    public function doChangePwd($nickname, $oldPwd, $newPwd)
    {
        // 1.校验(已有)
        if ($oldPwd == $newPwd) {
            return false;
        }
        // 3.执行 q
        if (! $this->isValidUser($nickname, $oldPwd)) {
            return false;
        }
        // 4.修改
        $data['upwd'] = $newPwd;
        if($this->create($data))
        {
            return $this->where(array('uemail' => $nickname))->save();
        }
    }
    public function isValidUser($nickname, $pwd)
    {
        $count = $this->where(array(
            'uemail' => $nickname,
            'upwd' => md5($pwd),
        ))->count();
        return $count;
    }
}