<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;

class RequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testIp()
    {
        $matcher = new RequestMatcher();

        $matcher->matchIp('192.168.1.1/1');
        $request = Request::create('', 'get', array(), array(), array(), array('REMOTE_ADDR' => '192.168.1.1'));
        $this->assertTrue($matcher->matches($request));

        $matcher->matchIp('192.168.1.0/24');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchIp('1.2.3.4/1');
        $this->assertFalse($matcher->matches($request));
    }

    public function testMethod()
    {
        $matcher = new RequestMatcher();

        $matcher->matchMethod('get');
        $request = Request::create('', 'get');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchMethod('post');
        $this->assertFalse($matcher->matches($request));

        $matcher->matchMethod(array('get', 'post'));
        $this->assertTrue($matcher->matches($request));
    }

    public function testHost()
    {
        $matcher = new RequestMatcher();

        $request = Request::create('', 'get', array(), array(), array(), array('HTTP_HOST' => 'foo.example.com'));

        $matcher->matchHost('.*\.example\.com');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchHost('\.example\.com$');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchHost('^.*\.example\.com$');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchMethod('.*\.sensio\.com');
        $this->assertFalse($matcher->matches($request));
    }

    public function testPath()
    {
        $matcher = new RequestMatcher();

        $request = Request::create('/admin/foo');

        $matcher->matchPath('/admin/.*');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchPath('/admin');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchPath('^/admin/.*$');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchMethod('/blog/.*');
        $this->assertFalse($matcher->matches($request));
    }

    public function testAttributes()
    {
        $matcher = new RequestMatcher();

        $request = Request::create('/admin/foo');
        $request->attributes->set('foo', 'foo_bar');

        $matcher->matchAttribute('foo', 'foo_.*');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchAttribute('foo', 'foo');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchAttribute('foo', '^foo_bar$');
        $this->assertTrue($matcher->matches($request));

        $matcher->matchAttribute('foo', 'babar');
        $this->assertFalse($matcher->matches($request));
    }
}

