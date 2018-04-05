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
use AndyDune\ArrayContainer\Action\KeysLeave;
use AndyDune\ArrayContainer\ArrayContainer;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

abstract class DocumentAbstract
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ObjectId|null
     */
    protected $id = null;
    protected $fieldsMap = [];

    protected $data = [];
    protected $dataWasMentioned = [];

    final public function __construct(Collection $collection, $strong = false)
    {
        $this->collection = $collection;
        $this->describe();
        $map = StringNotationTypeMap::getInstance();
        array_walk($this->fieldsMap, function (&$value, $key) use($map) {
            $value = $map->getTypeObject($value);
        });
    }

    public function save()
    {
        $dataToSave = $this->getDataToSave(true);
        if (!$dataToSave) {
            return false;
        }
        if ($this->id) {
            $this->collection->updateOne(['_id' => $this->id], ['$set' => $dataToSave]);
        } else {
            $result = $this->collection->insertOne($dataToSave);
            $this->id = $result->getInsertedId();
        }
        $this->dataWasMentioned = [];
        return true;
    }

    public function getDataToSave($onlyMentionedData = false)
    {
        if ($onlyMentionedData) {
            $arrayContainer = new ArrayContainer($this->data);
            $arrayContainer->setAction(new KeysLeave())->executeAction($this->dataWasMentioned);
            return $arrayContainer->getArrayCopy();
        }
        return $this->data;
    }

    /**
     * Overload this method to direct describe collection fields.
     */
    protected function describe()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    public function populate($data)
    {
        if ($data instanceof BSONDocument) {
            $data = $data->getArrayCopy();
        }
        $this->id = $data['_id'];
        unset($data['_id']);
        $this->data = $data;
    }

    public function retrieve($filter = [])
    {
        if (!$filter and !$this->id) {
            return false;
        }
        $filter = $filter ? $filter : ['_id' => $this->id];
        $result = $this->collection->findOne($filter);
        if ($result) {
            $this->populate($result);
            return true;
        }
        return false;
    }

    public function __set($name, $value)
    {
        $this->dataWasMentioned[] = $name;
        if (array_key_exists($name, $this->fieldsMap)) {
            $value = $this->fieldsMap[$name]->convertToDatabaseValue($value, $this->get($name));
        }

        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        if (array_key_exists($name, $this->data)) {
            $result = $this->data[$name];
            if (array_key_exists($name, $this->fieldsMap)) {
                return $this->fieldsMap[$name]->convertToPhpValue($result);
            }
            return $result;
        }
        return null;

    }
}