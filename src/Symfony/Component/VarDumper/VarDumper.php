<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper;

use Symfony\Component\VarDumper\Cloner\ExtCloner;
use Symfony\Component\VarDumper\Cloner\PhpCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class VarDumper
{
    private static $handler;

    /**
     * @deprecated use dump() instead
     */
    public static function debug($var)
    {
        if (null === self::$handler) {
            $cloner = extension_loaded('symfony_debug') ? new ExtCloner() : new PhpCloner();
            $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();
            self::$handler = function ($var) use ($cloner, $dumper) {
                $dumper->dump($cloner->cloneVar($var));
            };
        }

        return call_user_func(self::$handler, array('debug() is deprecated, use dump() instead' => $var));
    }

    public static function dump($var)
    {
        if (null === self::$handler) {
            $cloner = extension_loaded('symfony_debug') ? new ExtCloner() : new PhpCloner();
            $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();
            self::$handler = function ($var) use ($cloner, $dumper) {
                $dumper->dump($cloner->cloneVar($var));
            };
        }

        return call_user_func(self::$handler, $var);
    }

    public static function setHandler($callable)
    {
        if (null !== $callable && !is_callable($callable, true)) {
            throw new \InvalidArgumentException('Invalid PHP callback.');
        }

        $prevHandler = self::$handler;
        self::$handler = $callable;

        return $prevHandler;
    }
}
