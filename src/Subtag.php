<?php

declare(strict_types=1);

namespace iggyvolz\DocBlockParser;

use LogicException;

/**
 * @property string $Name
 * @property string $Description
 */
class Subtag
{
    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
    }
    private string $name;
    public function getName(): string
    {
        return $this->name;
    }
    private string $description;
    public function getDescription(): string
    {
        return $this->description;
    }
    public function __get(string $var)
    {
        switch ($var) {
            case "Name":
                return $this->getName();
            case "Description":
                return $this->getDescription();
            default:
                throw new LogicException("Invalid property $var on " . self::class);
        }
    }
}
