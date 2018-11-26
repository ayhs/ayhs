<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
	public function login(){
		if(IS_POST)
		{
            // 1.创建数据表操作对象
			$userTable = D('user');
            // 2.获取表单数据
			$username = I('post.uemail');
			$pwd = I('post.upwd');
            //判断用户名有效性
			if($userTable->isValidUser($username,$pwd))
			{
                //3.1把用户名信息放到session中
				session('logineduser',$username);
				session('logineduserid',$userTable->getUserIdByUserName($username));
				session('loginedusertype',$userTable->getUserTypeByUserName($username));
                //dump($_SESSION);
                //3.2跳转到首页
				$this->success('登录成功','javascript:window.location.href=document.referrer;');
			}
			else{
				$this->error('用户名或密码错误，请重新填写');
			}

		}
		else{
			$this->display();
		}
	}
	public function register()
	{
		if (IS_POST) {

            // 1.创建数据表操作对象
			$userTable = D('user');
            // 2.获取表单数据
			$nickname = I('post.uemail');
			$pwd = I('post.upwd');
			$realname = I('post.uname');
			$captcha = I('post.captcha');

			// if (Captcha::checkCaptcha($captcha, Captcha::REGISTR_CAPTCHA)) {
   //              // 3.执行注册
			$r = $userTable->doUserRegister(trim($nickname), $pwd, trim($realname));
			if ($r)
			{
				session('logineduser',$nickname);
				session('logineduserid',$userTable->getUserIdByUserName($nickname));
				$this->success('注册并登录成功,','javascript:window.location.href=document.referrer;');
			}
			// }else {
			// 	unlink('./Public/image/'.$userimgurl);
			// 	$this->error('验证码不正确');
			// }
		} else {
            // 现实视图文件
			$this->display();
		}
	}
	public function logout()
	{
        //1注销session
		session('logineduser',null);
		session('logineduserid',null);
        //跳转首页
		$this->success('注销成功','javascript:window.location.href=document.referrer;',1);
	}
	public function captcha($atype = 'register')
	{
		switch ($atype) {
			case 'register':
			Captcha::createCaptcha(Captcha::REGISTR_CAPTCHA);
			break;
			case 'login':
			Captcha::createCaptcha(Captcha::LOGIN_CAPTCHA);
			break;
			default:
			Captcha::createCaptcha(Captcha::REGISTR_CAPTCHA);
			break;
		}
	}
}