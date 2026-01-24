<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('view'))
{
    /**
     * BladeOne View Helper
     *
     * @param string $view
     * @param array $variables
     * @return string
     */
    function view($view, $variables = [])
    {
        return get_instance()->blade->view($view, $variables);
    }
}
