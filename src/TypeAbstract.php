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


abstract class TypeAbstract
{
    /**
     * @var null|TypeAbstract
     */
    protected $childType = null;

    /**
     * @param TypeAbstract $type
     * @return TypeAbstract
     */
    public function setChildType(TypeAbstract $type)
    {
        $this->childType = $type;
        return $this;
    }

    abstract public function convertToPhpValue($value);

    abstract public function convertToDatabaseValue($value, $existValue = null);
}