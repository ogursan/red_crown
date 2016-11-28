<?php

namespace classes;


abstract class AbstractRepository
{
    public function __construct()
    {
        if (!DB::o()->checkTableExistence($this->getTableName())) {
            DB::o()->createTable($this->getTableName(), $this->getTableFields());
            DB::o()->importDataFromCsv($this->getTableName(), $this->getDataFilePath());
        }
    }

    abstract public function getTableName();

    abstract public function getTableFields();

    abstract public function getDataFilePath();

    abstract public function buildEntity($data);

    abstract public function save(EntityInterface $entity);
}
