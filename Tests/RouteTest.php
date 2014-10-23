<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 23.10.14 20:08
 */

namespace Solve\Router\Tests;


use Solve\Router\Route;

class RouteTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $route = new Route();
        $route->setName('products');
        $route->setUriPattern('products/{category}/{id}?');
        $route->setup(array(
            'controller' => 'IndexController'
        ));
        $route->generateUri();
    }

}
 