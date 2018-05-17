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
use AndyDune\ConditionalExecution\ConditionHolder;
use AndyDune\DateTime\DateTime;
use AndyDune\MongoOdm\TypeAbstract;
use MongoDB\BSON\UTCDateTime;

class DateTimeType extends TypeAbstract
{
    public function convertToPhpValue($value)
    {
        if ($value instanceof UTCDateTime) {
            return new DateTime($value->toDateTime());
        }
        return new DateTime((int)$value);
    }

    public function convertToDatabaseValue($value, $existValue = null)
    {
        if ($value instanceof DateTime) {
            return new UTCDateTime($value->getTimestamp() * 1000);
        }

        if ($value instanceof UTCDateTime) {
            return $value;
        }

        $conditionStringVariants = new ConditionHolder();
        $conditionStringVariants->add(function ($value) {
            $bool = preg_match('|^[+-]{1}|ui', $value);
            return $bool;
        });

        $conditionStringVariants->executeIfTrue(function ($value) {
            $dateTime = new DateTime();
            $dateTime->add($value);
            return new UTCDateTime($dateTime->getTimestamp() * 1000);
        });
        $conditionStringVariants->executeIfFalse(function ($value) {
            $value = strtotime($value);
            return new UTCDateTime($value * 1000);
        });

        $condition = new ConditionHolder();
        $condition->add(is_string($value))->executeIfTrue($conditionStringVariants)
        ->executeIfFalse(function ($value) {
            return new UTCDateTime($value * 1000);
        });

        return $condition->doIt($value);
    }

}