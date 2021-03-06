<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 22.10.14 00:47
 */

namespace Solve\Router;


use Solve\Http\HttpStatus;
use Solve\Http\Request;
use Solve\Http\Response;
use Solve\Storage\ArrayStorage;

/**
 * Class Router
 * @package Solve\Router
 *
 * @version 1.0
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 */
class Router {

    /**
     * @var ArrayStorage
     */
    private $_routes;
    /**
     * @var Router
     */
    private static $_mainInstance;
    private        $_webRoot = '/';
    private        $_currentUri;
    private        $_currentHost;
    /**
     * @var Route
     */
    private $_currentRoute;
    private $_currentRequest;

    public function __construct() {
        $this->_routes = new ArrayStorage();
    }

    public static function getMainInstance() {
        if (!self::$_mainInstance) {
            self::$_mainInstance = new Router();
        }
        return self::$_mainInstance;
    }

    public function processRequest(Request $request) {
        $this->_currentHost    = $request->getHost();
        $this->_currentUri     = $request->getUri();
        $this->_currentRequest = $request;
        $this->detectRouteForUri($this->_currentUri);
        return $this;
    }

    public function processIncomeRequest() {
        return $this->processRequest(Request::getIncomeRequest());
    }

    public function addRoute($name, $config) {
        if (!is_array($config)) {
            $config = array('pattern' => $config);
        }
        $this->updatePatternWithWebRoot($config['pattern']);
        $this->_routes->offsetSet($name, $config);
        return $this;
    }

    public function addRoutes($routes) {
        foreach ($routes as $routeName => $routeConfig) {
            $this->addRoute($routeName, $routeConfig);
        }
        return $this;
    }

    public function getRoute($routeName) {
        $route = null;
        if ($this->_routes->has($routeName)) {
            $info = $this->_routes->get($routeName);
            $route = new Route($routeName, $info['pattern'], $info);
        }
        return $route;
    }

    public function getRoutes() {
        return $this->_routes;
    }

    public function removeRoute($name) {
        $this->_routes->offsetUnset($name);
        return $this;
    }

    /**
     * @return null
     */
    public function getWebRoot() {
        return $this->_webRoot;
    }

    public function setWebRoot($webRoot) {
        if ($webRoot) {
            if ($webRoot[0] !== '/') {
                $webRoot = '/' . $webRoot;
            }
            if (strlen($webRoot) > 1 && $webRoot[strlen($webRoot) - 1] == '/') {
                $webRoot = substr($webRoot, 0, -1);
            }
        } else {
            $webRoot = '/';
        }
        $this->_webRoot = $webRoot;
        foreach ($this->_routes as $route => $config) {
            $this->updatePatternWithWebRoot($this->_routes[$route]['pattern']);
        }
        return $this;
    }

    private function updatePatternWithWebRoot(&$pattern) {
        if (strpos($pattern, $this->_webRoot) !== 0) {
            $pattern = $this->_webRoot . ($this->_webRoot !== '/' ? '/' : '') . $pattern;
            $pattern = str_replace('//', '/', $pattern);
        }
    }

    /**
     * @param $uri
     * @return Route
     */
    public function detectRouteForUri($uri) {
        $this->_currentRoute = Route::createNotFoundInstance();
        $this->_currentRoute->setRequest($this->_currentRequest);

        if ($uri && $uri[0] !== '/') $uri = '/' . $uri;
        $this->_currentUri = $uri;

        if (strpos($uri, $this->_webRoot) !== 0) {
            $this->_currentRoute->setName('webRootNotFound');
            return $this->_currentRoute;
        }
        foreach ($this->_routes as $routeName => $routeConfig) {
            $match = UriService::matchPatternToUri($routeConfig['pattern'], $uri, $routeConfig);
            if ($match) {
                $this->_currentRoute = new Route($routeName, $routeConfig['pattern'], $routeConfig);
                $this->_currentRoute->setRequest($this->_currentRequest);
                $this->_currentRoute->setVars($match);
                break;
            }
        }
        return $this->_currentRoute;
    }

    public function redirectToRelativeUri($relativeUri) {
        if ($relativeUri == '/') {
            $relativeUri = '';
        }
        $fullUrl = $this->getBaseUri() . $relativeUri;
        $response = new Response();
        $response->setStatusCode(HttpStatus::HTTP_FOUND);
        $response->setHeader('Location', $fullUrl);
        $response->send();
        die();
    }

    public function getBaseUri() {
        return $this->_currentRequest->getProtocol() . '://' . $this->_currentRequest->getHost() . $this->_webRoot;
    }

    /**
     * @return mixed
     */
    public function getCurrentHost() {
        return $this->_currentHost;
    }

    public function setCurrentHost($currentHost) {
        $this->_currentHost = $currentHost;
        return $this;
    }

    /**
     * @return Route
     */
    public function getCurrentRoute() {
        return $this->_currentRoute;
    }

    public function setCurrentRoute($currentRoute) {
        $this->_currentRoute = $currentRoute;
        return $this;
    }

    /**
     * @return Request
     */
    public function getCurrentRequest() {
        return $this->_currentRequest;
    }

    public function setCurrentRequest($currentRequest) {
        $this->_currentRequest = $currentRequest;
        return $this;
    }

    public function getExecutionMode() {
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            return Request::MODE_CONSOLE;
        } else {
            return Request::MODE_WEB;
        }
    }


}