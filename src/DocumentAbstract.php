<?php
/**
 * Object Document mapper for mongoDB with no proxies, special configuration.
 *
 * You should use factory for create instance for this class.
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
use MongoDB\Collection;

abstract class DocumentAbstract
{
    /**
     * @var Collection
     */
    protected $collection;

    protected $id;
    protected $fieldsMap = [];

    protected $dataDb = [];
    protected $dataPhp = [];

    final public function __construct(Collection $collection, $strong = false)
    {
        $this->collection = $collection;
        $this->describe();
        $map = StringNotationTypeMap::getInstance();
        array_walk($this->fieldsMap, function (&$value, $key) use($map) {
            $value = $map->getTypeObject($value);
        });
    }

    /**
     * Overload this method to direct describe collection fields.
     */
    public function describe()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    public function populate($data)
    {
        $this->id = $data['_id'];
        unset($data['_id']);
        $this->dataDb = $data;
    }

    public function __set($name, $value)
    {
        $this->dataPhp[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->dataPhp)) {
            return $this->dataPhp[$name];
        }

        if (array_key_exists($name, $this->dataDb)) {
            $result = $this->dataDb[$name];
            if (array_key_exists($name, $this->fieldsMap)) {
                return $this->fieldsMap[$name]->convertToPhpValue($result);
            }
            return $result;
        }
        return null;
    }
}