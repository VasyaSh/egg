<?php

namespace Egg;

/**
 * @author Vasilii B. Shpilchin <shpilchin@vasya.pro>
 */
class Egg {

    function __construct(\Closure $callback) {
        ($this->_($callback))();
    }

    private function _(\Closure $callback) {
        return \Closure::bind($callback, $this, Egg::class);
    }

}

//
// An example
// 
// Go to //...../egg.php?path=index/hello/Kitty
// Go to //...../egg.php?path=index/hello/Alisa
//

new Egg(function() {

    $router = $this->_(function($path) {
        if (empty($path)) {
            $path = '/';
        }
        $this->route = [
            'path' => $path,
            'controller' => 'indexController',
            'action' => 'indexAction',
            'params' => []
        ];
        $parts = explode('/', $path);
        if (count($parts) && strlen($parts[0])) {
            $this->route['controller'] = array_shift($parts) . 'Controller';
        }
        if (count($parts) && strlen($parts[0])) {
            $this->route['action'] = array_shift($parts) . 'Action';
        }
        $this->route['params'] = $parts;
        return $this->route['controller'];
    });

    $indexController = $this->_(function() {
        $indexAction = $this->_(function($params) {
            echo 'This is Egg!';
        });
        $helloAction = $this->_(function($params) {
            if ((($this->modelHello)()->checkName)($params[0])) {
                echo 'Welcome back, ', $params[0], '!';
            } else {
                echo 'Hello guest ', $params[0], '!';
            }
        });
        ${$this->route['action']}($this->route['params']);
    });

    $this->modelHello = $this->_(function() {
        $public = new \stdClass();
        $known = [
            'Kitty',
            'Vasya'
        ];
        $checkName = function($name) use ($known) {
            return in_array($name, $known);
        };
        $public->checkName = $checkName;
        return $public;
    });

    ${$router($_GET['path'])}();
});
