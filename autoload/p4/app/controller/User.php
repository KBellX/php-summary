<?php

namespace app\controller;

use app\model\User as UserModel;

class User 
{
    public function index() 
    {
        echo "this is controller User function index <br />\n";
        $model = new UserModel();
        $model->getList();
    }
}
