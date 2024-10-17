<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\Blade;



class View
{
    private static Blade $blade;

    public string $value;



    # Returns [self]
    public function __construct (string &$value)
    {
        // (Getting the value)
        $this->value = &$value;
    }



    # Returns [void]
    public static function config (string $views_folder_path, string $cache_folder_path)
    {
        // (Getting the value)
        self::$blade = new Blade( $views_folder_path, $cache_folder_path );
    }



    # Returns [View]
    public static function build (string $blade_file_path, array $kv_data = [])
    {
        // Returning the value
        return new View( self::$blade->build( $blade_file_path, $kv_data ) );
    }

    # Returns [View]
    public static function build_html (string $blade_file_path, array $kv_data = [], array $js_vars = [])
    {
        // Returning the value
        return new View( self::$blade->build_html( $blade_file_path, $kv_data, $js_vars ) );
    }



    # Returns [void]
    public function render ()
    {
        // Printing the value
        echo $this;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->value;
    }
}



?>