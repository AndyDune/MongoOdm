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
use MongoDB\Collection;

abstract class DocumentAbstract
{
    /**
     * @var Collection
     */
    protected $collection;

    protected $id;
    protected $fieldsMapString = [];
    protected $fieldsMap = [];

    final public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->describe();
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
}