<?php
/**
 * Created by PhpStorm.
 * User: szl4zsy
 * Date: 12/5/2016
 * Time: 11:28 AM
 */

namespace Home\Controller;
use Think\Controller;

class SortController extends Controller
{
    public function index()
    {
        $this->display('sort');
    }
    public function sortData()
    {
        $post=json_decode(file_get_contents('php://input'));
//        select * from news_main PARTITION(news_programsp0) order by id desc;

        $news=M();
        $class='news_';
        $hash=0;
        $sort='';
        foreach($post as $item)
        {
            if($item->name=='classify')
            {
                switch($item->classId){
                    case 1:
                        $class.='car';
                        break;
                    case 2:
                        $class.='sens';
                        break;
                    case 3:
                        $class.='world';
                        break;
                    case 4:
                        $class.='program';
                        break;
                    case 5:
                        $class.='test';
                        break;
                    default:
                        $class='';
                }
            }
            else
            {
                if($item->classId!=0)
                {
                    if($item->classId>2)
                    {
                        $hash=$item->classId % 3;
                    }
                    elseif($item->classId==1)
                    {
                        $sort='news_click asc';
                    }else
                    {
                        $sort='news_click desc';
                    }
                }
                else
                {
                    $sort='id desc';
                }
            }
        }
        if($class!='')$where=' PARTITION('.$class.')';
        if($hash>0)$where=' PARTITION('.$class.'sp'.$hash.') ';
        $where.=' order by '.$sort;
        $sql='select * from news_main '.$where;
//        echo $sql;
        $res=$news->query($sql);
        echo json_encode($res);
    }
}