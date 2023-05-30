<?php

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Collection\Manager;

define("BASE_PATH", (__DIR__));
require_once(BASE_PATH . '/vendor/autoload.php');

// Use Loader() to autoload our model
$container = new FactoryDefault();
$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://root:VajsFVXK36vxh4M6@cluster0.nwpyx9q.mongodb.net/?retryWrites=true&w=majority'
        );
        return $mongo->client_db;
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
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock
        ];
        $output = $this->mongo->products->insertOne($arr);

        if ($output->getInsertedCount() > 0) {
            echo "<h3>New Product Inserted on client side</h3>";
        } else {
            echo "<h3>There was some error on client side</h3>";
            die;
        }
    }
);

$app->put(
    '/product/update',
    function () use ($app) {
        $product = $app->request->getJsonRawBody();
        $product=(array)$product;
        $id = $product['id'];
        $output = $this->mongo->products->updateOne(['id' => $id], ['$set' => $product]);
        if ($output->getModifiedCount()) {
            echo "<h3>Product Update Successfully on client side</h3>";
        } else {
            echo "<h3>There was some error on client side!</h3>";
            die;
        }
        var_dump($output);
    }
);

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo '<h1>This is crazy, but this page was not found!</h1>';
});

$app->handle($_SERVER['REQUEST_URI']);
