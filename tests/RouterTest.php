<?php

/*
 * The MIT License
 *
 * Copyright 2014 felipecwb.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Felipecwb\Routing;

/**
 * RouterTest
 *
 * @author felipecwb
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    
    public function assertPreConditions()
    {
        $this->assertTrue(class_exists(__NAMESPACE__ . '\Router'));
    }

    public function testInstance()
    {
        $router = new Router(
            new Matcher(
                new RouteCollection()
            )
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Router', $router);
    }

    public function testAddRoute()
    {
        $router = new Router(
            new Matcher(
                new RouteCollection()
            )
        );
        $r1 = $router->add(new Route('|/home(.*)|', function () {
            echo "Welcome!";
        }));
        $r2 = $router->add(new Route('|/hello(?:/(\w+))?|', function ($n = 'World') {
            $n = ' ' . $n;
            echo "Hello{$n}!";
        }));

        $this->assertInstanceOf(__NAMESPACE__ . '\Route', $r1);
        $this->assertInstanceOf(__NAMESPACE__ . '\Route', $r2);
        $this->assertAttributeInstanceOf(
            __NAMESPACE__ . '\Matcher', 'matcher', $router
        );
    }

    /**
     * @dataProvider providerTestFind
     */
    public function testMatch($path)
    {
        $router = new Router(
            new Matcher(
                new RouteCollection([
                    new Route('|/home[/]?|', function () {return '/home';}),
                    new Route('|/about[/]?|', function () {return '/about';}),
                    new Route('|/contact[/]?|', function () {return '/contact';})
                ])
            )
        );

        $route = $router->match($path);

        $this->assertEquals($path, $route->call());
    }

    public function testFind2()
    {
        $route = new Route('|/home[/]?|', function () {return 'Home!';});

        $router = new Router(
            new Matcher(
                new RouteCollection()
            )
        );
        $router->add($route);

        $result = $router->match('/home');
        $this->assertSame($route, $result);
        $result = $router->match('/home/');
        $this->assertSame($route, $result);
    }

    public function testFindWithArgument()
    {
        $router = new Router(
            new Matcher(
                new RouteCollection([
                    new Route('|/name[s]?(?:/(\d+))?|', function ($id = null) {
                        $names = ['Felipe', 'Peter', 'Mary'];
                        return $id === null
                            ? $names
                            : $names[(int) $id - 1];
                    }),
                    (new Route('|/about(?:/(\w+))?|', function ($m) {
                        return "About {$m}!";
                    }))->setRules(new Rules\ConcreteRules(false))
                ])
            )
        );

        $route = $router->match('/names');
        $this->assertEquals(['Felipe', 'Peter', 'Mary'], $route->call());

        $route = $router->match('/name/1');
        $this->assertEquals('Felipe', $route->call());

        $route = $router->match('/name/2');
        $this->assertEquals('Peter', $route->call());

        $route = $router->match('/name/3');
        $this->assertEquals('Mary', $route->call());
    }

    /**
     * @dataProvider providerTestFind
     * @expectedException Felipecwb\Routing\Exception\RouteNotFoundException
     */
    public function testFindWithInvalidRules($path)
    {
        $InvalidRules = new Rules\ConcreteRules(false);
        
        $router = new Router(
            new Matcher(
                new RouteCollection()
            )
        );
        $router->add(new Route('|/home[/]?|', function () {return '/home';}))
            ->setRules($InvalidRules);

        $router->add(new Route('|/about[/]?|', function () {return '/about';}))
            ->setRules($InvalidRules);

        $router->add(new Route('|/contact[/]?|', function () {return '/contact';}))
            ->setRules($InvalidRules);

        $route = $router->match($path);

        $this->assertEquals($path, $route->call());
    }

    public function providerTestFind()
    {
        return [
            ['/home'],
            ['/about'],
            ['/contact'],
        ];
    }
}
