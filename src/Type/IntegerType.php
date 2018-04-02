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


namespace AndyDune\MongoOdm\Type;
use AndyDune\MongoOdm\TypeAbstract;

class IntegerType extends TypeAbstract
{
    public function convertToPhpValue($value)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, $existValue = null)
    {
        return (int)trim($value);
    }

}