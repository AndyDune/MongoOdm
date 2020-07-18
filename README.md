# MongoOdm

[![Build Status](https://travis-ci.org/AndyDune/MongoOdm.svg?branch=master)](https://travis-ci.org/AndyDune/MongoOdm)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/andydune/mongo-odm.svg?style=flat-square)](https://packagist.org/packages/andydune/mongo-odm)
[![Total Downloads](https://img.shields.io/packagist/dt/andydune/mongo-odm.svg?style=flat-square)](https://packagist.org/packages/andydune/mongo-odm)


Object Document mapper for mongoDB with no proxies, special configuration.

Installation
------------

Installation using composer:

```
composer require andydune/mongo-odm
```
Or if composer was not installed globally:
```
php composer.phar require andydune/mongo-odm
```
Or edit your `composer.json`:
```
"require" : {
     "andydune/mongo-odm": "^1"
}

```
And execute command:
```
php composer.phar update
```

# Control types
```php
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
```
