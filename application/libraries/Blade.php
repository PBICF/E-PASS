<?php
defined('BASEPATH') or exit('No direct script access allowed');

use eftec\bladeone\BladeOne;

class Blade
{
    protected $CI;
    protected $blade;

    public function __construct()
    {
        $views = APPPATH . 'views';          // where your blade templates live
        $cache = APPPATH . 'cache/blade';    // cache folder (create if missing)

        if (!is_dir($cache)) {
            mkdir($cache, 0777, true);
        }

        $this->CI = &get_instance();
        $this->blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);

        // Register custom directives
        $this->registerDirectives();
    }

    public function view($view, $variables = [])
    {
        echo $this->blade->run($view, $variables);
        return $this->blade;
    }

    protected function registerDirectives()
    {
        /**
         * Asset directive
         * Example: asset helper => @asset('css/app.css')
         */
        $this->blade->directive('asset', fn ($file) => "base_url(trim($file, \"'\"))");

        /**
         * CSRF directive
         * Example: @csrf
         */
        $this->blade->directive('csrf', function () {
            return '<?= \'<input type="hidden" name="\'.csrf_token().\'" value="\'.csrf_hash().\'">\'; ?>';
        });
    }
}
