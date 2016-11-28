<?php

namespace classes;


class ClientRepository extends AbstractRepository
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'client';
    }

    /**
     * @return array
     */
    public function getTableFields()
    {
        return [
            'id' => 'int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'varchar(255) NOT NULL',
            'status' => 'tinyint(1) unsigned DEFAULT 0',
        ];
    }

    /**
     * @return string
     */
    public function getDataFilePath()
    {
        return dirname(__FILE__) . '/../files/clients.csv';
    }

    /**
     * @param $data
     * @return Client
     */
    public function buildEntity($data)
    {
        $client = new Client();

        $client
            ->setId($data['id'])
            ->setName($data['name'])
            ->setStatus($data['status']);

        return $client;
    }

    /**
     * @return Client
     */
    public function getRandomClient()
    {
        $data = DB::o()->getRandomRow($this->getTableName());

        return $this->buildEntity($data);
    }

    /**
     * @param Client|EntityInterface $entity
     * @return Client|EntityInterface
     */
    public function save(EntityInterface $entity)
    {
        $data = [
            'status' => $entity->getStatus(),
        ];

        DB::o()->updateById($this->getTableName(), $data, $entity->getId());

        return $entity;
    }
}
