<?php

use Phalcon\DI;

use App\Repositories\UserRepository;

class UserRepositoryTest extends UnitTestCase
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
    public function testGetUserModel()
    {
        //Mock model
        $model = Mockery::mock('Model');
        $model->shouldReceive('getModel')->andReturn("TEST");

        //register model
        $this->di->set('model', $model, true);

        //create class
        $repo = new UserRepository();

        //call method
        $result = $this->callMethod(
            $repo,
            'getUserModel',
            []
        );

        //check result
        $this->assertEquals($result, 'TEST');
    }

    public function testGetDataByParamsNoLimit()
    {
        //mock mongoService
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('createConditionFilter')->andReturn([
            'name' => ['$regex' => '^Tes']
        ]);
        $mongoService->shouldReceive('manageOrderInParams')->andReturn([[
            'name' => ['$regex' => '^Tes']
        ]]);
        
        //register mongoService
        $this->di->set('mongoService', $mongoService, true);

        //Mock model
        $model = Mockery::mock('Model');
        $model->shouldReceive('find')->andReturn([
            [
                "username"   => "Tester",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58eb5ab69aaf84001429e402",
            ]
        ]);


        //create repo
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                           ->setMethods([
                                'getUserModel'])
                           ->getMock();

        $repo->method('getUserModel')
                   ->willReturn($model);

        //create params
        $params = [['name' => 'Tes%'], 'en'];

        //call method
        $result = $this->callMethod(
            $repo,
            'getDataByParams',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertInternalType('array', $result[0]);
        $this->assertInternalType('integer', $result[1]);
        $this->assertEquals(1, $result[1]);
        $this->assertEquals('58eb5ab69aaf84001429e402', $result[0][0]['id']);
        $this->assertEquals('Tester', $result[0][0]['username']);
    }

    public function testGetDataByParamsHaveLimit()
    {
        //mock mongoService
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('createConditionFilter')->andReturn([
            'username' => ['$regex' => '^Tes']
        ]);
        $mongoService->shouldReceive('manageLimitOffsetInParams')->andReturn([
            'username' => ['$regex' => '^Tes'], 'limit' => 3, 'skip' => 0
        ]);
        $mongoService->shouldReceive('manageOrderInParams')->andReturn([[
            'username' => ['$regex' => '^Tes'], 'limit' => 3, 'skip' => 0
        ]]);

        //register mongoService
        $this->di->set('mongoService', $mongoService, true);

        //Mock model
        $model = Mockery::mock('Model');
        $model->shouldReceive('find')->andReturn([
            [
                "username"   => "Tester",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58eb5ab69aaf84001429e402",
            ]
        ]);

        $model->shouldReceive('count')->andReturn(1);


        //create repo
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                           ->setMethods([
                                'getUserModel'])
                           ->getMock();
                           
        $repo->method('getUserModel')
                   ->willReturn($model);

        //create params
        $params = [['name' => 'Tes%', 'limit' => 3]];

        //call method
        $result = $this->callMethod(
            $repo,
            'getDataByParams',
            $params
        );

        //check result
        $this->assertInternalType('array', $result);
        $this->assertInternalType('array', $result[0]);
        $this->assertInternalType('integer', $result[1]);
        $this->assertEquals(1, $result[1]);
        $this->assertEquals('58eb5ab69aaf84001429e402', $result[0][0]['id']);
        $this->assertEquals('Tester', $result[0][0]['username']);
    }

    public function testGetDetailDataById()
    {
        //Mock user
        $user = Mockery::mock('User');

        //Mock mongoService
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('getDetailDataById')->andReturn($user);

        //register repository
        $this->di->set('mongoService', $mongoService, true);

        //create repo
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                           ->setMethods([
                                'getUserModel'])
                           ->getMock();
                           
        $repo->method('getUserModel')
                   ->willReturn($user);


        //create params
        $params = ['58e27db58d6a71405dbbdb32'];

        //call method
        $result = $this->callMethod(
            $repo,
            'getDetailDataById',
            $params
        );

        //check result
        $this->assertInternalType('object', $result);
        $this->assertNotNull($result);
    }

    // public function testGetTypeById()
    // {
    //     //Mock Type repository
    //     $typeRepo = Mockery::mock('TypeRepository');
    //     $typeRepo->shouldReceive('getTypeById')->andReturn([
    //         'id'   => '58e27db58d6a71405dbbdb32',
    //         'name' => 'Test'
    //     ]);

    //     //Mock repository
    //     $repository = Mockery::mock('Repository');
    //     $repository->shouldReceive('getRepository')->andReturn($typeRepo);

    //     //register repository
    //     $this->di->set('repository', $repository, true);

    //     //create repo
    //     $repo = new UserRepository();

    //     //create params
    //     $params = ['58e27db58d6a71405dbbdb32'];

    //     //call method
    //     $result = $this->callMethod(
    //         $repo,
    //         'getTypeById',
    //         $params
    //     );

    //     //check result
    //     $this->assertInternalType('array', $result);
    //     $this->assertArrayHasKey('id', $result);
    //     $this->assertArrayHasKey('name', $result);
    // }

    public function testCheckDuplicateNotDup()
    {
        //create class
        $repo = new UserRepository();

        $model  = Mockery::mock('Types');
        $model->shouldReceive('find')->andReturn(null);

        $params = [$model, ['username' => 'Test']];

        //call method
        $result = $this->callMethod(
            $repo,
            'checkDuplicate',
            $params
        );

        //check result
        $this->assertFalse($result);
    }

    public function testCheckDuplicateDup()
    {
        $user = Mockery::mock('User');
        //create class
        $repo = new UserRepository();

        $model  = Mockery::mock('Model');
        $model->shouldReceive('find')->andReturn($user);

        $params = [$model, ['username' => 'Test']];

        //call method
        $result = $this->callMethod(
            $repo,
            'checkDuplicate',
            $params
        );

        //check result
        $this->assertTrue($result);
    }

    public function testCheckDuplicateForUpdateNotDup()
    {
        $user = Mockery::mock('User');
        $user->_id = '58abd2f22f8331000a3acb92';

        //create class
        $repo = new UserRepository();
        
        //Mock model
        $model  = Mockery::mock('Model');
        $model->shouldReceive('find')->andReturn([$user]);

        $params = [$model, '58abd2f22f8331000a3acb92', ['id' => '58abd2f22f8331000a3acb92', 'username' => 'Test']];

        //call method
        $result = $this->callMethod(
            $repo,
            'checkDuplicateForUpdate',
            $params
        );

        //check result
        $this->assertFalse($result);
    }

    public function testCheckDuplicateForUpdateDup()
    {
        $user = Mockery::mock('User');
        $user->_id = '58abd2f22f8331000a3acb91';

        //create class
        $repo = new UserRepository();

        $model  = Mockery::mock('Model');
        $model->shouldReceive('find')->andReturn([$user]);

        $params = [$model, '58abd2f22f8331000a3acb92', ['id' => '58abd2f22f8331000a3acb92', 'username' => 'Test']];

        //call method
        $result = $this->callMethod(
            $repo,
            'checkDuplicateForUpdate',
            $params
        );

        //check result
        $this->assertTrue($result);
    }

    public function testEncryptPasswordNoPass()
    {
        $repo = new UserRepository();
        //call method
        $result = $this->callMethod(
            $repo,
            'encryptPassword',
            ['']
        );

        //check result
        $this->assertEmpty($result);
    }

    public function testEncryptPasswordHavePass()
    {
        //Mock security
        $security = Mockery::mock('Security');
        $security->shouldReceive('hash')->andReturn('hsporgkrd045ysg%yg445ergjspo5ijgrei');
        //register security
        $this->di->set('security', $security, true);

        $repo = new UserRepository();
        //call method
        $result = $this->callMethod(
            $repo,
            'encryptPassword',
            ['password']
        );

        //check result
        $this->assertNotEmpty($result);
        $this->assertEquals('hsporgkrd045ysg%yg445ergjspo5ijgrei', $result);
    }

    public function testInsertDataError()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("save")->andReturn(false);

        //create class
        $repo = new UserRepository();

        $params = [$model, [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58abd2f22f8331000a3acb92',
            'status'       => 'active',
        ]];
        //call method
        $result = $this->callMethod(
            $repo,
            'insertData',
            $params
        );

        //check result
        $this->assertNull($result);
    }

    public function testInsertDataSuccess()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("save")->andReturn(true);

        //create class
        $repo = new UserRepository();

        $params = [$model, [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58abd2f22f8331000a3acb92',
            'status'       => 'active',
        ]];
        //call method
        $result = $this->callMethod(
            $repo,
            'insertData',
            $params
        ); 

        //check result
        $this->assertNotNull($result);
    }

    public function testUpdateDataError()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("save")->andReturn(false);

        //create class
        $repo = new UserRepository();

        $params = [$model, [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58abd2f22f8331000a3acb92',
            'status'       => 'active',
        ]];
        //call method
        $result = $this->callMethod(
            $repo,
            'updateData',
            $params
        );

        //check result
        $this->assertNull($result);
    }

    public function testUpdateDataSuccess()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("save")->andReturn(true);

        //create class
        $repo = new UserRepository();

        $params = [$model, [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58abd2f22f8331000a3acb92',
            'status'       => 'active',
        ]];
        //call method
        $result = $this->callMethod(
            $repo,
            'updateData',
            $params
        ); 

        //check result
        $this->assertNotNull($result);
    }

    public function testUpdatePasswordError()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("save")->andReturn(false);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['encryptPassword'])
                    ->getMock();

        $repo->method('encryptPassword')
            ->willReturn('4t596hyrdoidrjh0iy45o-trdjdog');

        $params = [$model, 'password'];
        //call method
        $result = $this->callMethod(
            $repo,
            'updatePassword',
            $params
        ); 

        //check result
        $this->assertNull($result);
    }

    public function testUpdatePasswordSuccess()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("save")->andReturn(true);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['encryptPassword'])
                    ->getMock();

        $repo->method('encryptPassword')
            ->willReturn('4t596hyrdoidrjh0iy45o-trdjdog');

        $params = [$model, 'password'];
        //call method
        $result = $this->callMethod(
            $repo,
            'updatePassword',
            $params
        ); 

        //check result
        $this->assertNotNull($result);
    }

    public function testDeleteDataError()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("delete")->andReturn(false);

        //create class
        $repo = new UserRepository();

        $params = [$model];
        //call method
        $result = $this->callMethod(
            $repo,
            'deleteData',
            $params
        );

        //check result
        $this->assertNull($result);
    }

    public function testDeleteDataSuccess()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive("delete")->andReturn(true);

        $model->_id = '58abd2f22f8331000a3acb91';

        //create class
        $repo = new UserRepository();

        $params = [$model];
        //call method
        $result = $this->callMethod(
            $repo,
            'deleteData',
            $params
        ); 

        //check result
        $this->assertEquals('58abd2f22f8331000a3acb91', $result);
    }

    public function testCheckPassword()
    {
        //Mock security
        $security = Mockery::mock('Security');
        $security->shouldReceive('checkHash')->andReturn(true);
        //register security
        $this->di->set('security', $security, true);

        $repo = new UserRepository();
        //call method
        $result = $this->callMethod(
            $repo,
            'checkPassword',
            ['srgsergjrkhm0g-e[kgmldfpg', 'password']
        );

        //check result
        $this->assertTrue($result);
    }

    public function testManageLoginAdmin()
    {
        //TODO : this function not complete (use AD)
        //Mock user
        $users = [[
            "username"     => "Test1",
            "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
            "content_type" => "sale",
            "content_id"   => "58eb50b59aaf8400135e1f12",
            "status"       => "active",
            "created_at"   => "2017-04-10 17:13:10",
            "updated_at"   => "2017-04-10 18:17:46",
            "id"           => "58eb5ab69aaf84001429e402"
        ]];

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['checkPassword'])
                    ->getMock();

        $repo->method('checkPassword')
            ->willReturn(true);

         //call method
        $result = $this->callMethod(
            $repo,
            'manageLogin',
            [$users, 'password']
        );
        //check result
        $this->assertTrue($result[0]);
        $this->assertInternalType('array', $result[1]);
        $this->assertArrayHasKey('username', $result[1]);
        $this->assertArrayHasKey('content_type', $result[1]);
        $this->assertArrayHasKey('content_id', $result[1]);
        $this->assertArrayHasKey('status', $result[1]);
    }

    public function testManageLoginMerchant()
    {
        //TODO : this function not complete (use AD)
        //Mock type
        $type = Mockery::mock('Types');
        $type->name = "Merchant";

        //Mock user
        $users = [[
            "username"     => "Test1",
            "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
            "content_type" => "sale",
            "content_id"   => "58eb50b59aaf8400135e1f12",
            "status"       => "active",
            "created_at"   => "2017-04-10 17:13:10",
            "updated_at"   => "2017-04-10 18:17:46",
            "id"           => "58eb5ab69aaf84001429e402"
        ]];

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getTypeById', 'checkPassword'])
                    ->getMock();

        $repo->method('getTypeById')
            ->willReturn($type);

        $repo->method('checkPassword')
            ->willReturn(true);

         //call method
        $result = $this->callMethod(
            $repo,
            'manageLogin',
            [$users, 'password']
        );

        //check result
        $this->assertTrue($result[0]);
        $this->assertInternalType('array', $result[1]);
        $this->assertArrayHasKey('username', $result[1]);
        $this->assertArrayHasKey('content_type', $result[1]);
        $this->assertArrayHasKey('content_id', $result[1]);
        $this->assertArrayHasKey('status', $result[1]);
    }

    public function testGetUserById()
    {
        $user = Mockery::mock('User');
        //mock model
        $model = Mockery::mock('Model');
        $model->shouldReceive("findById")->andReturn($user);
        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        //call method
        $result = $repo->getUserById('58abd2f22f8331000a3acb91');

        //check result
        $this->assertNotNull($result);
    }

    public function testGetUserException()
    {
        //create class
        $repo = new UserRepository();

        //call method 
        $result = $repo->getUser([]);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertFalse($result['success']);
        $this->assertEquals('missionFail', $result['message']);

    }

    public function testGetUserSuccessNoLimit()
    {
        //Mock type
        $user = Mockery::mock('Type');

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('getAllIdFromDatas')->andReturn('58eb5ab69aaf84001429e402');

        //register model
        $this->di->set('mongoService', $mongoService, true);
        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getDataByParams'])
                    ->getMock();

        $repo->method('getDataByParams')
            ->willReturn([$user, 1]);

        //call method 
        $result = $repo->getUser([]);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('string', $result['data']);
        $this->assertEquals('58eb5ab69aaf84001429e402', $result['data']);

    }

    public function testGetUserSuccessHaveLimit()
    {
        //Mock type
        $user = Mockery::mock('User');

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('getAllIdFromDatas')->andReturn('58eb5ab69aaf84001429e402');

        //register model
        $this->di->set('mongoService', $mongoService, true);
        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getDataByParams'])
                    ->getMock();

        $repo->method('getDataByParams')
            ->willReturn([$user, 1]);

        //call method 
        $result = $repo->getUser(['limit' => 2]);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('string', $result['data']);
        $this->assertEquals('58eb5ab69aaf84001429e402', $result['data']);
        $this->assertArrayHasKey('totalRecord', $result);
        $this->assertEquals(1, $result['totalRecord']);

    }

    public function testGetUserDetailException()
    {
        //create class
        $repo = new UserRepository();

        //call method 
        $result = $repo->getUserDetail([]);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertFalse($result['success']);
        $this->assertEquals('missionFail', $result['message']);

    }

    public function testGetUserDetail()
    {
        //Mocke category
        $user = Mockery::mock('Users');

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('addIdTodata')->andReturn([[
            "username"   => "Test1",
            "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
            "type_id"    => "58eb50b59aaf8400135e1f12",
            "status"     => "active",
            "created_at" => "2017-04-10 17:13:10",
            "updated_at" => "2017-04-10 18:17:46",
            "id"         => "58eb5ab69aaf84001429e402"
        ]]);
        $mongoService->shouldReceive('manageSortDataByIdList')->andReturn([[
            "username"   => "Test1",
            "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
            "type_id"    => "58eb50b59aaf8400135e1f12",
            "status"     => "active",
            "created_at" => "2017-04-10 17:13:10",
            "updated_at" => "2017-04-10 18:17:46",
            "id"         => "58eb5ab69aaf84001429e402"
        ]]);

        //register model
        $this->di->set('mongoService', $mongoService, true);

        //create class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                           ->setMethods([
                                'getDetailDataById'
                            ])
                           ->getMock();
                           
        $repo->method('getDetailDataById')
                   ->willReturn($user);

        //call method 
        $result = $repo->getUserDetail(['id' => '58eb5ab69aaf84001429e402']);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data'][0]);
        $this->assertArrayHasKey('type_id', $result['data'][0]);
        $this->assertArrayHasKey('username', $result['data'][0]);
        $this->assertArrayHasKey('status', $result['data'][0]);

    }

    public function testAddUserTypeError()
    {
        //create class
        $repo = new UserRepository();

        $params = [
            'username'     => 'Test',
            'content_type' => 'xxx',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active',
        ];
        //call method 
        $result = $repo->addUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertFalse($result['success']);
        $this->assertEquals('typeNotFound', $result['message']);
    }

    public function testAddUserDuplicate()
    {
        //mock model
        $model = Mockery::mock('Users');

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicate'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('checkDuplicate')
            ->willReturn(true);

        $params = [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active',
        ];
        //call method
        $result = $repo->addUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('dataDuplicate', $result['message']);
    }

    public function testAddUserInsertFail()
    {
        //mock model
        $model = Mockery::mock('Users');

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicate', 'insertData'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('checkDuplicate')
            ->willReturn(false);

        $repo->method('insertData')
            ->willReturn(null);

        $params = [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active',
        ];
        //call method
        $result = $repo->addUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('insertError', $result['message']);
    }

    public function testAddUserSuccess()
    {
        //mock user
        $user  = Mockery::mock('Users');
        //mock model
        $model = Mockery::mock('Model');

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('addIdTodata')->andReturn(
                [
                    "username"     => "Test1",
                    "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "content_type" => "sale",
                    "content_id"   => "58eb50b59aaf8400135e1f12",
                    "status"       => "active",
                    "created_at"   => "2017-04-10 17:13:10",
                    "updated_at"   => "2017-04-10 18:17:46",
                    "id"           => "58eb5ab69aaf84001429e402"
                ]
            );

        //register model
        $this->di->set('mongoService', $mongoService, true);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicate', 'insertData'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('checkDuplicate')
            ->willReturn(false);

        $repo->method('insertData')
            ->willReturn($user);

        $params = [
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active',
        ];
        //call method
        $result = $repo->addUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertArrayHasKey('username', $result['data']);
    }

    public function testEditUserTypeError()
    {
        //create class
        $repo = new UserRepository();
                           
        //call method 
        $result = $repo->editUser([
            'id'           => '58abd2f22f8331000a3acb91',
            'username'     => 'Test',
            'content_type' => 'xxx',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active'
        ]);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertFalse($result['success']);
        $this->assertEquals('typeNotFound', $result['message']);
    }

    public function testEditUserDataNotFound()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn(null);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $params = [
            'id'           => '58abd2f22f8331000a3acb91',
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active'
        ];
        //call method
        $result = $repo->editUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('dataNotFound', $result['message']);
    }

    public function testEditUserDataDuplicate()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn([
                "username"     => "Test1",
                "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "content_type" => "sale",
                "content_id"   => "58eb50b59aaf8400135e1f12",
                "status"       => "active",
                "created_at"   => "2017-04-10 17:13:10",
                "updated_at"   => "2017-04-10 18:17:46",
                "id"           => "58abd2f22f8331000a3acb91"
            ]);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicateForUpdate'])
                    ->getMock();

        
        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('checkDuplicateForUpdate')
            ->willReturn(true);

        $params = [
            'id'           => '58abd2f22f8331000a3acb91',
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active'
        ];
        //call method
        $result = $repo->editUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('dataDuplicate', $result['message']);
    }

    public function testEditUserUpdateFail()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn([
                "username"     => "Test1",
                "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "content_type" => "sale",
                "content_id"   => "58eb50b59aaf8400135e1f12",
                "status"       => "active",
                "created_at"   => "2017-04-10 17:13:10",
                "updated_at"   => "2017-04-10 18:17:46",
                "id"           => "58abd2f22f8331000a3acb91"
            ]);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicateForUpdate', 'updateData'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('checkDuplicate')
            ->willReturn(false);

        $repo->method('updateData')
            ->willReturn(null);

        $params = [
            'id'           => '58abd2f22f8331000a3acb91',
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active'
        ];
        //call method
        $result = $repo->editUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('updateError', $result['message']);
    }

    public function testEditUserSuccess()
    {
        //mock type
        $type = Mockery::mock('Types');

        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn([
                "username"     => "Test1",
                "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "content_type" => "sale",
                "content_id"   => "58eb50b59aaf8400135e1f12",
                "status"       => "active",
                "created_at"   => "2017-04-10 17:13:10",
                "updated_at"   => "2017-04-10 18:17:46",
                "id"           => "58abd2f22f8331000a3acb91"
            ]);

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('addIdTodata')->andReturn(
                [
                    "username"     => "Test1",
                    "password"     => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "content_type" => "sale",
                    "content_id"   => "58eb50b59aaf8400135e1f12",
                    "status"       => "active",
                    "created_at"   => "2017-04-10 17:13:10",
                    "updated_at"   => "2017-04-10 18:17:46",
                    "id"           => "58abd2f22f8331000a3acb91"
                ]
            );

        //register model
        $this->di->set('mongoService', $mongoService, true);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicateForUpdate', 'updateData'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('checkDuplicateForUpdate')
            ->willReturn(false);

        $repo->method('updateData')
            ->willReturn([
                    "username"   => "Test",
                    "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "content_type" => "sale",
                    "content_id"   => "58eb50b59aaf8400135e1f12",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:13:10",
                    "updated_at" => "2017-04-10 18:17:46",
                    "id"         => "58abd2f22f8331000a3acb91"
                ]);

        $params = [
            'id'           => '58abd2f22f8331000a3acb91',
            'username'     => 'Test',
            'content_type' => 'sale',
            'content_id'   => '58eb50b59aaf8400135e1f12',
            'status'       => 'active'
        ];
        //call method
        $result = $repo->editUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertArrayHasKey('username', $result['data']);
    }

    public function testDeleteUserDataNotFound()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn(null);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $params = ['id' => '58abd2f22f8331000a3acb91'];
        //call method
        $result = $repo->deleteUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('dataNotFound', $result['message']);
    }

    public function testDeleteUserDeleteFail()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn([
                "username"   => "Test",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58abd2f22f8331000a3acb91"
            ]);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'deleteData'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('deleteData')
            ->willReturn(null);

        $params = ['id' => '58abd2f22f8331000a3acb91'];
        //call method
        $result = $repo->deleteUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('deleteError', $result['message']);
    }

    public function testDeleteUserSuccess()
    {
        //mock model
        $model = Mockery::mock('Users');
        $model->shouldReceive('findById')->andReturn([
                "username"   => "Test",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58abd2f22f8331000a3acb91"
            ]);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'deleteData'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($model);

        $repo->method('deleteData')
            ->willReturn('58abd2f22f8331000a3acb91');

        $params = ['id' => '58abd2f22f8331000a3acb91'];
        //call method
        $result = $repo->deleteUser($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('58abd2f22f8331000a3acb91', $result['data']);
    }

    public function testCheckLoginDataNotFound()
    {
        //mock users
        $users = [[], 0];

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getDataByParams'])
                    ->getMock();

        $repo->method('getDataByParams')
            ->willReturn($users);

        $params = [
            'username' => 'TestX',
            'password' => 'password',
        ];
        //call method
        $result = $repo->checkLogin($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('dataNotFound', $result['message']);
    }

    public function testCheckLoginFail()
    {
        //mock users
        $user = Mockery::mock('Users');
        $users = [[$user], 0];

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('addIdTodata')->andReturn([
                [
                    "username"   => "Test1",
                    "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "type_id"    => "58eb50b59aaf8400135e1f12",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:13:10",
                    "updated_at" => "2017-04-10 18:17:46",
                    "id"         => "58abd2f22f8331000a3acb91"
                ]
            ]);

        //register model
        $this->di->set('mongoService', $mongoService, true);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getDataByParams', 'manageLogin'])
                    ->getMock();

        $repo->method('getDataByParams')
            ->willReturn($users);

        $repo->method('manageLogin')
            ->willReturn([false]);

        $params = [
            'username' => 'Test1',
            'password' => 'password',
        ];
        //call method
        $result = $repo->checkLogin($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('loginFail', $result['message']);
    }

    public function testCheckLoginSuccess()
    {
        //mock users
        $user = Mockery::mock('Users');
        $users = [[$user], 0];

        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('addIdTodata')->andReturn([
                [
                    "username"   => "Test1",
                    "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "type_id"    => "58eb50b59aaf8400135e1f12",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:13:10",
                    "updated_at" => "2017-04-10 18:17:46",
                    "id"         => "58abd2f22f8331000a3acb91"
                ]
            ]);

        //register model
        $this->di->set('mongoService', $mongoService, true);

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getDataByParams', 'manageLogin'])
                    ->getMock();

        $repo->method('getDataByParams')
            ->willReturn($users);

        $repo->method('manageLogin')
            ->willReturn([true, [
                    "username"   => "Test1",
                    "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "type_id"    => "58eb50b59aaf8400135e1f12",
                    "type"       => "Merchant",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:13:10",
                    "updated_at" => "2017-04-10 18:17:46",
                    "id"         => "58abd2f22f8331000a3acb91"
                ]]);

        $params = [
            'username' => 'Test1',
            'password' => 'password',
        ];
        //call method
        $result = $repo->checkLogin($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertArrayHasKey('username', $result['data']);
        $this->assertArrayHasKey('type_id', $result['data']);
        $this->assertArrayHasKey('type', $result['data']);
    }

    public function testChangePasswordDataNotFound()
    {
        //mock users
        $users = null;

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserById'])
                    ->getMock();

        $repo->method('getUserById')
            ->willReturn($users);

        $params = [
            'id'  => '58abd2f22f8331000a3acb91',
            'old' => 'password',
            'new' => 'password1',
        ];
        //call method
        $result = $repo->changePassword($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('dataNotFound', $result['message']);

    }

    public function testChangePasswordOldPassNotMatch()
    {
        //mock users
        $user = Mockery::mock('Users');
        $user->password = 'nsjdfgokf0%f^$waoij4iejriovfdjerw';

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserById', 'checkPassword'])
                    ->getMock();

        $repo->method('getUserById')
            ->willReturn($user);

        $repo->method('checkPassword')
            ->willReturn(false);

        $params = [
            'id'  => '58abd2f22f8331000a3acb91',
            'old' => 'password',
            'new' => 'password1',
        ];
        //call method
        $result = $repo->changePassword($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('oldPasswordWrong', $result['message']);

    }

    public function testChangePasswordUpdateError()
    {
        //mock users
        $user = Mockery::mock('Users');
        $user->password = 'nsjdfgokf0%f^$waoij4iejriovfdjerw';

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserById', 'checkPassword', 'updatePassword'])
                    ->getMock();

        $repo->method('getUserById')
            ->willReturn($user);

        $repo->method('checkPassword')
            ->willReturn(true);

        $repo->method('updatePassword')
            ->willReturn(null);

        $params = [
            'id'  => '58abd2f22f8331000a3acb91',
            'old' => 'password',
            'new' => 'password1',
        ];
        //call method
        $result = $repo->changePassword($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('updateError', $result['message']);

    }

    public function testChangePasswordSuccess()
    {
        //Mock mongo services
        $mongoService = Mockery::mock('MongoService');
        $mongoService->shouldReceive('addIdTodata')->andReturn(
                [
                    "username"   => "Test1",
                    "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "type_id"    => "58eb50b59aaf8400135e1f12",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:13:10",
                    "updated_at" => "2017-04-10 18:17:46",
                    "id"         => "58abd2f22f8331000a3acb91"
                ]
            );

        //register model
        $this->di->set('mongoService', $mongoService, true);

        //mock users
        $user = Mockery::mock('Users');
        $user->password = 'nsjdfgokf0%f^$waoij4iejriovfdjerw';

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserById', 'checkPassword', 'updatePassword'])
                    ->getMock();

        $repo->method('getUserById')
            ->willReturn($user);

        $repo->method('checkPassword')
            ->willReturn(true);

        $repo->method('updatePassword')
            ->willReturn($user);

        $params = [
            'id'  => '58abd2f22f8331000a3acb91',
            'old' => 'password',
            'new' => 'password1',
        ];
        //call method
        $result = $repo->changePassword($params);

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('username', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
        $this->assertArrayHasKey('type_id', $result['data']);
        $this->assertArrayHasKey('status', $result['data']);

    }

    public function testCheckDuplicateUsername()
    {
        //mock users
        $user = Mockery::mock('Users');

        //creste class
        $repo = $this->getMockBuilder('App\Repositories\UserRepository')
                    ->setMethods(['getUserModel', 'checkDuplicate'])
                    ->getMock();

        $repo->method('getUserModel')
            ->willReturn($user);

        $repo->method('checkDuplicate')
            ->willReturn(true);

        //call method
        $result = $repo->checkDuplicateUsername('Tester');

        //check result
        $this->assertTrue($result);    
    }
    //------- end: Test function --------//
}