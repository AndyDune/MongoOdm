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

use AndyDune\MongoOdm\Type\IntegerType;
use AndyDune\MongoOdm\Type\StringType;

class StringNotationTypeMap
{
    protected $types = [
        'string' => StringType::class,
        'int' => IntegerType::class,
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
            if (is_string($this->types[$string])) {
                return new $this->types[$string];
            }
            return $this->types[$string];
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