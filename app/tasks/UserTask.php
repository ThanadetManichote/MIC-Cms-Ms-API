<?php
use Phalcon\DI;
use Phalcon\DI\InjectionAwareInterface;
use Phalcon\Cli\Task;
use Phalcon\DiInterface;

class UserTask extends Task implements InjectionAwareInterface
{
    //====== Start: Define parameter =======//
    // public $di;
    //====== End: Define parameter =======//

    //====== Start: Support Method =======//
    //Method for get filename
    protected function getFilenameFromParam($params)
    {
        //Default filename   
        $filename = date('Y-m-d').".txt";
        if (!empty($params) && isset($params[0])) {
            $filename = $params[0];
            
        }
        return $filename;
    }

    //Method for get fullpath (file import)
    protected function getImportFileFullPath($filename)
    {
        return $this->config->import->path.$filename;
    }

    //Method for get user repository
    protected function getUserRepository()
    {
        return $this->repository->getRepository("UserRepository");
    }

    protected function getTypeRepository()
    {
        return $this->repository->getRepository("TypeRepository");
    }

    //Method for create parameter for import user
    protected function createParameterForImport($datas, $contentId)
    {
        //Define outputs
        return [
            'username'     => $datas[0],
            'password'     => $datas[1],
            'content_type' => $datas[2],
            'content_id'   => $contentId,
            'status'       => 'active',
        ];
    }

    //Method for create parameter for import sale account
    protected function createParameterForImportAccount($datas)
    {
        //Define outputs
        $outputs = [];

        switch ($datas[2]) {
            case 'sale': 
                $outputs = [
                    'region'    => $datas[3],
                    'code'      => $datas[4],
                    'firstname' => $datas[5],
                    'lastname'  => $datas[6],
                    'team'      => $datas[7],
                ];
                break;
        }
        
        return $outputs;
    }

    //Method for create account by type
    protected function createAccoutDataByType($datas)
    {
        //create params
        $params = $this->createParameterForImportAccount($datas);

        //create account in service
        return $this->saleService->createAccount($params);

    }

    //Method for manage import user from file
    protected function importUserData($filename)
    {
        $outputs = [
            'success' => true,
            'message' => '',
            'error'   => []
        ];

        if (empty($filename)) {
            $outputs['success'] = false;
            $outputs['message'] = $this->message->cli->fileNotFound;
            return $outputs;
        }

        //get full path
        $fullpath = $this->getImportFileFullPath($filename);
        //check have file
        if (!$this->checkHaveFile($fullpath)) {
           $outputs['success'] = false;
           $outputs['message'] = $this->message->cli->fileNotFound;
           return $outputs; 
        }


        if (($handle = fopen($fullpath, "r")) !== FALSE) {
            //get user repo
            $userRepo  = $this->getUserRepository();
            
            while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
                
                //check user duplicate
                if ($userRepo->checkDuplicateUsername($data[0])) {
                    $outputs['error'][] = $this->message->cli->duplicate." ( ".implode("|", $data)." )";
                    continue;
                }

                //create account data by type
                $res = $this->createAccoutDataByType($data);
                if (!$res['success']) {
                    $outputs['error'][] = $res['message']." ( ".implode("|", $data)." )";
                    continue;
                }
                
                //get account id
                $contentId = $res['data']['id'];
                
                //create paremeter for import
                $params = $this->createParameterForImport($data, $contentId);
                
                //add data
                $res    = $userRepo->addUser($params);
                if (!$res['success']) {
                    $outputs['error'][] = $res['message']." ( ".implode("|", $data)." )";
                }
            }
            fclose($handle);
        } else {
            $outputs['success'] = false;
            $outputs['message'] = $this->message->cli->fileCannotOpen;
        }
        
        
        return $outputs;
    }

    //Method for print import error record
    protected function printImportErrorRecord($result)
    {
        echo "=== Show Import Result ===\n";
        if (!$result['success']) {
            echo "Error : ".$result['message']."\n";
        }

        $errors = $result['error'];
        
        if (!empty($errors)) {
            echo "*** Error record ***\n";
            foreach ($errors as $error) {
                echo $error."\n";
            }
        } else {
            echo "*** Finish ***\n";
        }
        return true;
    }

    //Method for check extension file
    protected function checkExtension($filename)
    {
        $files = explode(".", $filename);
        if (end($files) != "txt") {
            return false;
        }
        return true;
    }

    //Method for check have file 
    protected function checkHaveFile($fullpath)
    {
        if (file_exists($fullpath)) {
            return true;
        }
        return false;
    }
    //====== End: Support Method  =======//

    //====== Start: Main Method =======//
    //Method for import user
    public function importAction(array $params)
    {
        $output   = true;
        
        //get filename
        $filename = $this->getFilenameFromParam($params);

        //check file extension
        if (!$this->checkExtension($filename)) {
            echo "File not support!\n";
            return false;
        }

        $res = $this->importUserData($filename);
        if (!$res['success']) {
            return false;
        }
        //print error record
        $this->printImportErrorRecord($res);
        
        return $output;
    }
    //====== End: Main Method  =======//
}