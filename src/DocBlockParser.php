<?php

declare(strict_types=1);

namespace iggyvolz\DocBlockParser;

use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionProperty;
use ReflectionClassConstant;
use ReflectionFunctionAbstract;

class DocBlockParser
{
    private array $tagConversions = [];
    private array $subtagConversions = [];
    public function addTagConverter(string $tag, string $tagClass): void
    {
        $this->tagConversions[$tag] = $tagClass;
    }
    public function addSubtagConverter(string $tag, string $tagClass): void
    {
        $this->subtagConversions[$tag] = $tagClass;
    }
    public function parseClassDocblock(string $class): DocBlock
    {
        return $this->parseDocblockFromReflection(new ReflectionClass($class));
    }
    public function parseMethodDocblock(string $class, string $method): DocBlock
    {
        return $this->parseDocblockFromReflection(new ReflectionMethod($class, $method));
    }
    public function parseFunctionDocblock(string $function): DocBlock
    {
        return $this->parseDocblockFromReflection(new ReflectionFunction($function));
    }
    public function parsePropertyDocblock(string $class, string $property): DocBlock
    {
        return $this->parseDocblockFromReflection(new ReflectionProperty($class, $property));
    }
    public function parseClassConstantDocblock(string $class, string $constant): DocBlock
    {
        return $this->parseDocblockFromReflection(new ReflectionClassConstant($class, $constant));
    }
    /**
     * @param ReflectionClass|ReflectionFunctionAbstract|ReflectionClassConstant|ReflectionProperty $refl
     */
    public function parseDocblockFromReflection($refl): DocBlock
    {
        $docblock = $refl->getDocComment();
        if ($docblock === false) {
            $docblock = "";
        }
        return $this->parseDocblock($docblock);
    }
    public function parseDocblock(string $block): DocBlock
    {
        // Get the description - anything after / and *, but before @ tags
        preg_match_all('/^[\/\s*]*([^@\r\n]*)/m', $block, $matches);
        $descriptions = $matches[1];
        // Trim all descriptions, and filter out blank ones
        $descriptions = array_map(fn(string $s):string => trim($s), $descriptions);
        $descriptions = array_filter($descriptions, fn(string $s):bool => !empty($s));

        // Get the tags
        // https://stackoverflow.com/a/12236825
        preg_match_all('/@(\w+)\s*(.*)\r?\n/m', $block, $matches);
        $tags = array_combine($matches[1], $matches[2]);
        array_walk($tags, function (&$value, $key): void {
            if (preg_match("/^(.*)\s*(@.+)$/U", $value, $matches)) {
                [$_, $description, $subtag_texts] = $matches;
                $subtags = explode("@", $subtag_texts);
                // Trim and filter
                $subtags = array_map(fn(string $s):string => trim($s), $subtags);
                $subtags = array_filter($subtags, fn(string $s):bool => !empty($s));
                $subtags = array_map(function (string $s): Subtag {
                    preg_match("/^(\w+)\s*(.*)$/", $s, $matches);
                    [$_, $tag, $description] = $matches;
                    $description = trim($description);
                    $class = $this->subtagConversions[$tag] ?? Subtag::class;
                    return new $class($tag, $description);
                }, $subtags);
                $class = $this->tagConversions[$key] ?? Tag::class;
                $value = new $class($key, $description, $subtags);
            } else {
                $class = $this->tagConversions[$key] ?? Tag::class;
                $value = new $class($key, $value, []);
            }
        });
        '@phan-var Tag[] $tags';
        return new DocBlock(implode(PHP_EOL, $descriptions), array_values($tags));
    }
}
