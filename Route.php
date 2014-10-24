<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 22.10.14 01:01
 */

namespace Solve\Router;


class Route {

    /**
     * @var string
     */
    private $_name;
    private $_uriPattern;
    private $_config;
    private $_vars = array();

    public function __construct($name = null, $uriPattern = null, $config = array()) {
        $this->_name = $name;
        $this->_uriPattern = $uriPattern;
        $this->_config = $config;
    }

    public static function createInstance($name = null, $uriPattern = null, $config = array()) {
        return new static($name, $uriPattern, $config);
    }

    public function getUri($vars = array()) {
        if (!empty($vars)) {
            foreach ($vars as $key => $value) {
                $this->_vars[$key] = $value;
            }
        }
        return UriService::buildUrlFromPattern($this->_uriPattern, $this->_vars);
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * @return mixed
     */
    public function getUriPattern() {
        return $this->_uriPattern;
    }

    /**
     * @param mixed $uriPattern
     */
    public function setUriPattern($uriPattern) {
        $this->_uriPattern = $uriPattern;
    }

    /**
     * @return array
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config) {
        $this->_config = $config;
    }

    /**
     * @return array
     */
    public function getVars() {
        return $this->_vars;
    }

    /**
     * @param array $vars
     */
    public function setVars($vars) {
        $this->_vars = $vars;
    }

    public function getVar($name, $default = null) {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : $default;
    }

    public function __toString() {
        return $this->_name;
    }
}