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
        $reply=M();
        $reply->query('sp_topic_new_reply('.$this->reply_content.','.$this->user_id.','.$this->topic_id.','.$this->pid.')');
    }
}