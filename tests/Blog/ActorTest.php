<?php
use \PHPUnit_Framework_TestCase as TestCase;
use boot\Bootstrap;
use Entity\Actor;
use Doctrine\ORM\EntityManager;

/**
 * Actor test case.
 */
class ActorTest extends TestCase
{

    /**
     *
     * @var Actor
     */
    private $actor;
    
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // TODO Auto-generated ActorTest::setUp()
        
        $this->actor = new Actor(/* parameters */);
        $this->entityManager = Bootstrap::getPimple("entityManager");
        
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated ActorTest::tearDown()
       
        $this->actor = null;
        
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Actor->__construct()
     */
    public function test__construct()
    {
        // TODO Auto-generated ActorTest->test__construct()
        $this->markTestIncomplete("__construct test not implemented");
        
        $this->actor->__construct(/* parameters */);
    }

    /**
     * Tests Actor->setActorId()
     */
    public function testSetActorId()
    {
        // TODO Auto-generated ActorTest->testSetActorId()
        $this->markTestIncomplete("setActorId test not implemented");
        
        $this->actor->setActorId(/* parameters */);
    }

    /**
     * Tests Actor->getActorId()
     */
    public function testGetActorId()
    {
        // TODO Auto-generated ActorTest->testGetActorId()
        $this->markTestIncomplete("getActorId test not implemented");
        
        $this->actor->getActorId(/* parameters */);
    }

    /**
     * Tests Actor->setFirstName()
     */
    public function testSetFirstName()
    {
        // TODO Auto-generated ActorTest->testSetFirstName()
        $this->markTestIncomplete("setFirstName test not implemented");
        
        $this->actor->setFirstName(/* parameters */);
    }

    /**
     * Tests Actor->getFirstName()
     */
    public function testGetFirstName()
    {
        // TODO Auto-generated ActorTest->testGetFirstName()
        $this->markTestIncomplete("getFirstName test not implemented");
        
        $this->actor->getFirstName(/* parameters */);
    }

    /**
     * Tests Actor->setLastName()
     */
    public function testSetLastName()
    {
        // TODO Auto-generated ActorTest->testSetLastName()
        $this->markTestIncomplete("setLastName test not implemented");
        
        $this->actor->setLastName(/* parameters */);
    }

    /**
     * Tests Actor->getLastName()
     */
    public function testGetLastName()
    {
        // TODO Auto-generated ActorTest->testGetLastName()
        $this->markTestIncomplete("getLastName test not implemented");
        
        $this->actor->getLastName(/* parameters */);
    }

    /**
     * Tests Actor->setLastUpdate()
     */
    public function testSetLastUpdate()
    {
        // TODO Auto-generated ActorTest->testSetLastUpdate()
        $this->markTestIncomplete("setLastUpdate test not implemented");
        
        $this->actor->setLastUpdate(/* parameters */);
    }

    /**
     * Tests Actor->getLastUpdate()
     */
    public function testGetLastUpdate()
    {
        // TODO Auto-generated ActorTest->testGetLastUpdate()
        $this->markTestIncomplete("getLastUpdate test not implemented");
        
        $this->actor->getLastUpdate(/* parameters */);
    }

    /**
     * Tests Actor->addFilmActor()
     */
    public function testAddFilmActor()
    {
        // TODO Auto-generated ActorTest->testAddFilmActor()
        $this->markTestIncomplete("addFilmActor test not implemented");
        
        $this->actor->addFilmActor(/* parameters */);
    }

    /**
     * Tests Actor->removeFilmActor()
     */
    public function testRemoveFilmActor()
    {
        // TODO Auto-generated ActorTest->testRemoveFilmActor()
        $this->markTestIncomplete("removeFilmActor test not implemented");
        
        $this->actor->removeFilmActor(/* parameters */);
    }

    /**
     * Tests Actor->getFilmActors()
     */
    public function testGetFilmActors()
    {
        // TODO Auto-generated ActorTest->testGetFilmActors()
        $this->markTestIncomplete("getFilmActors test not implemented");
        
        $this->actor->getFilmActors(/* parameters */);
    }

    /**
     * Tests Actor->__sleep()
     */
    public function test__sleep()
    {
        // TODO Auto-generated ActorTest->test__sleep()
        $this->markTestIncomplete("__sleep test not implemented");
        
        $this->actor->__sleep(/* parameters */);
    }
    
    
    public function test_insert()
    {
        $actor = new Actor();
       
        $actor->setFirstName("php");
        $actor->setLastName("unit");
        $this->entityManager->persist($actor);
        $this->entityManager->flush($actor);
    }
}

