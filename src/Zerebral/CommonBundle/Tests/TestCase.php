<?php

namespace Zerebral\CommonBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;


abstract class TestCase extends WebTestCase
{
    /**
     * @var Kernel
     */
    protected static $kernel = null;

    /**
     * @var \PropelPDO
     */
    protected $connection;


    protected function setUp()
    {
        self::$kernel = static::createKernel();
        self::$kernel->boot();

        $this->connection = \Propel::getConnection();
    }

    /**
     * @return \PropelPDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set private/protected object property
     *
     * @param object $object
     * @param string $protectedPropertyName
     * @param mixed $value
     */
    public function setProperty($object, $protectedPropertyName, $value)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($protectedPropertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Get private/protected object property
     *
     * @param object $object
     * @param string $protectedPropertyName
     * @return mixed
     */
    public function getProperty($object, $protectedPropertyName)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($protectedPropertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * Call private/protected object method
     *
     * @param object $object
     * @param string $protectedMethod
     * @param array $args
     * @return mixed
     */
    public function callMethod($object, $protectedMethod, $args = array())
    {
        $class = new \ReflectionClass($object);
        $method = $class->getMethod($protectedMethod);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    /**
     * Execute SELECT-statement and print well-formatted result
     *
     * @param $query
     * @param \PDO $connection
     */
    public function printSqlDataSet($query, \PDO $connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->getConnection();
        }

        $data = $connection->prepare($query)->fetchAll(\PDO::FETCH_COLUMN);

        printf("Query: %s (%d rows)" . PHP_EOL, $query, count($data));

        $format = join("|", array_fill(0, count($data[0]), '%15s')) . PHP_EOL;
        vprintf($format, array_keys($data[0]));
        vprintf($format, array_fill(0, count($data[0]), '---------------'));

        foreach ($data as $row) {
            vprintf($format, $row);
        }
    }
}