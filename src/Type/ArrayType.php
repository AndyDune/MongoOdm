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


class ArrayType extends TypeAbstract
{
    public function convertToPhpValue($value)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, $existValue = null)
    {
        if (is_array($value)) {
            return $value;
        }

        if (!$existValue) {
            $existValue = [];
        }

        if ($this->childType) {
            $value = $this->childType->convertToDatabaseValue($value);
        }

        $existValue[] = $value;
        return $existValue;
    }

}