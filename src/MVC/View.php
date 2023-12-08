<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\Core;
use \Solenoid\HTML\Builder as HTMLBuilder;



class View
{
    public static Core $core;



    # Returns [self]
    public function __construct (Core &$core)
    {
        // (Getting the value)
        self::$core = $core;
    }

    # Returns [View]
    public static function create (Core &$core)
    {
        // Returning the value
        return new View( $core );
    }



    # Returns [string] | Throws [Exception]
    public static function build (string $blade_file_path, array $kv_data = [], array $js_vars = [])
    {
        // (Building the content with blade)
        $blade_file_content = self::$core::$blade->build( $blade_file_path, $kv_data );



        // (Getting the value)
        $html_builder = HTMLBuilder::create( $blade_file_content );

        foreach ($js_vars as $k => $v)
        {// Processing each entry
            // (Appending the var)
            $html_builder->append_var( $k, $v );
        }



        // (Getting the value)
        $blade_file_content = $html_builder->fetch_content();



        // Returning the value
        return $blade_file_content;
    }

    # Returns [string|false] | Throws [Exception]
    public static function build_raw (string $html_file_path, array $kv_data = [], array $js_vars = [])
    {
        // (Getting the value)
        $html_file_path = self::$core::$basedir . '/views/' . $html_file_path;



        // (Reading the file content)
        $html_file_content = file_get_contents( $html_file_path );

        if ( $html_file_content === false )
        {// (Unable to read the file content)
            // (Setting the value)
            $message = "Unable to read the file content";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        foreach ($kv_data as $k => $v)
        {// Processing each entry
            // (Getting the value)
            $html_file_content = str_replace( $k, $v, $html_file_content );
        }



        // (Getting the value)
        $html_builder = HTMLBuilder::create( $html_file_content );

        foreach ($js_vars as $k => $v)
        {// Processing each entry
            // (Appending the var)
            $html_builder->append_var( $k, $v );
        }



        // (Getting the value)
        $html_file_content = $html_builder->fetch_content();



        // Returning the value
        return $html_file_content;
    }



    # Returns [\Solenoid\HTML\Builder]
    public static function open (string $html_content)
    {
        // Returning the value
        return HTMLBuilder::create( $html_content );
    }

    # Returns [\Solenoid\HTML\Builder|false] | Throws [Exception]
    public static function open_file (string $html_file_path)
    {
        // (Getting the value)
        $html_file_content = file_get_contents( $html_file_path );

        if ( $html_file_content === false )
        {// (Unable to read the file content)
            // (Setting the value)
            $message = "Unable to read the file content";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // (Opening the html builder)
        return self::open( $html_file_content );
    }



    # Returns [string]
    public static function resolve_svelte_path (string $view_path)
    {
        // (Getting the values)
        $blade_folder_path = self::$core::$blade->get_basedir();
        $svelte_build_path = realpath( self::$core::$basedir . '/../web/build' );

        $diff              = array_diff( explode( '/', $svelte_build_path ), explode( '/', $blade_folder_path ) );



        // (Setting the value)
        $rel_path = [];

        foreach ($diff as $entry)
        {// Processing each entry
            // (Appending the value)
            $rel_path[] = '..';
        }

        foreach ($diff as $entry)
        {// Processing each entry
            // (Appending the value)
            $rel_path[] = $entry;
        }

        // (Getting the value)
        $rel_path = implode( '/', $rel_path );



        // Returning the value
        return "$rel_path/$view_path";
    }
}



?>