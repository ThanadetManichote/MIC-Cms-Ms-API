<?php
namespace App\Repositories;

use App\Models\Contents;

class ContentRepository extends \Phalcon\Mvc\Micro {

    //------- start : Define variable ----------//
    protected $allowFilter = ['id','last_name','first_name'];
    //------- end : Define variable ----------//

    //------- start: protected method ------------//
    //Method for get user model
    protected function getContentModel()
    {
        return $this->model->getModel('Contents');
    }

    protected function getDataByParams($params,$headers)
    {
        $language = $this->langsetting($headers);
        //Create conditions
        $conditions  = $this->mongoService->createConditionFilter($params, $this->allowFilter);
        //create model
        $model       = $this->getContentModel();
        
        $filterCon   = [$conditions];
        //Manage order
        $filterCon   = $this->mongoService->manageOrderInParams($params, $filterCon, $this->allowFilter);

        //set limit
        if(isset($params['page']['limit'])){
            $params['page']['limit'] = $params['page']['limit'];
        }else{
            $params['page']['limit'] = 10;
        }

        //query data
        $total = 0;
        if ($params['page']['limit'] == 'all') {
            $contents = $model->find($filterCon);
            $total = count($contents);
        } else {
            $total     = $model->count([$conditions]);
            $filterCon = $this->mongoService->manageLimitOffsetInParams($params['page'], $filterCon);
            $contents     = $model->find($filterCon);
        }
        $contents = $this->mongoService->graphFormatData($contents,$language);

        return [$contents,$total,$links];
    }

    public function createlinks($limit,$totalRecord)
    {
        if($limit>=$totalRecord){
            $links['self'] = "http://mic-cms-api.dev:8107/content";
            $links['next'] = "";
            $links['last'] = "";
        }else{
            $totalpage = $totalRecord/$limit;
            $offset = $totalpage*$limit;
            $links['self'] = "http://mic-cms-api.dev:8107/content";
            $links['next'] = "http://mic-cms-api.dev:8107/content?page[offset]=2";
            $links['last'] = "http://mic-cms-api.dev:8107/content?page[offset]=".$offset;
        }
        return $links;
    }

    

    //Method get array lang
    public function langsetting($params)
    {
        if(isset($params['Language']) && $params['Language']){
            $lang = explode("|",$params['Language']);
        }else{
            $lang = ['th','en','zh'];
        }
        return $lang;
    }


    //Method for get content by filter
    public function getContent($params,$headers)
    {
        //Define output
        $outputs = [
            'success' => true,
            'message' => '',
        ];

        try {
            // create filter
            $contents           = $this->getDataByParams($params,$headers);
            $outputs['links'] = $contents[2];
            $outputs['data'] = $contents[0];

            if (isset($params['limit'])) {
                //get total record
                $outputs['totalRecord'] = $contents[1];
            }

        } catch (\Exception $e) {
            $outputs['success'] = false;
            $outputs['message'] = 'missionFail';
        }



        return $outputs;
    }

    //Method for get content detail by id (list)
    public function getContentDetail($params,$headers)
    {
        $language = $this->langsetting($headers);
        //Define output
        $outputs = [
            'success' => true,
            'message' => '',
        ];

        try { 
            //get data
            $contents           = $this->getDetailDataById($params['id']);

            $contents           = $this->mongoService->graphFormatData($contents,$language);

            $contents           = $this->mongoService->graphManageSortDataByIdList($contents, $params['id'],$language);
            
            $outputs['data'] = $contents;

        } catch (\Exception $e) {
            $outputs['success'] = false;
            $outputs['message'] = 'missionFail';
        }
        

        return $outputs;    
    }


    //Method for get category detail
    protected function getDetailDataById($id)
    {
        //create model
        $model = $this->getContentModel();
        $contents = $this->mongoService->getDetailDataById($model, $id, $this->allowFilter);
        return $contents;
    }







}








