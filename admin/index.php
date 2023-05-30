<?php

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Collection\Manager;
use Admin\Hooks\hooks;

define("BASE_PATH", (__DIR__));
require_once(BASE_PATH . '/vendor/autoload.php');
require_once('hooks.php');

$container = new FactoryDefault();
$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://root:VajsFVXK36vxh4M6@cluster0.nwpyx9q.mongodb.net/?retryWrites=true&w=majority'
        );
        return $mongo->webhooks;
    },
    true
);
$container->set(
    'collectionManager',
    function () {
        return new Manager();
    }
);

$app = new Micro($container);


// products/create, products/update
$app->post(
    '/product/create',
    function () use ($app) {
        $product = $app->request->getJsonRawBody();
        $arr = [
            'id' => uniqid(),
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock
        ];
        $output = $this->mongo->products->insertOne($arr);

        if ($output->getInsertedCount() > 0) {
            echo "<h3>Inserted successfully</h3>";
            // inserted successfully, and update the hook now
            hooks::createHooks('/product/create', $arr);
        } else {
            echo "<h3>There was some error</h3>";
            die;
        }
    }
);
$app->get(
    '/product/get',
    function () {
        $output = $this->mongo->products->find();
        $data = [];
        foreach ($output as $value) {
            $data[] = $value;
        }
        echo json_encode($data);
    }
);
$app->put(
    '/product/update',
    function () use ($app) {
        $product = $app->request->getJsonRawBody();
        $id = $product->id;
        $output = $this->mongo->products->updateOne(['id' => (string)$id], ['$set' => $product]);
        if ($output->getModifiedCount()) {
            echo "<h3>Updated Successfully</h3>";
            // update the hook now
            hooks::createHooks('/product/update', $product);
        } else {
            echo "<h3>There was some error !</h3>";
            die;
        }
    }
);

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo '<h1>This is crazy, but this page was not found!</h1>';
});

$app->handle($_SERVER['REQUEST_URI']);
