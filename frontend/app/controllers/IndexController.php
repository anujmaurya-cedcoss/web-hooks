<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }
    public function signupAction()
    {

        $arr = [
            'name' => $_POST['name'],
            'mail' => $_POST['mail'],
            'pass' => $_POST['pass'],
        ];
        if ($arr['pass'] != '' && $arr['name'] != '') {
            // insert credentials with jwt token in db
            $output = $this->mongo->users->insertOne($arr);
            $this->response->redirect('/index/login');
        } else {
            echo "<h1>Enter Correct Details</h1>";
            die;
        }
    }

    public function loginAction()
    {
        // redirected to view
    }
    public function doLoginAction()
    {
        $output = $this->mongo->users->findOne(["mail" => $_POST['mail'], "pass" => $_POST['pass']]);
        if ($output->name != '') {
            $this->response->redirect('/webhooks/index');
        } else {
            echo "<h3>Invalid Credentials!</h3>";
            die;
        }
    }
}
