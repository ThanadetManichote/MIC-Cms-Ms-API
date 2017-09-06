<?php
namespace App\Services;

use Phalcon\Exception;

class Service extends \Phalcon\Mvc\Micro {

    public function getService($name)
    {
        $className = "\\App\\Services\\{$name}";

        if (!class_exists($className)) {
            throw new Exception("Service Class {$className} doesn't exists.");
        }

        return new $className();
    }

    //Method for get service config
    protected function getServiceConfig($name)
    {
        $config = $this->config->services->toArray();

        return $config[$name];
    }

    //Method for manage response
    protected function manageResponse($response, $serviceName)
    {
        //Define output
        $result = [
            'success' => true,
            'message' => '',
            'data'    => [],
        ];

        if (empty($response)) {
            $result['success'] = false;
            $result['message'] = str_replace('[servicename]', $serviceName, $this->message->text->serverError);
            return $result;
        }

        $body = json_decode($response->body, true);       

        if ($response->header->statusCode != 200) {
             $result['success'] = false;
            if (isset($body['message'])) {
                $result['message'] = $body['message'];
                return $result; 
            }

            if (isset($body['status']['code']) && $body['status']['code'] != 200) {
                $result['message'] = $body['error']['message'];
                return $result; 
            }

            $result['message'] = str_replace('[servicename]', $serviceName, $this->message->text->serverError);

        } else {
            //success
            $result['data'] = $body['data'];
        }
        
        return $result;
    }
}