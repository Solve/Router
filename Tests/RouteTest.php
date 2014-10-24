<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 23.10.14 20:08
 */

namespace Solve\Router\Tests;

require_once __DIR__ . '/../UriService.php';
require_once __DIR__ . '/../Route.php';

use Solve\Router\Route;

class RouteTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $route = new Route();
        $route->setName('products');
        $route->setUriPattern('products/{category}/{id}?');
        $route->setConfig(array(
            'controller' => 'IndexController'
        ));
        $url = $route->getUri(array('category'=>'macbooks', 'id'=>'air'));
        $this->assertEquals('products/macbooks/air', $url, 'getUri works fine');

        $this->assertEquals('products/', Route::createInstance('products', 'products/')->getUri(), 'inline creation');
        $this->assertEquals('products', $route, '__toString');
    }

}
 