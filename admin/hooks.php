<?php

namespace Admin\Hooks;

require_once(BASE_PATH . '/vendor/autoload.php');
class hooks
{
    public function createHooks($method, $args)
    {
        $mongo = new \MongoDB\Client(
            'mongodb+srv://root:VajsFVXK36vxh4M6@cluster0.nwpyx9q.mongodb.net/?retryWrites=true&w=majority'
        );
        $args = json_encode($args);
        $allHooks = $mongo->webhooks->hooks->find(["event" => $method]);
        foreach ($allHooks as $value) {
            $url = $value->ip;
            $url .= $value->event;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if ($method == '/product/update') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'put');
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);
            return $output;
        }
    }
}
