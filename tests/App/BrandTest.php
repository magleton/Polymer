<?php
use \PHPUnit_Framework_TestCase as TestCase;
use boot\Bootstrap;
use Entity\Brand;
use Entity\Actor;

/**
 * Brand test case.
 */
class BrandTest extends TestCase
{

    /**
     *
     * @var Brand
     */
    private $Brand;

    private $entityManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entityManager = \Polymer\Utils\CoreUtils::getDbInstance('db1');
        // TODO Auto-generated BrandTest::setUp()

    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Brand->setId()
     */
    public function testSetId()
    {
        $order = new \Entity\Models\Eorder();
        $order->setLoanMoney(100);
        $order->setManagerPhone('13456789870');
        $this->entityManager->persist($order);
        $this->entityManager->flush($order);
    }

}

