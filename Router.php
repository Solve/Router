<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 22.10.14 00:47
 */

namespace Solve\Router;


use Solve\Storage\ArrayStorage;

class Router {

    private $_routes;

    /**
     * @var Router
     */
    private static $_instance;

    public function __construct() {
        $this->_routes = new ArrayStorage();
    }

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new Router();
        }
        return self::$_instance;
    }

    public function addRoute($name, $params) {
        $this->_routes->offsetSet($name, $params);
        return $this;
    }

    public function getRoutes() {
        return $this->_routes;
    }

    public function removeRoute($name) {
        $this->_routes->offsetUnset($name);
        return $this;
    }

} 