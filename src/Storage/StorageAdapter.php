<?php

namespace App\Storage;

use App\Entity\Employee;
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

    public function findAll(): array
    {
        $employee = $this->storageXml->getXml();
        $employeesEntityArray = [];
        foreach ($employee as $employee) {
            $employeesEntityArray[] = $this->xmlToEntity($employee);
        }

        usort($employeesEntityArray, [$this, 'sort']);

        return $employeesEntityArray;
    }

    public function searchByUuid(string $uuid): Employee
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
                $employee->shift,
                $employee->attributes()
            );
        } catch (\Throwable $e) {
            throw new Exception('Unknown entity class/type: ' . $e->getMessage());
        }

        return $entity;
    }

    public function storeEntity(Employee $employee): void
    {
        $this->storageXml->storeEntity($employee);
        $this->storageXml->saveToFile();
    }

    public function updateEntity(Employee $employee): void
    {
        $this->storageXml->deleteByUuid($employee->getUuid());
        $this->storageXml->storeEntity($employee);
        $this->storageXml->saveToFile();
    }

    public function deleteEntityByUuid(string $uuid): void
    {
        $this->storageXml->deleteByUuid($uuid);
        $this->storageXml->saveToFile();
    }

    public function sort(Employee $a, Employee $b): int
    {
        $collator = new Collator("sk_SK");
        return $collator->compare($a->getLastName(), $b->getLastName());
    }

    /**
     * @return Array[] [["Ábelovič","31"],["Barančík","30"],...]
     */
    public function getChartData()
    {
        return array_reduce(
            $this->findAll(),
            function ($carry, $item) {
                $carry[] = [$item->getLastName(), $item->getAge()];
                return $carry;
            },
            []
        );
    }
}
