<?php

declare(strict_types=1);

namespace iggyvolz\DocBlockParser;

use LogicException;

/**
 * @property string $Name
 * @property string $Description
 * @property Subtag[] $Tags
 */
class Tag
{
    public function __construct(string $name, string $description, array $tags)
    {
        $this->name = $name;
        $this->description = $description;
        $this->tags = $tags;
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
    private array $tags;
    /**
     * @return Subtag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function __get(string $var)
    {
        switch ($var) {
            case "Name":
                return $this->getName();
            case "Description":
                return $this->getDescription();
            case "Tags":
                return $this->getTags();
            default:
                throw new LogicException("Invalid property $var on " . self::class);
        }
    }


    /**
     * @return Subtag[]
     */
    public function getSubtagsByName(string $name): array
    {
        return array_values(array_filter($this->tags, fn(Subtag $t):bool => $t->Name === $name));
    }

    public function getSubtagByName(string $name): ?Subtag
    {
        return $this->getSubtagsByName($name)[0] ?? null;
    }
}
