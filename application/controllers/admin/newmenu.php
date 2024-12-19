<?php

defined('BASEPATH') or exit('No direct script access allowed');

class newmenu extends AdminController
{
    public function index(){
        $this->load->view("admin/knowledge_base/newmenu");
    }
}