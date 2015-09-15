<?php
namespace Rox\Gateway\MongoDb;

use PhlyMongo\HydratingMongoCursor;
use PhlyMongo\HydratingPaginatorAdapter as MongoPaginatorAdapter;
use Zend\Paginator\Paginator;
use Rox\Gateway\RoxGateway;

/**
 * Use this class to implement collection especific methods
 * 
 * TODO Review docs and transactions checking success and thow exceptions in negative cases
 * TODO implements a interface for common methods
 * @author Marcelo Araújo
 */
class AbstractGateway extends RoxGateway
{

    /**
     *
     * @param mixed $id
     * @param string $module
     * @param string $collection
     * @return array
     */
    public function getReference($id, $module, $collection)
    {
        $className = "$module\Gateway\MongoDb\\$collection";
        $gateway = new $className($this->db);
        return $gateway->findById($id);
    }

    /**
     *
     * @param array $documents
     * @param int $id
     * @return array|NULL
     */
    public function getSubDocument($documents, $id)
    {
        foreach ($documents as $document) {
            if ($document['_id'] == $id) {
                return $document;
            }
        }
        return null;
    }

    /**
     *
     * @param \MongoCursor $cursor
     * @return \PhlyMongo\HydratingMongoCursor
     */
    public function hydrateCollection(\MongoCursor $cursor)
    {
        return new HydratingMongoCursor($cursor, $this->model->getHydrator(), $this->model);
    }

    /**
     *
     * @return \MongoCollection
     */
    public function getCollection()
    {
        return $this->getCollection();
    }

    /**
     * Find all documents from especific colection
     * 
     * @return \Zend\Paginator\Paginator
     */
    public function findAll($criteria = [], $sort = null)
    {
        $cursor = $this->getCollection()->find($criteria);
        if ($sort) {
            $cursor->sort($sort);
        }
        $adapter = new MongoPaginatorAdapter(new HydratingMongoCursor($cursor, $this->model->getHydrator(), $this->model));
        return new Paginator($adapter);
    }

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function count($criteria = [])
    {
        return $this->getCollection()->count($criteria);
    }

    /**
     *
     * @param array $criteria
     * @return array
     */
    public function findCurrent($criteria = [])
    {
        return $this->getCollection()->findOne($criteria);
    }

    /**
     *
     * @param string $label
     * @return array An associative array with [value => label] format
     */
    public function getAssocArray($criteria = [], $label = 'name')
    {
        $assoc = [];
        $data = $this->getCollection()
            ->find($criteria, [
            '_id',
            $label
        ])
            ->sort([
            $label => 1
        ]);
        foreach ($data as $record) {
            $assoc[$record['_id']->{'$id'}] = $record[$label];
        }
        return $assoc;
    }

    /**
     * Find and return a document by its mongoId
     * 
     * @param mixed $id
     *            of document
     * @return array
     */
    public function findById($id)
    {
        return $this->getCollection()->findOne([
            '_id' => $this->getMongoId($id)
        ]);
    }

    /**
     *
     * @param array $data
     *            with _id
     * @return mixed
     */
    public function update(array $data)
    {
        $id = $this->getMongoId($data['_id']);
        unset($data['_id']);
        return $this->getCollection()->update([
            '_id' => $id
        ], [
            '$set' => $data
        ]);
    }

    /**
     *
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        if (! $data['_id']) {
            unset($data['_id']);
        } else {
            $data['_id'] = $this->getMongoId($data['_id']);
        }
        $this->getCollection()->save($data);
        return $data;
    }

    /**
     * Convert a string into a \MongoId
     *
     * @param mixed $id
     * @return \MongoId
     */
    public function getMongoId($id, $field = 'unknown')
    {
        if ($id instanceof \MongoId) {
            return $id;
        } elseif (preg_match('/\w{24}/', $id)) {
            return new \MongoId($id);
        } else {
            // throw new \Exception("$field field must match /[a-z0-9]{24}/");
            throw new \Exception("Parâmetro inválido: $field");
        }
    }

    /**
     *
     * @param mixed $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->getCollection()->remove([
            '_id' => $this->getMongoId($id)
        ]);
    }
}