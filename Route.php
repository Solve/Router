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

    private $_name;
    private $_uriPattern;
    private $_vars;

    public function __construct($name = null, $uriPattern = null, $vars = array()) {

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

    public function setup($setupVars) {
        $this->_vars = $setupVars;
    }



}