<?php
/**
 * Created by PhpStorm.
 * User: szl4zsy
 * Date: 12/23/2016
 * Time: 9:40 AM
 */

namespace Home\Controller;
use Think\Controller;

class WeiboController extends Controller
{
    public $topic_reply=array();        //存放回复数组集合
    public function index()
    {
        $this->weibo_list=$this->showResult();
        $this->display('weibo');
    }
    public function showResult(){
//        $weibo=M('topic');
//        $list=$weibo->order('topic_id')->limit(10)->select();
//        var_dump($list);
        $weibo=M();
        $list=$weibo->procedure('call sp_topic_list');
        $weibo_list=$list[0];
        $weibo_meta=$list[1];
        foreach($weibo_list as $k=>$list_item)
        {
            unset($weibo_list[$k]['idset']);
            foreach($weibo_meta as $key=>$meta_item)
            {
                if($meta_item['topic_id']==$list_item['topic_id'])
                {
                    $weibo_list[$k][$meta_item['meta_key']]=$meta_item['meta_value'];
                }
            }
        }
        return $weibo_list;
//        echo "weibo_list=eval('".json_encode($weibo_list)."');".PHP_EOL;
    }
    public function addReply()
    {
        $data=json_decode(file_get_contents("php://input"));
        $data=$data[0];
        $reply=M();
        $result=$reply->query('call sp_topic_new_reply("'.$data->reply_content.'",'.$data->user_id.','.$data->topic_id.','.$data->pid.')');
        echo $result[0]['success'];
    }
    public function gainReply(){
        $topic_id=file_get_contents("php://input");
        $replies=M('topic_reply');
        $this->topic_reply=$replies->where(' topic_id = '.$topic_id)->select();
//        $this->_genReplyRecursion();
//        var_export($this->_genReplyRecursion());
        echo json_encode($this->_genReplyRecursion());
    }
    public function _genReplyRecursion(){
        $replies=[];
        //循环初始数组，取出根评论及parent_reply_id为0的元素作为根节点
        foreach($this->topic_reply as $key => $value)
        {
            if($value['parent_reply_id']==0)
            {
                unset($this->topic_reply[$key]);
                //将根节点内容和他的主键ID传给函数用于生成递归评论
                $replies[]=$this->_genReplyTree($value,$value['reply_id']);
            }
        }
        return $replies;
    }
    public function _genReplyTree($father,$father_id)
    {
//        var_export($this->topic_reply);
        //获得父节点下的子节点
        $childs=$this->_getChildReplies($father,$father_id);
        //如果子节点获取到了，再去查看子节点下是不是还有子节点
        if(isset($childs['child']))
        {
            foreach ($childs['child'] as $key=>$value)
            {
                $children=$this->_genReplyTree($value,$value['reply_id']);
//                var_export($children['child']);
//                var_export(PHP_EOL.'----------------'.PHP_EOL);
                if($children['child']!=null)
                {
                    $childs['child'][$key]['child']=$children['child'];
                }
            }
        }
        return $childs;
    }
    public function _getChildReplies($father,$father_id)
    {
        foreach($this->topic_reply as $key => $value)
        {
            if($value['parent_reply_id']==$father_id)
            {
                unset($this->topic_reply[$key]);
                //注意这里一定要有[]，不然会覆盖上一个child
                $father['child'][]=$value;
            }
        }
        return $father;
    }
}