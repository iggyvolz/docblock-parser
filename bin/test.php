<?php

declare(strict_types=1);

use iggyvolz\DocBlockParser\Tag;
use iggyvolz\DocBlockParser\Subtag;
use iggyvolz\DocBlockParser\DocBlockParser;

require_once __DIR__ . "/../vendor/autoload.php";
/**
 * fin
 * bax @foo bar bak @bin bar @boo bay @boo yak
 */
class foo
{
}
class FooTag extends Tag
{
    public function doSomething(): void
    {
        echo "I'm a foo tag!" . PHP_EOL;
    }
}
class BooSubtag extends Subtag
{
    public function doSomething(): void
    {
        echo "I'm a subtag, and my value is " . $this->Description . PHP_EOL;
    }
}
$parser = new DocBlockParser();
$parser->addTagConverter("foo", FooTag::class);
$parser->addSubtagConverter("boo", BooSubtag::class);
$docblock = $parser->parseClassDocblock(foo::class);
var_dump($docblock);
$fooTag = $docblock->getTagByName("foo");
$fooTag->doSomething();
$booSubtags = $fooTag->getSubtagsByName("boo");
foreach ($booSubtags as $booSubtag) {
    $booSubtag->doSomething();
}
/*
Output:
object(iggyvolz\DocBlockParser\DocBlock)#4 (2) {
  ["description":"iggyvolz\DocBlockParser\DocBlock":private]=>
  string(7) "fin
bax"
  ["tags":"iggyvolz\DocBlockParser\DocBlock":private]=>
  array(1) {
    [0]=>
    object(FooTag)#5 (3) {
      ["name":"iggyvolz\DocBlockParser\Tag":private]=>
      string(3) "foo"
      ["description":"iggyvolz\DocBlockParser\Tag":private]=>
      string(7) "bar bak"
      ["tags":"iggyvolz\DocBlockParser\Tag":private]=>
      array(3) {
        [1]=>
        object(iggyvolz\DocBlockParser\Subtag)#6 (2) {
          ["name":"iggyvolz\DocBlockParser\Subtag":private]=>
          string(3) "bin"
          ["description":"iggyvolz\DocBlockParser\Subtag":private]=>
          string(3) "bar"
        }
        [2]=>
        object(BooSubtag)#7 (2) {
          ["name":"iggyvolz\DocBlockParser\Subtag":private]=>
          string(3) "boo"
          ["description":"iggyvolz\DocBlockParser\Subtag":private]=>
          string(3) "bay"
        }
        [3]=>
        object(BooSubtag)#8 (2) {
          ["name":"iggyvolz\DocBlockParser\Subtag":private]=>
          string(3) "boo"
          ["description":"iggyvolz\DocBlockParser\Subtag":private]=>
          string(3) "yak"
        }
      }
    }
  }
}
I'm a foo tag!
I'm a subtag, and my value is bay
I'm a subtag, and my value is yak
*/
