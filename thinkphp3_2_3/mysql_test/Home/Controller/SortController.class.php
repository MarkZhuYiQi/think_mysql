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
        $res=$news->query('select * from news_main PARTITION(news_programsp0) order by id desc;');
        echo json_encode($res);
    }
}