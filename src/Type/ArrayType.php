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
use MongoDB\Model\BSONArray;


class ArrayType extends TypeAbstract
{
    protected $push = true;

    protected $arrayMaxLength = 1000;

    public function convertToPhpValue($value)
    {
        if ($value instanceof BSONArray) {
            $value = $value->getArrayCopy();
        }
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

        if ($this->push) {
            $existValue[] = $value;
            if (count($existValue) > $this->arrayMaxLength) {
                array_shift($existValue);
            }
        } else {
            array_unshift($existValue, $value);
            if (count($existValue) > $this->arrayMaxLength) {
                array_pop($existValue);
            }
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

    /**
     * Push value onto the end of array.
     * It is used as default.
     *
     * @return ArrayType
     */
    public function useArrayPush()
    {
        $this->push = true;
        return $this;
    }

    /**
     * Prepend value to the beginning of an array
     * @return ArrayType
     */
    public function useArrayUnShift()
    {
        $this->push = false;
        return $this;
    }

}