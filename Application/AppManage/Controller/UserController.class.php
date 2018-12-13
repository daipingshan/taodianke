<?php
namespace AppManage\Controller;

class UserController extends CommonController {
    public function index(){
       $user = M('user');
       $count      = $user->count();// 查询满足要求的总记录数
       $Page       = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(25)
       $show       = $Page->show();// 分页显示输出
       // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $user->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
          
  
	    $this->assign('list',$list);// 赋值数据集
	    $this->assign('Sment',Sment);// 复制输出状态
	    $this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	public function edit(){
	    $user = M('user');
        $userId = I('get.id',0,'int');
        //查询
        if($userId != 0 && $userId != ''){

            $userinfo = $user->where('id='.$userId)->find(); 
            $this->assign('userinfo',$userinfo);// 赋值数据集
        }
        //修改
        
        if(IS_POST && $userId != 0){
           
            $data['email'] =        I('post.email',null,'email'); 
            $data['username'] =     I('post.username',null,'string'); 
            $data['real_name'] =    I('post.real_name',null,'string'); 
            $data['qq'] =           I('post.qq',null,'int'); 
            $data['bank_account'] =   I('post.bank_account',null,'string'); 
            $data['mobile'] =       I('post.mobile',null,'number_int');
            if($_POST['pwd'] != '' && isset($_POST['pwd'])){
                $data['password'] =     md5(trim(I('post.pwd',null,'string')));
            }
            
            if($user->where('id = '.$userId)->save($data)){
                
                $this->success('修改成功', '/Assets/user/index');
                
            }
            
        }
            
     
	    $this->display();
	}
	
}