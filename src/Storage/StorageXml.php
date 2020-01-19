<?php

namespace App\Storage;

use App\Entity\Employee;
use Exception;
use SimpleXMLElement;

libxml_use_internal_errors(true);

class StorageXml
{
    const EMPLOYEE = 'employee';
    const DEFAULT_XML_OBJECT = "<?xml version='1.0' encoding='UTF-8'?><employees></employees>";

    private $xmlObj;
    private $filename;

    public function __construct()
    {
        $filename = 'test.xml';
        $this->filename = $filename;
        if (!file_exists($filename)) {
            $this->xmlObj = new SimpleXMLElement(self::DEFAULT_XML_OBJECT);
            $this->save($filename);
    
        } else {
            $this->loadFile();
        }
    }

    public function loadFile()
    {
        libxml_use_internal_errors(true);
        $this->xmlObj = simplexml_load_file($this->filename);
        if (!$this->xmlObj instanceof SimpleXMLElement) {
            $error_messages = '';
            foreach (libxml_get_errors() as $error) {
                $error_messages .= $error->message . PHP_EOL;
            }
            throw new Exception('Unable to load file ' . $error_messages);
        }
    }
    public function storeEntity(Employee $employee)
    {
        $entity = $this->xmlObj->addChild(StorageXml::EMPLOYEE);
        $entity->addAttribute('uuid', $employee->getUuid() ?: $this->generateUuid());
        $entity->addChild('role', strtolower($employee->getClassName()));
        foreach ($employee as $prop => $key) {
            $entity->addChild($prop, $key);
        }

        return $this;
    }

    public function save()
    {
        $this->xmlObj->asXml($this->filename);
    }

    public function getXml()
    {
        return $this->xmlObj;
    }

    public function generateUuid()
    {
        return bin2hex(random_bytes(4));
    }

    public function searchByUuid(string $uuid)
    {
        return $this->getXml()->xpath('//employee[@uuid="' . $uuid . '"]');
    }

    public function deleteByUuid(string $uuid)
    {
        unset($this->getXml()->xpath('//employee[@uuid="' . $uuid . '"]')[0][0]);
        return $this;
    }
}
