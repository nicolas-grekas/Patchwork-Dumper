<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Tests;

use Symfony\Component\VarDumper\Cloner\PhpCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CliDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        require __DIR__.'/Fixtures/dumb-var.php';

        $dumper = new CliDumper('php://output');
        $dumper->setColors(false);
        $cloner = new PhpCloner();
        $data = $cloner->cloneVar($var);

        ob_start();
        $dumper->dump($data);
        $out = ob_get_clean();
        $closureLabel = PHP_VERSION_ID >= 50400 ? 'public method' : 'function';
        $out = preg_replace('/[ \t]+$/m', '', $out);

        $this->assertSame(
            <<<EOTXT

[
  "number" => 1
  0 => null #1
  "const" => 1.1
  1 => true
  2 => false
  3 => NAN
  4 => INF
  5 => -INF
  6 => 9223372036854775807
  "str" => "déjà"
  7 => b"é"
  "[]" => []
  "res" => resource:stream{
    wrapper_type: "plainfile"
    stream_type: "dir"
    mode: "r"
    unread_bytes: 0
    seekable: true
    timed_out: false
    blocked: true
    eof: false
    options: []
  }
  8 => resource:Unknown{}
  "obj" => Symfony\Component\VarDumper\Tests\Fixture\DumbFoo{ #2
    foo: "foo"
    "bar": "bar"
  }
  "closure" => Closure{
    reflection: """
      Closure [ <user> {$closureLabel} Symfony\Component\VarDumper\Tests\Fixture\{closure} ] {
        @@ {$var['file']} {$var['line']} - {$var['line']}

        - Parameters [2] {
          Parameter #0 [ <required> \$a ]
          Parameter #1 [ <optional> PDO or NULL &\$b = NULL ]
        }
      }
      """
  }
  "line" => {$var['line']}
  "nobj" => [
    0 => {} #3
  ]
  "recurs" => [ #4
    0 => [&4]
  ]
  9 => null &1
  "sobj" => Symfony\Component\VarDumper\Tests\Fixture\DumbFoo{@2}
  "snobj" => {&3}
  "snobj2" => {@3}
  "file" => "{$var['file']}"
  b"bin-key-é" => ""
]

EOTXT
            ,

            $out
        );
    }
}
