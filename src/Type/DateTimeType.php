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
use AndyDune\DateTime\DateTime;
use AndyDune\MongoOdm\TypeAbstract;
use MongoDB\BSON\UTCDateTime;

class DateTimeType extends TypeAbstract
{
    public function convertToPhpValue($value)
    {
        if ($value instanceof UTCDateTime) {
            return $value->toDateTime();
        }
        // @todo add exception if unexpected BD type
        return $value;
    }

    public function convertToDatabaseValue($value, $existValue = null)
    {
        if ($value instanceof DateTime) {
            return new UTCDateTime($value->getTimestamp() * 1000);
        }

        if (is_string($value)) {
            $value = strtotime($value);
        }
        return new UTCDateTime($value * 1000);
    }

}