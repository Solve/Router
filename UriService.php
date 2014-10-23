<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 23.10.14 20:15
 */

namespace Solve\Router;


class UriService {

    private static $_internalPatterns = array(
        'operator'   => '[-a-z0-9]+',
        'operand'    => '[-_a-z0-9]+',
        'format'     => 'html|htm|rss|xml|json'
    );
    private static $_operators = array(
        'controller', 'action', 'application'
    );

    public static function matchUri($pattern) {
        $pattern = self::buildPattern($pattern);
    }

    public static function buildPattern($sourcePattern, $vars = array()) {
        return '#^' . self::buildInternalPattern($sourcePattern, $vars) . '$#isU';
    }

    public static function buildInternalPattern($sourcePattern, $vars = array()) {
        $matches = array();
        preg_match_all('#\{(\w+)\}#is', $sourcePattern, $matches);

        if ($matches[1]) {
            foreach($matches[1] as $var) {
                if (isset($vars[$var])) {
                    $pattern = $vars[$var];
                } elseif (isset(self::$_internalPatterns[$var])) {
                    $pattern = self::$_internalPatterns[$var];
                } elseif (in_array($var, self::$_operators)) {
                    $pattern = self::$_internalPatterns['operator'];
                } else {
                    $pattern = self::$_internalPatterns['operand'];
                }
                $sourcePattern = str_replace('{'.$var.'}', '(?P<'. $var .'>'.$pattern.')', $sourcePattern);
            }
        }
        if (empty($sourcePattern) || ($sourcePattern[0] !== '/')) {
            $sourcePattern = '/' . $sourcePattern;
        }
        return $sourcePattern;
    }

    public static function buildUrlFromPattern($pattern, $vars = array()) {
        $pattern = self::removeIncompleteOptionals($pattern, $vars);

        return $pattern;
    }

    private static function removeIncompleteOptionals($pattern, $vars) {
        $reg = '#(\(([-_\d\w{}/]*)\)\?)#isU';
        $res = null;
        preg_match_all($reg, $pattern, $res);
        if (!empty($res[2])) {
            foreach ($res[2] as $key=>$varPattern) {
                foreach($vars as $varName => $varValue) {
                    $varPattern = str_replace('{'.$varName . '}', $varValue, $varPattern);
                }
                if (strpos($varPattern, '{') !== false) {
                    $varPattern = '';
                } else {
                }
                $pattern = str_replace($res[1][$key], $varPattern, $pattern);
            }
            $pattern = self::removeIncompleteOptionals($pattern, $vars);
        }
        return $pattern;
    }


} 