<?php
namespace App\Controllers;

use App\Controllers\ApiController;
use App\Repositories\ContentRepository;
use App\Library\MyLibrary;
use Phalcon\Http\Client\Request;


class ContentController extends ApiController
{
    //------- start : Define variable ----------//
    private $getContentDetail = [
        [
            'type'   => 'required',
            'fields' => ['id'],
        ]
    ];

    private $urlcurl = "http://staging-mic-cms-ms-api.eggdigital.com";

    //------- end : Define variable ----------//


    //------- start: protected method ------------//
    //method for get content repo
    protected function getContentRepository()
    {
        return $this->repository->getRepository('ContentRepository');
    }
    //------- end: protected method ------------//

    //------- start: Main method ---------------//
    //Method for get content data
    public function getContentAction()
    {
        //get input
        $inputs   = $this->getAllUrlParam();

        $headers = $this->getHeaders();

        //get field repository
        $contentRepo = $this->getContentRepository();

        //get content data by input
        $result   = $contentRepo->getContent($inputs,$headers);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

         if (isset($result['totalRecord'])) {
            $inputs['totalRecord'] = $result['totalRecord'];
        }

        return $this->outputgraph($result, $inputs);
    }

    //Method for get user detail data
    public function getContentDetailAction()
    {
        //get input
        $inputs       = $this->getAllUrlParam();

        $headers = $this->getHeaders();

        //define default
        $default      = [];
        
        // Validate input
        $params       = $this->validateApi($this->getContentDetail, $default, $inputs);
        
        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }
        
        //get content repository
        $contentRepo = $this->getContentRepository();

        //get user data by id
        $result   = $contentRepo->getContentDetail($params,$headers);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    public function postContentCreateAction()
    {
        $data = $this->curlGetSchema();
        $rule = $this->insertContentRule($data);
        //get input
        $inputs = $this->postInput();

        $inputsforvalidate = $this->formatMultiInput($inputs);

        //define default
        $default = [];


        // Validate input
        $params = $this->validateApi($rule, $default, $inputsforvalidate);

        if (isset($params['msgError']))
        {
            //Validate error
            return $this->validateError($params['fieldError'], $params['msgError']);
        }

        //get content repository
        $contentRepo = $this->getContentRepository();

        //add user data by input
        $result = $contentRepo->addContent($params);

        //Check response error
        if (!$result['success'])
        {
            return $this->validateBussinessError($result['message']);
        }

        return $this->output($result['data']);
    }

    private function curlGetSchema()
    {
        // get available provider Curl or Stream
        $provider = Request::getProvider();
        $provider->setBaseUri($this->urlcurl);
        $provider->header->set('Accept', 'application/json');
        $response = $provider->get('schema');
        return json_decode($response->body);
    }

    private function insertContentRule($data)
    {
        $rule = [];
        if($data->status->code == 200){
            foreach ($data->data as $k => $v) {
                $type = $v->attributes->attr;
                
                
                if($type == 'multi_required'){
                    // $fields[] = $v->attributes->name;
                    $fields[] = $v->attributes->name.'_th';
                    $key = $this->searchValue($rule,'multi_required');
                    if($key != 'notfound' || $key == '0'){
                        $rule[$key]['fields'] = $fields;
                    }else{
                        $rule[$k] = [
                            'type' => $v->attributes->attr,
                            'fields' => $fields
                        ];
                    }
                }else{
                    $fields = '';
                    $fields[] = $v->attributes->name;
                    $rule[$k] = [
                        'type' => $v->attributes->attr,
                        'fields' => $fields
                    ];
                }
            }
        }
        return $rule;
    }

    private function searchValue($rule,$txt_search)
    {
        $key = 'notfound';
        foreach ($rule as $k => $v) {
            if($v['type'] == $txt_search){
                $key = $k;
                break;
            }
        }
        return $key;
    }

    private function formatMultiInput($inputs)
    {
        foreach ($inputs as $k => $v) {

            foreach ($v as $key => $value) {
                $inputs[$k."_".$key] = $value;
                unset($inputs[$k]);
            }
        }
        return $inputs;
    }



}