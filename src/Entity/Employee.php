<?php

namespace App\Entity;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class Employee
{
    const FORM_CHOICES = [
        'Office staff' => 'Admin',
        'Support staff' => 'User',
        'Developer staff' => 'Developer'
    ];

    const GENDER_CHOICES = [
        'male' => 'M',
        'female' => 'F',
    ];

    const SHIFT_CHOICES = [
        'Day' => 'day',
        'Afternoon' => 'afternoon',
        'Float' => 'float',
    ];

    /**
     * @var string
     */
    private ?string $uuid = null;

    /**
     * @Assert\NotBlank,
     * @Assert\Length(min=3)
     * 
     */
    public ?string $firstName = null;


    /**
     * @Assert\NotBlank,
     * @Assert\Length(min=3)
     * 
     */
    public ?string $lastName = null;

    /**
     * @Assert\Choice({"M", "F"})
     */
    public ?string $gender = null;

    /**
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    public ?string $dob = null;

    /**
     * @Assert\Email()
     * @var string
     */
    public ?string $email = null;

    /**
     * @Assert\NotBlank,
     * @Assert\Length(min=3)
     * 
     */
    public ?string $shift = null;
    
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getShowGender(): ?string
    {
        return array_flip(self::GENDER_CHOICES)[$this->getGender()];
    }

    public function getDob(): ?string
    {
        return $this->dob;
    }

    public function setDob(string $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getShift(): ?string
    {
        return array_flip(self::SHIFT_CHOICES)[$this->shift];
    }

    public function setShift(string $shift): self
    {
        $this->shift = $shift;

        return $this;
    }

    public function getAge(): ?string
    {
        $from = new DateTimeImmutable($this->getDob());
        $to   = new DateTimeImmutable('today');
        return $from->diff($to)->format("%y");
    }

    public function setEmployee(
            string $firstName, 
            string $lastName, 
            string $gender, 
            string $dob, 
            string $email,
            string $shift, 
            string $uuid = null)
    {

        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setGender($gender);
        $this->setDob($dob);
        $this->setUuid($uuid);
        $this->setEmail($email);
        $this->setShift($shift);

        return $this;
    }

    public function getClassName()
    {
        $path = explode('\\', get_class($this));
        return array_pop($path);
    }

    public function getRole()
    {
        return array_flip(self::FORM_CHOICES)[$this->getClassName()];
    }
}
