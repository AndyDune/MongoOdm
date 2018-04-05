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
use AndyDune\MongoOdm\DocumentAbstract;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testIntegerAndStringType()
    {
        $mongo =  new \MongoDB\Client();
        $collection = $mongo->selectDatabase('test')->selectCollection('test_odm');
        $collection->deleteMany([]);

        $odmClass = new class($collection) extends DocumentAbstract {
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
}