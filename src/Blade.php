<?php



namespace Solenoid\Core;



use \eftec\bladeone\BladeOne;
use \Solenoid\HTML\Builder as HTMLBuilder;



class Blade
{
    private BladeOne $blade_one;

    public string $basedir;



    # Returns [self]
    public function __construct (string $views_folder_path, string $cache_folder_path, bool $debug_mode = false)
    {
        // (Getting the value)
        $this->basedir = $views_folder_path;



        foreach ( [ $views_folder_path, $cache_folder_path ] as $folder_path )
        {// Processing each entry
            if ( !is_dir( $folder_path ) )
            {// (Directory not found)
                // (Making the directory)
                mkdir( $folder_path, 0777, true );
            }
        }



        // (Creating a BladeOne)
        $this->blade_one = new BladeOne( $views_folder_path, $cache_folder_path, $debug_mode ? BladeOne::MODE_DEBUG : 0 );
    }



    # Returns [string] | Throws [Exception]
    public function build (string $file_path, array $kv_data = [])
    {
        // Returning the value
        return $this->blade_one->run( $file_path, $kv_data );
    }

    # Returns [string] | Throws [Exception]
    public function build_html (string $blade_file_path, array $kv_data = [], array $js_vars = [])
    {
        // (Building the content)
        $blade_file_content = $this->build( $blade_file_path, $kv_data );



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
}



?>