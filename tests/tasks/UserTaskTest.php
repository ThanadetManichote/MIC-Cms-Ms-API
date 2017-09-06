<?php

use Phalcon\DI;

require(__DIR__."/../../app/tasks/UserTask.php");

class UserTaskTest extends UnitTestCase
{
    //------ start: MOCK DATA ---------//
    
    //------ end: MOCK DATA ---------//


    //------- start: Method for support test --------//
    protected static function callMethod($obj, $name, array $args) {
        $class  = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
    //------- end: Method for support test --------//

    //------- start: Test function --------//
    public function testGetFilenameFromParamNoFile()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = [[]];

        //call method
        $result = $this->callMethod(
            $task,
            'getFilenameFromParam',
            $params
        );

        //check result
        $this->assertEquals(date('Y-m-d').".txt", $result);

    }

    public function testGetFilenameFromParamHaveFile()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = [[
            'test-2017-01-01.txt'
        ]];

        //call method
        $result = $this->callMethod(
            $task,
            'getFilenameFromParam',
            $params
        );

        //check result
        $this->assertEquals('test-2017-01-01.txt', $result);
    }

    public function testGetImportFileFullPath()
    {
        //create config
        $config = new \Phalcon\Config( [ 
            'import' => [
                'path' => '/data/import/'
            ]
        ] );

        //register config
        $this->di->set('config', $config, true);

        //create class
        $task = new UserTask();

        //create params
        $params = ['test-2017-01-01.txt'];

        //call method
        $result = $this->callMethod(
            $task,
            'getImportFileFullPath',
            $params
        );

        //check result
        $this->assertEquals('/data/import/test-2017-01-01.txt', $result);
        
    }

    public function testGetTypeRepository()
    {
        //Mock repository
        $repository = Mockery::mock('Repository');
        $repository->shouldReceive('getRepository')->andReturn("TEST");
        //register repository
        $this->di->set('repository', $repository, true);

        //create class
        $task = new UserTask();

        //call method
        $result = $this->callMethod(
            $task,
            'getTypeRepository',
            []
        );

        //check result
        $this->assertEquals('TEST', $result);
    }


    public function testGetUserRepository()
    {
        //Mock repository
        $repository = Mockery::mock('Repository');
        $repository->shouldReceive('getRepository')->andReturn("TEST");
        //register repository
        $this->di->set('repository', $repository, true);

        //create class
        $task = new UserTask();

        //call method
        $result = $this->callMethod(
            $task,
            'getUserRepository',
            []
        );

        //check result
        $this->assertEquals('TEST', $result);
    }

    public function testCreateParameterForImport()
    {
        //create params
        $params = [['tester', '123456', 'sale'], '58eb50ad9aaf840012332b02'];
        //create class
        $task = new UserTask();
        
        //call method
        $result = $this->callMethod(
            $task,
            'createParameterForImport',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertArrayHasKey('content_type', $result);
        $this->assertArrayHasKey('content_id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals('tester', $result['username']);
        $this->assertEquals('123456', $result['password']);
        $this->assertEquals('sale', $result['content_type']);
        $this->assertEquals('58eb50ad9aaf840012332b02', $result['content_id']);
        $this->assertEquals('active', $result['status']);
    }

    public function testCreateParameterForImportAccountForSale()
    {
        //create params
        $params = [['tester', '123456', 'sale', 'CDS', '1012521', 'firstname', 'lastname', 'Support']];
        //create class
        $task = new UserTask();
        
        //call method
        $result = $this->callMethod(
            $task,
            'createParameterForImportAccount',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('region', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('firstname', $result);
        $this->assertArrayHasKey('lastname', $result);
        $this->assertArrayHasKey('team', $result);
        $this->assertEquals('CDS', $result['region']);
        $this->assertEquals('1012521', $result['code']);
        $this->assertEquals('firstname', $result['firstname']);
        $this->assertEquals('lastname', $result['lastname']);
        $this->assertEquals('Support', $result['team']);
    }

    public function testCreateParameterForImportAccountForNotDeine()
    {
        //create params
        $params = [['tester', '123456', 'xxx', 'CDS', '1012521', 'firstname', 'lastname', 'Support']];
        //create class
        $task = new UserTask();
        
        //call method
        $result = $this->callMethod(
            $task,
            'createParameterForImportAccount',
            $params
        );

        //check result
        $this->assertEmpty($result);
    }

    public function testCreateAccoutDataByType()
    {
        $saleService = Mockery::mock('SaleService');
        $saleService->shouldReceive('createAccount')->andReturn([
                'success' => true,
                'message' => '',
                'data'    => [
                    'id'        => '58eb50ad9aaf840012332b02',
                    'region'    => 'CDS',
                    'code'      => '1012521',
                    'firstname' => 'firstname',
                    'lastname'  => 'lastname',
                    'team'      => 'Support',
                ]
            ]);
        //register sale service
        $this->di->set('saleService', $saleService, true);
        
        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['createParameterForImportAccount'])
                    ->getMock();

        $task->method('createParameterForImportAccount')
            ->willReturn([
                'region'    => 'CDS', 
                'code'      => '1012521', 
                'firstname' => 'firstname', 
                'lastname'  => 'lastname', 
                'team'      => 'Support', 
            ]);

        //create params
        $params = [['tester', '123456', 'xxx', 'CDS', '1012521', 'firstname', 'lastname', 'Support']];

        //call method
        $result = $this->callMethod(
            $task,
            'createAccoutDataByType',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
    }

    public function testImportUserDataNoFileName()
    {
        //mock message
        $message = new \Phalcon\Config( [ 
            'cli' => [
                'fileNotFound' => 'File not found'
            ]
        ] );

        //register config
        $this->di->set('message', $message, true);

        //create params
        $params = [''];
        //create class
        $task = new UserTask();
        
        //call method
        $result = $this->callMethod(
            $task,
            'importUserData',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('File not found', $result['message']);
    }

    public function testImportUserDataNotHaveFile()
    {
        //mock message
        $message = new \Phalcon\Config( [ 
            'cli' => [
                'fileNotFound' => 'File not found'
            ]
        ] );

        //register config
        $this->di->set('message', $message, true);

        //create params
        $params = ['test.txt'];

        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getImportFileFullPath', 'checkHaveFile'])
                    ->getMock();

        $task->method('getImportFileFullPath')
            ->willReturn('/data/import/test.txt');

        $task->method('checkHaveFile')
            ->willReturn(false);
        
        //call method
        $result = $this->callMethod(
            $task,
            'importUserData',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('File not found', $result['message']);
    }

    public function testImportUserDataUserDuplicate()
    {
        //Mock user repository
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive('checkDuplicateUsername')->andReturn(true);
        
        //mock message
        $message = new \Phalcon\Config( [ 
            'cli' => [
                'duplicate' => 'Data duplicate'
            ]
        ] );

        //register config
        $this->di->set('message', $message, true);

        //create params
        $params = ['test.txt'];

        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getImportFileFullPath', 'checkHaveFile', 'getUserRepository'])
                    ->getMock();

        $task->method('getImportFileFullPath')
            ->willReturn('test.txt');

        $task->method('checkHaveFile')
            ->willReturn(true);

        $task->method('getUserRepository')
            ->willReturn($userRepo);

        //call method
        $result = $this->callMethod(
            $task,
            'importUserData',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertInternalType('array', $result['error']);
        $this->assertEquals('Data duplicate ( tester1|123456|sale|CDS|1012521|firstname|lastname|Support )', $result['error'][0]);
    }

    public function testImportUserDataCreateAccoutError()
    {
        //Mock user repository
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive('checkDuplicateUsername')->andReturn(false);
        $userRepo->shouldReceive('addUser')->andReturn([
            'success' => false,
            'message' => 'Data duplicate'
        ]);
        

        //create params
        $params = ['test.txt'];

        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getImportFileFullPath', 'checkHaveFile', 'getUserRepository', 'createAccoutDataByType'])
                    ->getMock();

        $task->method('getImportFileFullPath')
            ->willReturn('test.txt');

        $task->method('checkHaveFile')
            ->willReturn(true);

        $task->method('getUserRepository')
            ->willReturn($userRepo);

        $task->method('createAccoutDataByType')
            ->willReturn([
                'success' => false,
                'message' => 'The region is required',
        ]);
        
        //call method
        $result = $this->callMethod(
            $task,
            'importUserData',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertInternalType('array', $result['error']);
        $this->assertEquals('The region is required ( tester1|123456|sale|CDS|1012521|firstname|lastname|Support )', $result['error'][0]);
    }

    public function testImportUserDataAddUserError()
    {
        //Mock user repository
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive('checkDuplicateUsername')->andReturn(false);
        $userRepo->shouldReceive('addUser')->andReturn([
            'success' => false,
            'message' => 'Insert fail'
        ]);
        

        //create params
        $params = ['test.txt'];

        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getImportFileFullPath', 'checkHaveFile', 'getUserRepository', 'createAccoutDataByType', 'createParameterForImport'])
                    ->getMock();

        $task->method('getImportFileFullPath')
            ->willReturn('test.txt');

        $task->method('checkHaveFile')
            ->willReturn(true);

        $task->method('getUserRepository')
            ->willReturn($userRepo);

        $task->method('createAccoutDataByType')
            ->willReturn([
                'success' => true,
                'message' => '',
                'data'    => [
                    'id' => '58eb50ad9aaf840012332b02'
                ]
            ]);

        $task->method('createParameterForImport')
            ->willReturn([
                'username'     => 'tester',
                'password'     => '123456',
                'content_type' => 'sale',
                'content_id'   => '58eb50ad9aaf840012332b02',
                'status'       => 'active',
        ]);
        
        //call method
        $result = $this->callMethod(
            $task,
            'importUserData',
            $params
        );


        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertInternalType('array', $result['error']);
        $this->assertEquals('Insert fail ( tester1|123456|sale|CDS|1012521|firstname|lastname|Support )', $result['error'][0]);
    }

    public function testImportUserDataSuccess()
    {
        //Mock user repository
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive('checkDuplicateUsername')->andReturn(false);
        $userRepo->shouldReceive('addUser')->andReturn([
            'success' => true,
            'message' => ''
        ]);

        //create params
        $params = ['test.txt'];

        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getImportFileFullPath', 'checkHaveFile', 'getUserRepository', 'createAccoutDataByType', 'createParameterForImport'])
                    ->getMock();

        $task->method('getImportFileFullPath')
            ->willReturn('test.txt');

        $task->method('checkHaveFile')
            ->willReturn(true);

        $task->method('getUserRepository')
            ->willReturn($userRepo);

        $task->method('createAccoutDataByType')
            ->willReturn([
                'success' => true,
                'message' => '',
                'data'    => [
                    'id' => '58eb50ad9aaf840012332b02'
                ]
            ]);

        $task->method('createParameterForImport')
            ->willReturn([
                'username'     => 'tester',
                'password'     => '123456',
                'content_type' => 'sale',
                'content_id'   => '58eb50ad9aaf840012332b02',
                'status'       => 'active',
        ]);
        
        //call method
        $result = $this->callMethod(
            $task,
            'importUserData',
            $params
        );


        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertInternalType('array', $result['error']);
        $this->assertEmpty($result['error']);
    }
    
    public function testPrintImportErrorRecordError()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = [[
            'success' => false,
            'message' => 'ERROR!!',
            'error'   => []
        ]];

        //call method
        $result = $this->callMethod(
            $task,
            'printImportErrorRecord',
            $params
        );

        //check result
        $this->assertTrue($result);
    }

    public function testPrintImportErrorRecordSomeProblem()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = [[
            'success' => true,
            'message' => '',
            'error'   => ['Row 1 Error']
        ]];

        //call method
        $result = $this->callMethod(
            $task,
            'printImportErrorRecord',
            $params
        );

        //check result
        $this->assertTrue($result);
    }
    
    public function testCheckExtensionNotTxt()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = ['test.csv'];

        //call method
        $result = $this->callMethod(
            $task,
            'checkExtension',
            $params
        );

        //check result
        $this->assertFalse($result);
    }

    public function testCheckExtensionTxt()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = ['test.txt'];

        //call method
        $result = $this->callMethod(
            $task,
            'checkExtension',
            $params
        );

        //check result
        $this->assertTrue($result);
    }
    
    public function testCheckHaveFileNoFile()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = ['test2.txt'];

        //call method
        $result = $this->callMethod(
            $task,
            'checkHaveFile',
            $params
        );

        //check result
        $this->assertFalse($result);
    }

    public function testCheckHaveFileHaveFile()
    {
        //create class
        $task = new UserTask();

        //create params
        $params = ['test.txt'];

        //call method
        $result = $this->callMethod(
            $task,
            'checkHaveFile',
            $params
        );

        //check result
        $this->assertTrue($result);
    }

    public function testImportActionFileNotSupport()
    {
        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getFilenameFromParam', 'checkExtension'])
                    ->getMock();

        $task->method('getFilenameFromParam')
            ->willReturn('test.txt');

        $task->method('checkExtension')
            ->willReturn(false);

        //call method
        $result = $task->importAction(['import', 'test.txt']);

        //check result
        $this->assertFalse($result);

    }

    public function testImportActionImportFail()
    {
        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getFilenameFromParam', 'checkExtension', 'importUserData'])
                    ->getMock();

        $task->method('getFilenameFromParam')
            ->willReturn('test.txt');

        $task->method('checkExtension')
            ->willReturn(true);

        $task->method('importUserData')
            ->willReturn(['success' => false]);

        //call method
        $result = $task->importAction(['import', 'test.txt']);

        //check result
        $this->assertFalse($result);

    }

    public function testImportActionImportSuccess()
    {
        //create class
        $task = $this->getMockBuilder('UserTask')
                    ->setMethods(['getFilenameFromParam', 'checkExtension', 'importUserData', 'printImportErrorRecord'])
                    ->getMock();

        $task->method('getFilenameFromParam')
            ->willReturn('test.txt');

        $task->method('checkExtension')
            ->willReturn(true);

        $task->method('importUserData')
            ->willReturn(['success' => true]);

        $task->method('printImportErrorRecord')
            ->willReturn(true);

        //call method
        $result = $task->importAction(['import', 'test.txt']);

        //check result
        $this->assertTrue($result);

    }

    
    //------- end: Test function --------//
}