<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Employee;
use DOMDocument;
use Exception;
use SimpleXMLElement;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

libxml_use_internal_errors(true);

class StorageXml
{
    const EMPLOYEE = 'employee';
    const DEFAULT_XML_OBJECT = "<?xml version='1.0' encoding='UTF-8'?><employees/>";

    private SimpleXmlElement $xmlObj;
    private string $filename;

    public function __construct(ParameterBagInterface $params)
    {
       
        $filename = $params->get('storage_filename');
        $this->filename = $filename;
        if (!file_exists($filename)) {
            $this->xmlObj = new SimpleXMLElement(self::DEFAULT_XML_OBJECT);
            $this->saveToFile();
        } else {
            $this->loadFile();
        }
    }

    public function loadFile(): void
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
    public function storeEntity(Employee $employee): StorageXml
    {
        $entity = $this->xmlObj->addChild(StorageXml::EMPLOYEE);
        $entity->addAttribute('uuid', $employee->getUuid() ?: $this->generateUuid());
        $entity->addChild('role', strtolower($employee->getClassName()));
        foreach ($employee as $prop => $key) {
            $entity->addChild($prop, $key);
        }

        return $this;
    }

    public function saveToFile(): void
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($this->xmlObj->asXML());
        $dom->save($this->filename);
    }

    public function getXml(): SimpleXMLElement
    {
        return $this->xmlObj;
    }

    public function generateUuid(): string
    {
        return bin2hex(random_bytes(4));
    }

    /**  
     * @return SimpleXMLElement[] 
     */
    public function searchByUuid(string $uuid)
    {
        return $this->getXml()->xpath('//employee[@uuid="' . $uuid . '"]');
    }

    public function deleteByUuid(string $uuid): StorageXml
    {
        // removing element via self-reference ($x: SimpleXMLElement === $sxe[0]: SimpleXMLElement)
        unset($this->getXml()->xpath('//employee[@uuid="' . $uuid . '"]')[0][0]);
        return $this;
    }
}
