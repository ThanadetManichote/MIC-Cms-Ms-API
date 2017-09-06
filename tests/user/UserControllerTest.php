<?php

use Phalcon\DI;

use App\Controllers\UserController;

class UserControllerTest extends UnitTestCase
{
    //------ start: MOCK DATA ---------//
    private $getDetailInputs = [
        'id' => '58eb5ab69aaf84001429e402,58eb5bc89aaf840010437512'
    ];

    private $createInputs = [
        'username' => 'Test',
        'password' => 'password',
        'type_id'  => '58eb50b59aaf8400135e1f12',
        'status'   => 'active',
    ];

    private $updateInputs = [
        'id'       => '58eb5ab69aaf84001429e402',
        'username' => 'Test1',
        'password' => 'password',
        'type_id'  => '58eb50b59aaf8400135e1f12',
        'status'   => 'active',
    ];

    private $deleteInputs = [
        'id'   => '58eb5ab69aaf84001429e402'
    ];

    private $loginInputs = [
        'username'   => 'Test',
        'password'   => 'password',
    ];

    private $changePassInputs = [
        'old' => 'password',
        'new' => 'password',
        'id'  => '58eb5ab69aaf84001429e402',
    ];
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
    public function testGetUserRepository()
    {
        //mock repository
        $repository = Mockery::mock('Repository');
        $repository->shouldReceive("getRepository")->andReturn("TEST");

        //register
        $this->di->set('repository', $repository, true);

        //create class
        $user = new UserController();
        
        //call method
        $result   = $this->callMethod(
                $user,
                'getUserRepository',
               []
            );

        //check result
        $this->assertEquals( $result, "TEST" );
    }

    public function testGetUserActionError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("getUser")->andReturn([
            'success' => false,
            'message' => 'missinFail',
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'getUserRepository',
                                'validateBussinessError'
                            ])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn([]);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad request',
                        ],
                        'error' => [
                            "message" =>  "Mission Fail"
                        ]
                    ]);

        //call method
        $result = $user->getUserAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Mission Fail');
    }

    public function testGetUserActionSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("getUser")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => '58abd2f22f8331000a3acb92',
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'getUserRepository',
                                'output'
                            ])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn([]);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data'    => '58abd2f22f8331000a3acb92'
                    ]);

        //call method
        $result = $user->getUserAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('string', $result['data']);
        $this->assertEquals('58abd2f22f8331000a3acb92', $result['data']);
    }

    public function testGetUserActionSuccessWithLimit()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("getUser")->andReturn([
            'success' => true,
            'message'     => '',
            'data'        => "58eb5ab69aaf84001429e402,58eb5bc89aaf840010437512,58ec4e299aaf840010437514",
            'totalRecord' => 6
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'getUserRepository',
                                'output'
                            ])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn(['limit' => 3, 'offset' => 0]);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data'  => "58eb5ab69aaf84001429e402,58eb5bc89aaf840010437512,58ec4e299aaf840010437514",
                        'total' => [
                            'limit'       => 3,
                            'offset'      => 0,
                            'totalRecord' => 6
                        ]
                    ]);

        //call method
        $result = $user->getUserAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('string', $result['data']);
        $this->assertEquals('58eb5ab69aaf84001429e402,58eb5bc89aaf840010437512,58ec4e299aaf840010437514', $result['data']);
        $this->assertArrayHasKey('total', $result);
        $this->assertInternalType('array', $result['total']);
        $this->assertArrayHasKey('limit', $result['total']);
        $this->assertArrayHasKey('offset', $result['total']);
        $this->assertArrayHasKey('totalRecord', $result['total']);
    }

    public function testGetUserDetailActionValidateError()
    {
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'validateApi',
                                'validateError'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn([]);

        $user->method('validateApi')
                   ->willReturn([
                        'msgError'   => 'The id is required',
                        'fieldError' => 'id'
                    ]);

        $user->method('validateError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'The id is required',
                            "property" => "id"
                        ]
                    ]);

        //call method
        $result = $user->getUserDetailAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertInternalType('array', $result['status']);
        $this->assertArrayHasKey('code', $result['status']);
        $this->assertArrayHasKey('message', $result['status']);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertInternalType('array', $result['error']);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'The id is required');
        $this->assertArrayHasKey('property', $result['error']);
        $this->assertEquals($result['error']['property'], 'id');
    }

    public function testGetUserDetailActionGetDataError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("getUserDetail")->andReturn([
            'success' => false,
            'message' => 'missionFail'
        ]);

        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'validateApi',
                                'getUserRepository',
                                'validateBussinessError'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn($this->getDetailInputs);

        $user->method('validateApi')
                   ->willReturn($this->getDetailInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'Mission Fail'
                        ]
                    ]);

        //call method
        $result = $user->getUserDetailAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertInternalType('array', $result['status']);
        $this->assertArrayHasKey('code', $result['status']);
        $this->assertArrayHasKey('message', $result['status']);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertInternalType('array', $result['error']);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Mission Fail');
    }

    public function testGetUserDetailActionGetDataSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("getUserDetail")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => [
                [
                    "username"   => "tester",
                    "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                    "type_id"    => "58eb50b59aaf8400135e1f12",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:13:10",
                    "updated_at" => "2017-04-10 18:17:46",
                    "id"         => "58eb5ab69aaf84001429e402"
                ],
                [
                    "username"   => "tester1",
                    "password"   => '$2y$08$WjhwemlaU1NOc0ZUVHVyNO7c2VPNbDqjQF1W9RcXl/JDLElo4D8nu',
                    "type_id"    => "58eb50ad9aaf840012332b02",
                    "status"     => "active",
                    "created_at" => "2017-04-10 17:17:44",
                    "updated_at" => "2017-04-10 17:19:47",
                    "id"         => "58eb5bc89aaf840010437512"
                ]
            ]
        ]);

        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'validateApi',
                                'getUserRepository',
                                'output'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn($this->getDetailInputs);

        $user->method('validateApi')
                   ->willReturn($this->getDetailInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data' => [
                            [
                                "username"   => "tester",
                                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                                "type_id"    => "58eb50b59aaf8400135e1f12",
                                "status"     => "active",
                                "created_at" => "2017-04-10 17:13:10",
                                "updated_at" => "2017-04-10 18:17:46",
                                "id"         => "58eb5ab69aaf84001429e402"
                            ],
                            [
                                "username"   => "tester1",
                                "password"   => '$2y$08$WjhwemlaU1NOc0ZUVHVyNO7c2VPNbDqjQF1W9RcXl/JDLElo4D8nu',
                                "type_id"    => "58eb50ad9aaf840012332b02",
                                "status"     => "active",
                                "created_at" => "2017-04-10 17:17:44",
                                "updated_at" => "2017-04-10 17:19:47",
                                "id"         => "58eb5bc89aaf840010437512"
                            ]
                        ]
                    ]);

        //call method
        $result = $user->getUserDetailAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertInternalType('array', $result['status']);
        $this->assertArrayHasKey('code', $result['status']);
        $this->assertArrayHasKey('message', $result['status']);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertCount(2, $result['data']);
    }

    public function testPostCreateActionValidateError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'validateError'])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn([]);

        $user->method('validateApi')
                   ->willReturn([
                        'msgError'   => 'The username is required',
                        'fieldError' => 'username'
                    ]);

        $user->method('validateError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad request',
                        ],
                        'error' => [
                            'message'  => 'The username is required',
                            'property' => 'username',
                        ]
                    ]);

        //call method
        $result = $user->postCreateAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'The username is required');
        $this->assertArrayHasKey('property', $result['error']);
        $this->assertEquals($result['error']['property'], 'username');
    }

    public function testPostCreateActionError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("addUser")->andReturn([
            'success' => false,
            'message' => 'insertError'
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'getUserRepository',
                                'validateBussinessError'])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->createInputs);

        $user->method('validateApi')
                   ->willReturn($this->createInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'Insert Error',
                        ]
                    ]);

        //call method
        $result = $user->postCreateAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Insert Error');
    }

    public function testPostCreateActionSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("addUser")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => [
                "username"   => "Test",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58eb5ab69aaf84001429e402"
            ]
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'getUserRepository',
                                'validateApi',
                                'output'
                            ])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->createInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateApi')
                   ->willReturn($this->createInputs);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data' => [
                            "username"   => "Test",
                            "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                            "type_id"    => "58eb50b59aaf8400135e1f12",
                            "status"     => "active",
                            "created_at" => "2017-04-10 17:13:10",
                            "updated_at" => "2017-04-10 18:17:46",
                            "id"         => "58eb5ab69aaf84001429e402"
                        ]
                    ]);

        //call method
        $result = $user->postCreateAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
    }

    public function testPutUpdateActionValidateError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'validateError'])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn([]);

        $user->method('validateApi')
                   ->willReturn([
                        'msgError'   => 'The username is required',
                        'fieldError' => 'username'
                    ]);

        $user->method('validateError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad request',
                        ],
                        'error' => [
                            'message'  => 'The username is required',
                            'property' => 'username',
                        ]
                    ]);

        //call method
        $result = $user->putUpdateAction('58e27db58d6a71405dbbdb32');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'The username is required');
        $this->assertArrayHasKey('property', $result['error']);
        $this->assertEquals($result['error']['property'], 'username');
    }

    public function testPutUpdateActionError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("editUser")->andReturn([
            'success' => false,
            'message' => 'updateError'
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'getUserRepository',
                                'validateBussinessError'])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->updateInputs);

        $user->method('validateApi')
                   ->willReturn($this->updateInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'Update Error',
                        ]
                    ]);

        //call method
        $result = $user->putUpdateAction('58e27db58d6a71405dbbdb32');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Update Error');
    }

    public function testPutUpdateActionSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("editUser")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => [
                "username"   => "Test1",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58eb5ab69aaf84001429e402"
            ]
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'getUserRepository',
                                'validateApi',
                                'output'
                            ])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->updateInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateApi')
                   ->willReturn($this->updateInputs);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data' => [
                            "username"   => "Test1",
                            "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                            "type_id"    => "58eb50b59aaf8400135e1f12",
                            "status"     => "active",
                            "created_at" => "2017-04-10 17:13:10",
                            "updated_at" => "2017-04-10 18:17:46",
                            "id"         => "58eb5ab69aaf84001429e402"
                        ]
                    ]);

        //call method
        $result = $user->putUpdateAction('589991df19d70c00077b0e42');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
    }

    public function testDeleteUserActionValidateError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'validateApi',
                                'validateError'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn([]);

        $user->method('validateApi')
                   ->willReturn([
                        'msgError'   => 'The id is required',
                        'fieldError' => 'id'
                    ]);

        $user->method('validateError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad request',
                        ],
                        'error' => [
                            'message'  => 'The id is required',
                            'property' => 'id',
                        ]
                    ]);

        //call method
        $result = $user->deleteUserAction('58abd3c92f8331000b63d042');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'The id is required');
        $this->assertArrayHasKey('property', $result['error']);
        $this->assertEquals($result['error']['property'], 'id');
    }

    public function testDeleteUserActionError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("deleteUser")->andReturn([
            'success' => false,
            'message' => 'deleteError'
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'validateApi',
                                'getUserRepository',
                                'validateBussinessError'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn($this->deleteInputs);

        $user->method('validateApi')
                   ->willReturn($this->deleteInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'Delete Error',
                        ]
                    ]);

        //call method
        $result = $user->deleteUserAction('58abd3c92f8331000b63d042');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Delete Error');
    }

    public function testDeleteUserActionSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("deleteUser")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => '58abd3c92f8331000b63d042'
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getAllUrlParam',
                                'getUserRepository',
                                'validateApi',
                                'output'
                            ])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn($this->deleteInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateApi')
                   ->willReturn($this->deleteInputs);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data' => '58abd3c92f8331000b63d042'
                    ]);

        //call method
        $result = $user->deleteUserAction('58abd3c92f8331000b63d042');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('string', $result['data']);
    }
    
    public function testPostLoginActionValidateError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'validateError'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn([]);

        $user->method('validateApi')
                   ->willReturn([
                        'msgError'   => 'The username is required',
                        'fieldError' => 'username'
                    ]);

        $user->method('validateError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad request',
                        ],
                        'error' => [
                            'message'  => 'The username is required',
                            'property' => 'username',
                        ]
                    ]);

        //call method
        $result = $user->postLoginAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'The username is required');
        $this->assertArrayHasKey('property', $result['error']);
        $this->assertEquals($result['error']['property'], 'username');
    }

    public function testPostLoginActionLoginError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("checkLogin")->andReturn([
            'success' => false,
            'message' => 'loginFail',
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'getUserRepository',
                                'validateBussinessError'])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->loginInputs);

        $user->method('validateApi')
                   ->willReturn($this->loginInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'Login Fail',
                        ]
                    ]);

        //call method
        $result = $user->postLoginAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Login Fail');
    }

    public function testPostLoginActionSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("checkLogin")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => [
                "username"   => "Test1",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58eb5ab69aaf84001429e402"
            ]
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'getUserRepository',
                                'validateApi',
                                'output'
                            ])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->updateInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateApi')
                   ->willReturn($this->updateInputs);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data' => [
                            "username"   => "Test1",
                            "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                            "type_id"    => "58eb50b59aaf8400135e1f12",
                            "status"     => "active",
                            "created_at" => "2017-04-10 17:13:10",
                            "updated_at" => "2017-04-10 18:17:46",
                            "id"         => "58eb5ab69aaf84001429e402"
                        ]
                    ]);

        //call method
        $result = $user->postLoginAction();

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
    }

    public function testPutChangepasswordActionValidateError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'validateError'])
                           ->getMock();

        $user->method('getAllUrlParam')
                   ->willReturn([]);

        $user->method('validateApi')
                   ->willReturn([
                        'msgError'   => 'The id is required',
                        'fieldError' => 'id'
                    ]);

        $user->method('validateError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad request',
                        ],
                        'error' => [
                            'message'  => 'The id is required',
                            'property' => 'id',
                        ]
                    ]);

        //call method
        $result = $user->putChangepasswordAction('');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'The id is required');
        $this->assertArrayHasKey('property', $result['error']);
        $this->assertEquals($result['error']['property'], 'id');
    }

    public function testPutChangepasswordActionChangePassError()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("changePassword")->andReturn([
            'success' => false,
            'message' => 'oldPasswordWrong',
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'validateApi',
                                'getUserRepository',
                                'validateBussinessError'])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->changePassInputs);

        $user->method('validateApi')
                   ->willReturn($this->changePassInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateBussinessError')
                   ->willReturn([
                        'status' => [
                            'code'    => 400,
                            'message' => 'Bad Request',
                        ],
                        'error' => [
                            'message'  => 'Old password not match',
                        ]
                    ]);

        //call method
        $result = $user->putChangepasswordAction('58eb5ab69aaf84001429e402');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals($result['status']['code'], 400);
        $this->assertEquals($result['status']['message'], 'Bad Request');
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($result['error']['message'], 'Old password not match');
    }

    public function testPutChangepasswordActionSuccess()
    {
        //create mock repo
        $userRepo = Mockery::mock('UserRepository');
        $userRepo->shouldReceive("changePassword")->andReturn([
            'success' => true,
            'message' => '',
            'data'    => [
                "username"   => "Test1",
                "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                "type_id"    => "58eb50b59aaf8400135e1f12",
                "status"     => "active",
                "created_at" => "2017-04-10 17:13:10",
                "updated_at" => "2017-04-10 18:17:46",
                "id"         => "58eb5ab69aaf84001429e402"
            ]
        ]);
        //create class
        $user = $this->getMockBuilder('App\Controllers\UserController')
                           ->setMethods([
                                'getPostInput',
                                'getUserRepository',
                                'validateApi',
                                'output'
                            ])
                           ->getMock();

        $user->method('getPostInput')
                   ->willReturn($this->updateInputs);

        $user->method('getUserRepository')
                   ->willReturn($userRepo);

        $user->method('validateApi')
                   ->willReturn($this->updateInputs);

        $user->method('output')
                   ->willReturn([
                        'status' => [
                            'code'    => 200,
                            'message' => 'Success',
                        ],
                        'data' => [
                            "username"   => "Test1",
                            "password"   => '$2y$08$TVFJZXNBbTlhblh3RHB6NOwsLR4x2sRxW4BxOYeqszVMvpC1CFlt2',
                            "type_id"    => "58eb50b59aaf8400135e1f12",
                            "status"     => "active",
                            "created_at" => "2017-04-10 17:13:10",
                            "updated_at" => "2017-04-10 18:17:46",
                            "id"         => "58eb5ab69aaf84001429e402"
                        ]
                    ]);

        //call method
        $result = $user->putChangepasswordAction('58eb5ab69aaf84001429e402');

        //check result
        $this->assertInternalType('array', $result);
        $this->assertEquals($result['status']['code'], 200);
        $this->assertEquals($result['status']['message'], 'Success');
        $this->assertArrayHasKey('data', $result);
        $this->assertInternalType('array', $result['data']);
        $this->assertArrayHasKey('id', $result['data']);
    }
    //------- end: Test function --------//
}