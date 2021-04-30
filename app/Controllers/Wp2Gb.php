<?php

use App\Controllers\BaseController;

/*
 * wordpress博客导入Gitblog工具
 */

class Wp2Gb extends BaseController
{


    public function index()
    {

        //非命令行访问，返回404
        if (!$this->request->isCLI()) {
            return false;
        }
        $wordpress = new \App\Libraries\WordPress();
        $wordpress->loadWP();
        echo $wordpress->errMsg();
        return true;
    }
}
