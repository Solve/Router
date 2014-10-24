<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 23.10.14 20:21
 */

namespace Solve\Router\Tests;

require_once __DIR__ . '/../UriService.php';

use Solve\Router\UriService;

class UriServiceTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $this->assertEquals('#^/$#isU', UriService::buildPattern(null), 'build an empty pattern');

        $this->assertEquals('/categories/', UriService::buildInternalPattern('/categories/'), 'build internal category pattern');
        $this->assertEquals('#^/categories/$#isU', UriService::buildPattern('/categories/'), 'build category pattern');

        $this->assertEquals('/(?P<controller>[-a-z0-9]+)/', UriService::buildInternalPattern('/{controller}/'), 'build controller url');
        $this->assertEquals('/(?P<action>[-a-z0-9]+)/', UriService::buildInternalPattern('/{action}/'), 'build action url');
        $this->assertEquals('/(?P<action>.*)/', UriService::buildInternalPattern('/{action}/', array('action'=>'.*')), 'build custom action url');
        $this->assertEquals('/categories/((?P<id>\d+)/)?', UriService::buildInternalPattern('/categories/({id}/)?', array('id'=>'\d+')), 'build custom parameter url');

        $this->assertEquals('/categories/1/products/1', UriService::buildUrlFromPattern('/(categories/({id})?)?/products/({id})?', array('id'=>1)), 'build hard url');
        $this->assertEquals('1', UriService::buildUrlFromPattern('{id}?', array('id'=>'1')), 'simple build with "?"');

        try {
            $url = UriService::buildUrlFromPattern('{id}');
        } catch (\Exception $e) {
            $this->assertEquals('You have to specify id for pattern', $e->getMessage(), 'Exception if no id specified');
        }

        $match = UriService::matchPatternToUri('/categories/({id}/)?', '/categories/1/');
        $this->assertEquals(array('id'=>1), $match, 'matcher works');
    }

}
 