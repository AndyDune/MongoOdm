<?php
/**
 * Object Document mapper for mongoDB with no proxies, special configuration.
 *
 * PHP version >= 7.1
 *
 *
 * @package andydune/mongo-odm
 * @link  https://github.com/AndyDune/MongoOdm for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2018 Andrey Ryzhov
 *
 */


namespace AndyDune\MongoOdmTest;

use AndyDune\ArrayContainer\Action\ArrayShift;
use AndyDune\ArrayContainer\ArrayContainer;
use AndyDune\DateTime\DateTime;
use AndyDune\MongoOdm\DocumentAbstract;
use AndyDune\MongoOdm\Type\ArrayType;
use AndyDune\MongoOdm\Type\AssociativeArrayType;
use AndyDune\MongoOdm\Type\StringType;
use MongoDB\BSON\UTCDateTime;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testIntegerAndStringType()
    {
        $mongo = new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test_odm');
        $collection->deleteMany([]);

        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['number'] = 'integer';
                $this->fieldsMap['code'] = 'string';
                $this->fieldsMap['birthday'] = 'datetime';
            }
        };

        $time = time();
        $odmClass->number = '12';
        $odmClass->code = '125';
        $odmClass->birthday = date('Y-m-d H:i:s', $time);
        $odmClass->save();

        $res = $collection->findOne(['number' => 12]);
        $this->assertTrue((bool)$res);
        $res = $collection->findOne(['number' => '12']);
        $this->assertFalse((bool)$res);

        $res = $collection->findOne(['code' => '125']);
        $this->assertTrue((bool)$res);
        $res = $collection->findOne(['code' => 125]);
        $this->assertFalse((bool)$res);


        $odmClass->retrieve();

        $this->assertEquals($odmClass->birthday->getTimestamp(), $time);

        $odmClass->number = 'dasdsad';
        $odmClass->save();

        $res = $collection->findOne(['number' => 0]);
        $this->assertTrue((bool)$res);

        $collection->deleteMany([]);
    }

    public function testDateTimeType()
    {
        $mongo = new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test_odm');
        $collection->deleteMany([]);

        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['birthday'] = 'datetime';
            }
        };

        $time = time();
        $odmClass->birthday = date('Y-m-d H:i:s', $time);
        $odmClass->save();
        $odmClass->retrieve();
        $this->assertEquals($odmClass->birthday->getTimestamp(), $time);


        $time = $time + 23;
        $odmClass->birthday = $time;
        $odmClass->save();
        $odmClass->retrieve();
        $this->assertEquals($odmClass->birthday->getTimestamp(), $time);

        $odmClass->birthday = new DateTime($time);
        $odmClass->save();
        $odmClass->retrieve();
        $this->assertEquals($odmClass->birthday->getTimestamp(), $time);


        $time = time();
        $odmClass->birthday = '+ 1 hour';
        $odmClass->save();
        $odmClass->retrieve();
        $this->assertEquals($odmClass->birthday->format('G'), date('G', $time + 3600));

        $time = time();
        $odmClass->birthday = '- 3 hour';
        $odmClass->save();
        $odmClass->retrieve();
        $this->assertEquals($odmClass->birthday->format('G'), date('G', $time - 3 * 3600));


        $time = time() + 13;
        $odmClass->birthday = new UTCDateTime($time * 1000);
        $odmClass->save();
        $odmClass->retrieve();
        $this->assertEquals($odmClass->birthday->format('G'), date('G', $time));

    }

    public function testArray()
    {
        $mongo = new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test_odm');
        $collection->deleteMany([]);

        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = 'string_array';
            }
        };

        $odmClass->test_array = 'line 1';
        $odmClass->test_array = 12313123;
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;
        $this->assertTrue(count($array) == 2);
        $this->assertEquals('line 1', array_shift($array));
        $this->assertEquals('12313123', array_shift($array));


        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = 'integer_array';
            }
        };

        $odmClass->test_array = 'line 1';
        $odmClass->test_array = 12313123;
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;
        $this->assertTrue(count($array) == 2);
        $this->assertEquals(0, array_shift($array));
        $this->assertEquals(12313123, array_shift($array));


        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = new ArrayType(new StringType());
                $this->fieldsMap['test_array']->setArrayMaxLength(2);

            }
        };

        $odmClass->test_array = 'line 1';
        $odmClass->test_array = 12313123;
        $odmClass->test_array = 'line 2';
        $odmClass->test_array = 'line 3';
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;
        $this->assertTrue(count($array) == 2);
        $this->assertEquals('line 2', array_shift($array));
        $this->assertEquals('line 3', array_shift($array));


        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = new ArrayType(new StringType());
                $this->fieldsMap['test_array']->useArrayUnShift();
            }
        };

        $odmClass->test_array = 'line 1';
        $odmClass->test_array = 12313123;
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;
        $this->assertTrue(count($array) == 2);
        $this->assertEquals('12313123', array_shift($array));
        $this->assertEquals('line 1', array_shift($array));


        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = new ArrayType(new StringType());
                $this->fieldsMap['test_array']->useArrayUnShift();
                $this->fieldsMap['test_array']->setArrayMaxLength(2);
            }
        };

        $odmClass->test_array = 'line 1';
        $odmClass->test_array = 12313123;
        $odmClass->test_array = 'line 2';
        $odmClass->test_array = 'line 3';
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;
        $this->assertTrue(count($array) == 2);
        $this->assertEquals('line 3', array_shift($array));
        $this->assertEquals('line 2', array_shift($array));

        $odmClass->test_array = ['one'];
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;
        $this->assertTrue(count($array) == 1);
        $this->assertEquals('one', array_shift($array));


        $collection->deleteMany([]);


        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = new AssociativeArrayType(new StringType());
                //$this->fieldsMap['test_array']->setArrayMaxLength(2);
            }
        };

        $odmClass->test_array = ['line 1' => 1];
        $odmClass->test_array = [12312312 =>  2];
        $odmClass->test_array = ['line 2' =>  3];
        $odmClass->test_array = ['line 3' =>  4];
        $odmClass->save();

        $odmClass->retrieve();
        $array = $odmClass->test_array;

        $this->assertTrue(count($array) == 4);

        $container = new ArrayContainer($array);
        $value = $container->setAction(new ArrayShift())->executeAction();
        $this->assertEquals(['line 1' => 1], $value);

        $value = $container->setAction(new ArrayShift())->executeAction();
        $this->assertEquals([12312312 => 2], $value);


        $value = $container->setAction(new ArrayShift())->executeAction();
        $this->assertEquals(['line 2' => 3], $value);

        $value = $container->setAction(new ArrayShift())->executeAction();
        $this->assertEquals(['line 3' => 4], $value);

        $collection->deleteMany([]);



        $odmClass = new class($collection) extends DocumentAbstract
        {
            protected function describe()
            {
                $this->fieldsMap['test_array'] = new AssociativeArrayType(new StringType());
                $this->fieldsMap['test_array']->setArrayMaxLength(2);
            }
        };

        $odmClass->test_array = ['line 1' => 1];
        $odmClass->test_array = ['line 2' =>  3];
        $odmClass->test_array = ['line 3' =>  4];
        $odmClass->test_array = [12312312 =>  2];
        $odmClass->save();


        $odmClass->retrieve();
        $array = $odmClass->test_array;

        $this->assertTrue(count($array) == 2);

        $container = new ArrayContainer($array);
        $value = $container->setAction(new ArrayShift())->executeAction();
        $this->assertEquals(['line 3' => 4], $value);

        $value = $container->setAction(new ArrayShift())->executeAction();
        $this->assertEquals([12312312 => 2], $value);

        $collection->deleteMany([]);

    }
}