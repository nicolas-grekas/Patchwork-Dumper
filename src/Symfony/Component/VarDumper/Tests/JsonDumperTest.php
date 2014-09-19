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
use Symfony\Component\VarDumper\Dumper\JsonDumper;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class JsonDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        require __DIR__.'/Fixtures/dumb-var.php';

        $decPoint = (string) 0.5;
        $decPoint = $decPoint[1];
        $dumper = new JsonDumper();
        $cloner = new PhpCloner();
        $cloner->addCasters(array(
            ':stream' => function ($res, $a) {
                unset($a['uri']);

                return $a;
            }
        ));
        $var['dumper'] = $dumper;
        $data = $cloner->cloneVar($var);

        $var['file'] = str_replace('\\', '\\\\', $var['file']);

        $json = array();
        $dumper->dump($data, function ($line, $depth) use (&$json) {
            if (-1 !== $depth) {
                $json[] = str_repeat('  ', $depth).$line;
            }
        });
        $json = implode("\n", $json);
        $closureLabel = PHP_VERSION_ID >= 50400 ? 'public method' : 'function';

        $this->assertSame(
'{"_":"1:array:26",
  "number": 1,
  "n`0": null,
  "const": 1.1,
  "n`1": true,
  "n`2": false,
  "n`3": "n`NAN",
  "n`4": "n`INF",
  "n`5": "n`-INF",
  "n`6": "n`'.PHP_INT_MAX.'",
  "str": "déjà",
  "n`7": "b`é",
  "[]": [],
  "res": {"_":"14:resource:stream",
    "wrapper_type": "plainfile",
    "stream_type": "STDIO",
    "mode": "r",
    "unread_bytes": 0,
    "seekable": true,
    "timed_out": false,
    "blocked": true,
    "eof": false,
    "options": []
  },
  "n`8": {"_":"24:resource:Unknown"},
  "obj": {"_":"25:Symfony\\\\Component\\\\VarDumper\\\\Tests\\\\Fixture\\\\DumbFoo",
    "foo": "foo",
    "+:bar": "bar"
  },
  "closure": {"_":"28:Closure",
    "~:reflection": "Closure [ <user> '.$closureLabel.' Symfony\\\\Component\\\\VarDumper\\\\Tests\\\\Fixture\\\\{closure} ] {\n  @@ '.$var['file'].' '.$var['line'].' - '.$var['line'].'\n\n  - Parameters [2] {\n    Parameter #0 [ <required> $a ]\n    Parameter #1 [ <optional> PDO or NULL &$b = NULL ]\n  }\n}\n"
  },
  "line": '.$var['line'].',
  "nobj": [
    {"_":"32:stdClass"}
  ],
  "recurs": [
    "R`34:33"
  ],
  "n`9": "R`35:3",
  "sobj": "r`36:25",
  "snobj": "R`37:32",
  "snobj2": "r`38:32",
  "file": "'.$var['file'].'",
  "b`bin-key-é": "",
  "dumper": {"_":"41:Symfony\\\\Component\\\\VarDumper\\\\Dumper\\\\JsonDumper",
    "*:position": 0,
    "*:refsPos": [],
    "*:refs": [],
    "*:line": "",
    "*:lineDumper": [
      "r`47:41",
      "echoLine"
    ],
    "*:outputStream": {"_":"49:resource:stream",
      "wrapper_type": "PHP",
      "stream_type": "Output",
      "mode": "wb",
      "unread_bytes": 0,
      "seekable": false,
      "timed_out": false,
      "blocked": true,
      "eof": false,
      "options": []
    },
    "*:decimalPoint": "'.$decPoint.'",
    "*:indentPad": "  "
  },
  "__refs": {"33":[-34],"3":[-35],"25":[36],"32":[-37,38],"41":[47]}
}',
            $json
        );
    }
}
