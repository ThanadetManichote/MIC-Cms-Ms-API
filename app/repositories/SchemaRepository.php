<?php
namespace App\Repositories;

use App\Models\Schemas;

class SchemaRepository extends \Phalcon\Mvc\Micro {

    //------- start : Define variable ----------//
    protected $allowFilter = ['name', 'type', 'attr'];
    //------- end : Define variable ----------//

    //------- start: protected method ------------//
    //Method for get user model
    protected function getSchemaModel()
    {
        return $this->model->getModel('Schemas');
    }

    protected function getDataByParams($params)
    {
        //Create conditions
        $conditions  = $this->mongoService->createConditionFilter($params, $this->allowFilter);

        //create model
        $model       = $this->getSchemaModel();
        
        $filterCon   = [$conditions];
        //Manage order
        $filterCon   = $this->mongoService->manageOrderInParams($params, $filterCon, $this->allowFilter);

        //query data
        $total = 0;
        if (!isset($params['limit'])) {
            $schemas = $model->find($filterCon);
            $total = count($schemas);
        } else {

            $total     = $model->count([$conditions]);
            $filterCon = $this->mongoService->manageLimitOffsetInParams($params, $filterCon);
            $schemas     = $model->find($filterCon);

            
        }
        return [$schemas, $total];
    }


    //Method for get schema by filter
    public function getSchema($params)
    {

        //Define output
        $outputs = [
            'success' => true,
            'message' => '',
        ];

        try {
            // create filter
            $schemas           = $this->getDataByParams($params);
            $outputs['data'] = $schemas[0];
            
            if (isset($params['limit'])) {
                //get total record
                $outputs['totalRecord'] = $schemas[1];
            }

        } catch (\Exception $e) {
            $outputs['success'] = false;
            $outputs['message'] = 'missionFail';
        }
        

        return $outputs;
    }

}