<?php


namespace invoice\payment;


use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractNotificationController extends AbstractActionController
{
    abstract function onPay($orderId, $amount);
    abstract function onFail($orderId);
    abstract function onRefund($orderId);

    public function notifyAction() {
        $postData = file_get_contents('php://input');
        $notification = json_decode($postData, true);

        $config = (new Module())->getConfig();
        $key = $config['invoice']['api_key'];
        
        if($notification == null or empty($notification)) {
            echo 'ERROR';
            return new Response();
        }
        $type = $notification["notification_type"];
        $id = $notification["order"]["id"];

        if(!isset($notification['status'])) {
            echo 'ERROR';
            return new Response();
        }
        if($notification['signature'] != $this->getSignature($notification['id'], $notification["status"], $key)) {
            echo 'ERROR';
            return new Response();
        }


        if($type == "pay") {
            switch ($notification['status']) {
                case "successful":
                    $this->onPay($id, $notification['order']['amount']);
                    break;
                case "failed":
                    $this->onFail($id);
                    break;
            }
        }

        if($type == "refund") {
            $this->onRefund($id);
        }

        echo 'OK';
        return new Response();
    }

    private function getSignature($id, $status, $key) {
        return md5($id.$status.$key);
    }
}