<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 22.10.14 01:01
 */

namespace Solve\Router;


use Solve\Http\Request;

class Route {

    /**
     * @var string
     */
    private $_name;
    /**
     * @var Request
     */
    private $_request;
    private $_uriPattern;
    private $_config;
    private $_vars       = array();
    private $_isNotFound = false;

    public function __construct($name = null, $uriPattern = null, $config = array()) {
        $this->_name       = $name;
        $this->_uriPattern = $uriPattern;
        $this->_config     = $config;
    }

    public static function createInstance($name = null, $uriPattern = null, $config = array()) {
        return new static($name, $uriPattern, $config);
    }

    public static function createNotFoundInstance() {
        $route = new static('notFoundRoute');
        $route->_isNotFound = true;
        return $route;
    }

    public function buildUri($vars = null) {
        $vars = (array)$vars;
        foreach($this->_config as $key=>$value) {
            if (!array_key_exists($key, $vars) && preg_match('#[-_a-z0-9]+#', $value)) {
                $vars[$key] = $value;
            }
        }
        foreach ($vars as $key => $value) {
            $this->_vars[$key] = $value;
        }
        $uri = UriService::buildUriFromPattern($this->_uriPattern, $this->_vars);
        return $uri;
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
     * @return Request
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request) {
        $this->_request = $request;
    }


    /**
     * @return array
     */
    public function getVars() {
        return $this->_vars;
    }

    public function setVars($vars) {
        $this->_vars = $vars;
        return $this;
    }

    public function setVar($name, $value) {
        $this->_vars[$name] = $value;
        return $this;
    }

    public function getVar($name, $default = null) {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : $default;
    }

    public function getRequestVar($name, $defaultValue = null) {
        if (!$this->_request) return null;
        return $this->_request->getVar($name, $defaultValue);
    }

    public function getRequestVars() {
        if (!$this->_request) return null;
        return $this->_request->getVars();
    }

    public function setIsNotFound($isNotFound) {
        $this->_isNotFound = $isNotFound;
        return $this;
    }

    public function isNotFound() {
        return $this->_isNotFound;
    }

    public function isExists() {
        return !$this->_isNotFound;
    }

    public function __toString() {
        return $this->_name;
    }
}