<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->getAttrs();
        $this->display();
    }
    public function getClass()
    {
        $getClass=M('class')->where('prod_pclassid = 0')->select();
        echo 'classes='.json_encode($getClass);
        exit();
    }
    public function getAttrs()
    {
        $getAttrs=M('class_attr')->select();
        $this->assign('attrs',$getAttrs);
    }
    public function getForm()
    {
        $post=file_get_contents('php://input');
        $post=json_decode($post);
        $this->insertData($post);
    }
    public function insertData($post)
    {
        $Model = new \Think\Model();
        try{
            if(is_object($post))
            {
                if(isset($post->prod_data))
                {
                    foreach($post->prod_data as $item)
                    {
                        if($item->name=='prod_name')$prod_name=$item->value;
                        if($item->name=='prod_intr')$prod_intr=$item->value;
                    }
                }
                if(isset($post->attr_data))
                {
                    $attrs='';
//                    var_export($post->attr_data);
                    foreach($post->attr_data as $attr)
                    {
                        if($attrs!='')$attrs.=' union ';
                        $attrs.=' select '.$attr->attr_id.' as attr_id, \''.$attr->value.'\' as attr_value ';
                    }
                }
                $Model->execute('call think_mysql.sp_new_prod("'.$prod_name.'",'.$post->classId.',"'.$attrs.'",@res)');
                $res=$Model->query('select @res as res');
                echo $res[0]['res'];
                exit(); 
            }
        }catch(\Think\Exception $ex){
            var_export($ex->getMessage());
        }

    }
}