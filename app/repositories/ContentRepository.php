<?php
namespace App\Repositories;

use App\Models\Contents;

class ContentRepository extends \Phalcon\Mvc\Micro {

    //------- start : Define variable ----------//
    protected $allowFilter = ['name'];
    //------- end : Define variable ----------//

    //------- start: protected method ------------//
    //Method for get user model
    protected function getContentModel()
    {
        return $this->model->getModel('Contents');
    }

    protected function getDataByParams($params)
    {
        $language = $this->langsetting($params);
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
        $contents = $this->mongoformatid($contents);
        $links = $this->createlinks($params['page']['limit'],$total);

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

    public function mongoformatid($contents)
    {
        if(count($contents)>0){
            foreach ($contents as $k => $v) {
                $contents[$k]->id = (string)$v->_id;
                if(isset($v->first_name)){
                    $contents[$k]->attributes->first_name = $v->first_name;
                    unset($v->first_name);
                }

                if(isset($v->last_name)){
                    $contents[$k]->attributes->last_name = $v->last_name;
                    unset($v->last_name);
                }
                $contents[$k]->relationships = (object)[];
                unset($v->_id);
            }
        }        
        return $contents;
    }

    //Method get array lang
    public function langsetting($params)
    {
        if(isset($params['language']) && $params['language']){
            $lang = explode("|",$params['language']);
        }else{
            $lang = [];
        }
        return $lang;
    }


    //Method for get content by filter
    public function getContent($params)
    {
        //Define output
        $outputs = [
            'success' => true,
            'message' => '',
        ];

        // try {
            // create filter
            $contents           = $this->getDataByParams($params);
            $outputs['links'] = $contents[2];
            $outputs['data'] = $contents[0];

            if (isset($params['limit'])) {
                //get total record
                $outputs['totalRecord'] = $contents[1];
            }

        // } catch (\Exception $e) {
        //     $outputs['success'] = false;
        //     $outputs['message'] = 'missionFail';
        // }



        return $outputs;
    }







}








