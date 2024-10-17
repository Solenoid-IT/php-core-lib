<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\App\App;



class View
{
    private static $instance;

    protected static App $app;



    # Returns [self]
    private function __construct (App &$app)
    {
        // (Getting the value)
        self::$app = &$app;
    }



    # Returns [self]
    public static function init (App &$app)
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self($app);
        }



        // Returning the value
        return self::$instance;
    }



    # Returns [string] | Throws [Exception]
    public static function build (string $blade_file_path, array $kv_data = [])
    {
        // (Building the content with blade)
        return self::$app->blade->build( $blade_file_path, $kv_data );

    }

    # Returns [string] | Throws [Exception]
    public static function build_html (string $blade_file_path, array $kv_data = [], array $js_vars = [])
    {
        // Returning the value
        return self::$app->blade->build_html( $blade_file_path, $kv_data, $js_vars );
    }
}



?>