<?php

namespace Alpha\App\Hooks\Handlers;

use Alpha\App\App;

class Menu
{
    public function add()
    {
        $capability = 'manage_options';

        add_menu_page(
            __('Alpha', 'alpha'),
            'Alpha',
            $capability,
            'alpha',
            array($this, 'renderApp'),
            // 'dashicons-money-alt',
            $this->getMenuIcon(),
            6
        );
    }

    public function renderApp()
    {
        $view = App::make('view');
        $this->enqueueAssets();
        $view->render('admin.menu');
    }

    public function enqueueAssets()
    {
        $app = App::getInstance();

        $assets = $app['url.assets'];

        wp_enqueue_script(
            'alpha_admin_app_boot',
            $assets . '/admin/js/boot.js',
            array('jquery')
        );

        wp_enqueue_style(
            'alpha_admin_app', $assets . '/admin/css/alpha-admin.css'
        );

        wp_localize_script('alpha_admin_app_boot', 'alphaAdmin', array(
            'slug'  => $slug = $app->config->get('app.slug'),
            'nonce' => wp_create_nonce($slug),
            'rest'  => $this->getRestInfo($app),
            // 'routes'  => $this->getRoutes($app),
            'brand_logo' => $this->getMenuIcon(),
            'total' => $this->getTotalExpense(),
            'firstEntry' => '',
            'lastEntry' => '',
            'asset_url' => $assets
        ));

        do_action('alpha_loading_app');

        wp_enqueue_script(
            'alpha_admin_app_start',
            $assets . '/admin/js/start.js',
            array('alpha_admin_app_boot'),
            '1.0',
            true
        );

        wp_enqueue_script(
            'alpha_admin_app_vendor',
            $assets . '/admin/js/vendor.js',
            array('alpha_admin_app_start'),
            '1.0',
            true
        );
    }

    protected function getRestInfo($app)
    {
        $ns = $app->config->get('app.rest_namespace');
        $v = $app->config->get('app.rest_version');

        return [
            'base_url'  => esc_url_raw(rest_url()),
            'url'       => rest_url($ns . '/' . $v),
            'nonce'     => wp_create_nonce('wp_rest'),
            'namespace' => $ns,
            'version'   => $v,
        ];
    }

    // protected function getRoutes($app)
    // {
    //     $request = new \WP_REST_Request('GET', '/alpha/v2');
    //     $response = rest_do_request( $request );
    //     $server = rest_get_server();
    //     $data = $server->response_to_data($response, false);

    //     $routes= [];
        
    //     foreach ($data['routes'] as $route) {
    //         if (isset($route['_links'])) {
    //             $routes[$route['_links']['self'][0]['href']] = $route['methods'];
    //         }
    //     }

    //     return $routes;
    // }

    protected function getMenuIcon()
    {
        $app = App::getInstance();

        $assets = $app['url.assets'];

        return 'data:image/svg+xml;base64,' . base64_encode(
            file_get_contents($assets . '/images/logo.svg')
        );
    }

    protected function getTotalExpense()
    {
        return \Alpha\App\Models\Entry::sum('amount');
    }
}
