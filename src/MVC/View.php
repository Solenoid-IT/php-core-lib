<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\App\App;



class View
{
    public App $app;



    # Returns [self]
    public function __construct (App &$app)
    {
        // (Getting the value)
        $this->app = &$app;
    }



    # Returns [string] | Throws [Exception]
    public function build (string $blade_file_path, array $kv_data = [])
    {
        // (Building the content with blade)
        return $this->app->blade->build( $blade_file_path, $kv_data );

    }

    # Returns [string] | Throws [Exception]
    public function build_html (string $blade_file_path, array $kv_data = [], array $js_vars = [])
    {
        // Returning the value
        return $this->app->blade->build_html( $blade_file_path, $kv_data, $js_vars );
    }
}



?>