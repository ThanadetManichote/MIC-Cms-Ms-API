<?php
namespace App\Services;

use App\Services\Service;

class SaleService extends Service {

    private $serviceName = 'sale';


    public function createAccount($params)
    {
        //get config
        $config = $this->getServiceConfig($this->serviceName);
        //set base url
        $this->curl->setBaseUri($config["url"]);

        $response = $this->curl->post($config['create'], json_encode($params));

        return $this->manageResponse($response, $this->serviceName);

    }
}