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

    public static function matchPatternToUri($pattern, $uri, $vars = array()) {
        $match = null;
        preg_match(self::buildPattern($pattern, $vars), $uri, $match);
        if ($match) {
            return self::buildResult($match, $vars);
        } else {
            return false;
        }
    }

    public static function buildPattern($sourcePattern, $vars = array()) {
        return '#^' . self::buildInternalPattern($sourcePattern, $vars) . '$#isU';
    }

    protected static function buildResult($match, $vars) {
        $result = array();
        foreach($match as $key=>$value) {
            if (!is_numeric($key)) {
                $result[$key] = $value;
            }
            if (isset($vars[$key])) {
                $vars[$key] = $value;
            }
        }
        foreach($vars as $key=>$value) {
            $result[$key] = $value;
        }
        return $result;
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

    public static function buildUriFromPattern($pattern, $vars = array()) {
        $pattern = self::fillAndRemoveIncompleteOptionals($pattern, $vars);
        if (strpos($pattern, '{') !== false) {
            $matches = array();
            preg_match_all('#\{(\w+)\}#is', $pattern, $matches);
            throw new \Exception('You have to specify ' . implode(',', $matches[1]) . ' for pattern');

        }
        return $pattern;
    }

    public static function buildUriFromRoute(Route $route, $vars = array()) {
        $internalVars = $route->getConfig();
        foreach($internalVars as $key=>$value) {
            if (!array_key_exists($key, $vars) && preg_match('#'.self::$_internalPatterns['operand'].'#', $value)) {
                $vars[$key] = $value;
            }
        }
        return self::buildUriFromPattern($route->getUriPattern(), $vars);
    }

    private static function fillAndRemoveIncompleteOptionals($pattern, $vars) {
        $pattern = preg_replace('#(\{[-_\d\w/]+\})\?#', '(\1)?', $pattern);
        $reg = '#(\(([-_\d\w{}/]*)\)\?)#isU';
        $res = null;
        preg_match_all($reg, $pattern, $res);
        if (!empty($res[2])) {
            foreach ($res[2] as $key=>$varPattern) {
                $varPattern = self::updateStringWithVars($varPattern, $vars);
                if (strpos($varPattern, '{') !== false) {
                    $varPattern = '';
                }
                $pattern = str_replace($res[1][$key], $varPattern, $pattern);
            }
            $pattern = self::fillAndRemoveIncompleteOptionals($pattern, $vars);
        } else {
            $pattern = self::updateStringWithVars($pattern, $vars);
        }
        return $pattern;
    }

    private static function updateStringWithVars($string, $vars) {
        foreach($vars as $varName => $varValue) {
            $string = str_replace('{'.$varName . '}', $varValue, $string);
        }
        return $string;
    }
} 