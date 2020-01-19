<?php

namespace App\Storage;

use App\Entity\Employee;
use App\Entity\Developer;
use App\Entity\User;
use App\Entity\Admin;
use Collator;
use Exception;
use SimpleXMLElement;
use Psr\Log\LoggerInterface;


class StorageAdapter
{
    private $storageXml;

    public function __construct(StorageXml $storageXml, LoggerInterface $logger)
    {
        $this->storageXml = $storageXml;
        $this->logger = $logger;
    }

    public function findAll()
    {
        $all = $this->storageXml->getXml();
        $allAsArrayClass = [];
        foreach ($all as $employee) {
            $allAsArrayClass[] = $this->xmlToEntity($employee);
        }

        usort($allAsArrayClass,[$this, 'sort']);

        return $allAsArrayClass;
    }

    public function searchByUuid(string $uuid)
    {
        return $this->xmlToEntity($this->storageXml->searchByUuid($uuid)[0]);
    }

    public function xmlToEntity(SimpleXMLElement $employee): Employee
    {
        $entityType = 'App\\Entity\\' . ucfirst($employee->role);
        try {
            $entity = new $entityType;
            $entity->setEmployee(
                $employee->firstName,
                $employee->lastName,
                $employee->gender,
                $employee->dob,
                $employee->email,
                $employee->attributes()
            );
        } catch (\Throwable $e) {
            throw new Exception('Unknown entity class/type: ' . $e->getMessage());
        }

        return $entity;
    }

    public function storeEntity(Employee $employee) 
    {
        $this->storageXml->storeEntity($employee);
        $this->storageXml->save();
    }

    public function updateEntity(Employee $employee)
    {
        $this->storageXml->deleteByUuid($employee->getUuid());
        $this->storageXml->storeEntity($employee);
        $this->storageXml->save();
    }

    public function deleteEntityByUuid(string $uuid)
    {
        $this->storageXml->deleteByUuid($uuid);
        $this->storageXml->save();
    }

    public function sort(Employee $a, Employee $b)
    {
        $collator = new Collator("sk_SK");
        return $collator->compare($a->getLastName(), $b->getLastName());
    }

    public function getChartData()
    {
        return array_reduce($this->findAll(),
            function($carry, $item) {
                $carry[] = [$item->getLastName(), $item->getAge()];
                return $carry;
            }, 
            []);
    }

}
