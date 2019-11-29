<?php

declare(strict_types=1);

namespace iggyvolz\DocBlockParser;

use LogicException;

/**
 * @property-read string $Description
 * @property-read Tag[] $Tags
 */
class DocBlock
{
    /**
     * @internal
     * @param Tag[] $tags
     */
    public function __construct(string $description, array $tags)
    {
        $this->description = $description;
        $this->tags = $tags;
    }
    private string $description;
    public function getDescription(): string
    {
        return $this->description;
    }
    /**
     * @var Tag[]
     */
    private array $tags;
    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return Tag[]
     */
    public function getTagsByName(string $name): array
    {
        return array_filter($this->tags, fn(Tag $t):bool => $t->Name === $name);
    }

    public function getTagByName(string $name): ?Tag
    {
        return $this->getTagsByName($name)[0] ?? null;
    }

    public function __get(string $var)
    {
        switch ($var) {
            case "Description":
                return $this->getDescription();
            case "Tags":
                return $this->getTags();
            default:
                throw new LogicException("Invalid property $var on " . self::class);
        }
    }
}
