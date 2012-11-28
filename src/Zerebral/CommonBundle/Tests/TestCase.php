<?php

namespace Zerebral\CommonBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;


abstract class TestCase extends WebTestCase
{
//    /**
//     * @var \PropelConnection
//     */
//    protected $connection;


    protected function setUp()
    {
        parent::setUp();

        self::$kernel = static::createKernel();
        self::$kernel->boot();

        // @todo setup propel connection
//        $this->connection = self::$kernel->getContainer()->get('doctrine')->getConnection($this->connectionName);
    }

//    /**
//     * @return \PropelConnection
//     */
//    public function getConnection()
//    {
//        return $this->connection;
//    }

    public function setProperty($object, $protectedPropertyName, $value)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($protectedPropertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public function callMethod($object, $protectedMethod, $args = array())
    {
        $class = new \ReflectionClass($object);
        $method = $class->getMethod($protectedMethod);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

//    public function printSqlDataSet($connection, $query)
//    {
////        $format = "%10s|%15s|%10s|%10s|%10s|%10s";
//        $data = $connection->fetchAll($query);
//
//        printf("Query: %s (%d rows)" . PHP_EOL, $query, count($data));
//
//        $format = join("|", array_fill(0, count($data[0]), '%15s')) . PHP_EOL;
//        vprintf($format, array_keys($data[0]));
//        vprintf($format, array_fill(0, count($data[0]), '---------------'));
//
//        foreach($data as $row)
//            vprintf($format, $row);
//    }

//    protected function loadStoredProcedure($name)
//    {
//        $declaration = file_get_contents(self::$kernel->getRootDir() . '/StoredProcedures/' . $name . '.sql');
//        $this->getConnection()->exec("DROP PROCEDURE IF EXISTS " . $name);
//        $this->getConnection()->exec($declaration);
//    }
}