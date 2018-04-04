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


namespace AndyDune\MongoOdm;

use AndyDune\MongoOdm\Type\ArrayType;
use AndyDune\MongoOdm\Type\IntegerType;
use AndyDune\MongoOdm\Type\StringType;

class StringNotationTypeMap
{
    protected $types = [
        'string' => StringType::class,
        'integer' => IntegerType::class,
        'string_array' => [ArrayType::class, StringType::class],
        'integer_array' => [ArrayType::class, IntegerType::class],
    ];

    static protected $instance = null;

    static public function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $string
     * @return TypeAbstract
     * @throws Exception
     */
    public function getTypeObject($string)
    {
        if ($string instanceof TypeAbstract) {
            return $string;
        }

        if (array_key_exists($string, $this->types)) {
            $typeDescription = $this->types[$string];
            if (!is_array($typeDescription)) {
                $typeDescription = [$typeDescription];
            }

            $class = array_shift($typeDescription);

            if (is_string($class)) {
                $instance = new $class;
            } else {
                $instance = $class;
            }

            $class = array_shift($typeDescription);
            if (!$class) {
                return $instance;
            }

            if (is_string($class)) {
                $instanceSub = new $class;
            } else {
                $instanceSub = $class;
            }

            $instance->setChildType($instanceSub);
            return $instance;
        }

        if (class_exists($string)) {
            return new $string;
        }

        throw new Exception(sprintf('Type %s not registered.', $string));
    }

    /**
     * @param $string
     * @param string|TypeAbstract $class
     * @return StringNotationTypeMap
     */
    public function addType($string, $class)
    {
        $this->types[$string] = $class;
        return $this;
    }

    protected function __construct()
    {
    }
}