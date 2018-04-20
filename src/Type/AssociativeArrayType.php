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
use AndyDune\MongoOdm\Exception;
use AndyDune\MongoOdm\TypeAbstract;
use MongoDB\Model\BSONArray;


class AssociativeArrayType extends TypeAbstract
{
    protected $arrayMaxLength = 1000;

    public function convertToPhpValue($value)
    {
        if ($value instanceof BSONArray) {
            $value = $value->getArrayCopy();
        }
        if ($value instanceof \ArrayObject) {
            $value = $value->getArrayCopy();
        }

        return $value;
    }

    public function convertToDatabaseValue($value, $existValue = null)
    {
        if (!is_array($value)) {
            throw new Exception('It waits array');
        }

        if (!count($value)) {
            return [];
        }

        $key = key($value);
        $value = current($value);

        if (!$existValue) {
            $existValue = [];
        }

        if ($this->childType) {
            $value = $this->childType->convertToDatabaseValue($value);
        }

        $existValue[$key] = $value;

        if (count($existValue) > $this->arrayMaxLength) {
            // sort right before delete
            //ksort($existValue, SORT_STRING); // @todo think about it
            array_shift($existValue);
        }

        return $existValue;
    }

    /**
     * @param integer $length
     * @return ArrayType
     */
    public function setArrayMaxLength($length)
    {
        $this->arrayMaxLength = $length;
        return $this;
    }
}