<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 22.10.14 00:58
 */

namespace Solve\Router\Tests;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../UriService.php';
require_once __DIR__ . '/../Route.php';
require_once __DIR__ . '/../Router.php';

use Solve\Http\Request;
use Solve\Router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $testRoutes = array(
            'products'    => '/products/',
            'productInfo' => array(
                'pattern'    => '/products/{id}/{action}?',
                'controller' => 'Product'
            ),
        );

        $router = Router::getMainInstance();
        $this->assertEquals('/', $router->getWebRoot(), 'initial web root');

        $router->setWebRoot('');
        $this->assertEquals('/', $router->getWebRoot(), 'after empty set web root');
        $router->setWebRoot('/');
        $this->assertEquals('/', $router->getWebRoot(), 'after slash set web root');

        $router->addRoutes($testRoutes);
        $route = $router->detectRouteForUri('products/');
        $this->assertTrue($route->isExists(), 'Route found');

        $route = $router->detectRouteForUri('categories/');
        $this->assertTrue($route->isNotFound(), 'Route not found');

        $route = $router->detectRouteForUri('/products/1/edit');
        $this->assertEquals('productInfo', $route->getName(), 'route name is ok');
        $this->assertEquals('1', $route->getVar('id'), 'route id is ok');
        $this->assertEquals('edit', $route->getVar('action'), 'route action is ok');

        $route = $router->detectRouteForUri('/products/1/');
        $this->assertEquals('1', $route->getVar('id'), 'route id is ok');
        $this->assertNull($route->getVar('action'), 'route action is ok (null)');

    }

    public function testWebRoot() {
        $router = Router::getMainInstance();
        $router->setWebRoot('project1');

        $route = $router->detectRouteForUri('/products/');
        $this->assertEquals('webRootNotFound', $route, 'web root specified but not found');

        $route = $router->detectRouteForUri('/project1/products/');
        $this->assertEquals('products', $route, 'found products route in web folder');
    }

    public function testRequestProcess() {
        $router                    = Router::getMainInstance();
        $_SERVER['REQUEST_METHOD'] = Request::METHOD_GET;
        $_SERVER['REQUEST_URI']    = '/products/2/?id=12';
        $_SERVER['QUERY_STRING']   = 'id=12';
        $_SERVER['HTTP_HOST']      = 'test.com';
        $_SERVER['DOCUMENT_ROOT']      = '/';
        $request                   = Request::getIncomeRequest();
        $route = $router->processRequest($request)->getCurrentRoute();
        $this->assertTrue($route->isNotFound(), 'route not found without subfolder');

        $_SERVER['REQUEST_URI']    = 'project1/products/2/?id=12';
        $newIncomeRequest          = Request::createInstance()->processEnvironment();
        $route = $router->processRequest($newIncomeRequest)->getCurrentRoute();
        $this->assertEquals('Product', $route->getVar('controller'), 'Route detected');
        $this->assertEquals(12, $route->getRequestVar('id'), 'request var detected');
    }

}
