<?php

use Phalcon\Mvc\Controller;
use Phalcon\Escaper;

$escaper = new Escaper();

class WebhooksController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }

    public function createAction()
    {
        $arr = [
            'webhook' => $this->escaper->escapeHTML($this->request->getPost('webhook')),
            'event' => $this->escaper->escapeHTML($this->request->getPost('event')),
            'key' => $this->escaper->escapeHTML($this->request->getPost('key')),
            'ip' => $this->escaper->escapeHTML($this->request->getPost('ip'))
        ];
        $output = $this->mongo->hooks->insertOne($arr);

        if ($output->getInsertedCount() > 0) {
            echo "<h3>Inserted Successfully!</h3>";
        } else {
            echo "<h3>There was some error</h3>";
        }
        die;
    }
}
