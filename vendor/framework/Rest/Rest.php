<?php

namespace Alpha\Framework\Rest;

use Alpha\Framework\Support\Arr;

class Rest
{
    protected $app = null;
    
    protected $name = [];
    
    protected $prefix = [];

    protected $routes = [];

    protected $routeGroups = [];
    
    protected $groupStack = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function prefix($prefix)
    {
        $this->prefix[] = $prefix;

        return $this;
    }

    public function name($name)
    {
        $this->name[] = $name;

        return $this;
    }

    public function group($attributes = [], \Closure $callback = null)
    {
        if ($attributes instanceof \Closure) {
            $callback = $attributes;
            $attributes = [];
        }

        if ($prefix = $this->prefix) {
            $attributes['prefix'] = $prefix;
        }

        if ($name = $this->name) {
            $attributes['name'] = $name;
        }

        call_user_func($callback, $this->app);
        array_pop($this->prefix);
        array_pop($this->name);
    }

    public function get($uri, $handler)
    {
        $this->routes[] = $route = $this->newRoute(
            $uri, $handler, \WP_REST_Server::READABLE
        );

        return $route;
    }

    public function post($uri, $handler)
    {
        $this->routes[] = $route = $this->newRoute(
            $uri, $handler, \WP_REST_Server::CREATABLE
        );

        return $route;
    }

    public function put($uri, $handler)
    {
        $this->routes[] = $route = $this->newRoute(
            $uri, $handler, \WP_REST_Server::EDITABLE
        );

        return $route;
    }

    public function patch($uri, $handler)
    {
        $this->routes[] = $route = $this->newRoute(
            $uri, $handler, \WP_REST_Server::EDITABLE
        );

        return $route;
    }

    public function delete($uri, $handler)
    {
        $this->routes[] = $route = $this->newRoute(
            $uri, $handler, \WP_REST_Server::DELETABLE
        );

        return $route;
    }

    public function any($uri, $handler)
    {
        $this->routes[] = $route = $this->newRoute(
            $uri, $handler, \WP_REST_Server::ALLMETHODS
        );

        return $route;
    }

    protected function newRoute($uri, $handler, $method)
    {
        $uri = trim($uri, '/');

        // $options = debug_backtrace(false, 4)[3]['args'];

        // if ($options && count($options) > 1) {
        //     $options = $options[2];

        //     if (isset($options['prefix'])) {
        //         $prefix = $options['prefix'];
        //         $uri = $prefix.'/'.$uri;
        //     }

        //     if (isset($options['policy'])) {
        //         $policy = $options['policy'];
        //     }
        // }

        $prefix = array_map(function($prefix) {
            return trim($prefix, '/');
        }, $this->prefix);

        $prefix = implode('/', $prefix);

        $prefix = trim($prefix, '/') . '/' . trim($uri, '/');

        $route = new Route(
            $this->app,
            $this->getRestNamespace(),
            $prefix,
            $handler,
            $method,
            implode('', $this->name)
        );

        if (isset($policy)) {
            $route->withPolicy($policy);
        }

        return $route;
    }

    protected function getRestNamespace()
    {
        $version = $this->app->config->get('app.rest_version');

        $namespace = $this->app->config->get('app.rest_namespace');

        return "{$namespace}/{$version}";
    }

    public function registerRoutes()
    {
        // foreach ($this->routeGroups as $group) $group->register();

        foreach ($this->routes as $route) $route->register();
    }
}
