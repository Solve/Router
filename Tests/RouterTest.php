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

use Solve\Router\Router;

class RouterTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $routes = array(
            'products' => '/products/',
            'product'  => array(
                'pattern' => '/products/{id}/{action}',
            ),
        );


        $router = new Router();
        $router->addRoutes($routes);

    }

}
 