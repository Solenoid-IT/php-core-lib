<?php



namespace Solenoid\Core;



use \eftec\bladeone\BladeOne;



class Blade
{
    private string   $basedir;
    private BladeOne $blade_one;



    # Returns [self]
    public function __construct
    (
        string $views_folder_path,
        string $cache_folder_path,

        bool   $debug_mode = false
    )
    {
        // (Getting the value)
        $this->basedir = $views_folder_path;



        foreach ([ $views_folder_path, $cache_folder_path ] as $folder_path)
        {// Processing each entry
            if ( !is_dir( $folder_path ) )
            {// (Directory not found)
                // (Making the directory)
                mkdir( $folder_path, 0777, true );
            }
        }



        // (Creating a BladeOne)
        $this->blade_one = new BladeOne
        (
            $views_folder_path,
            $cache_folder_path,

            $debug_mode ? BladeOne::MODE_DEBUG : 0
        )
        ;
    }

    # Returns [Blade]
    public static function create
    (
        string $views_folder_path,
        string $cache_folder_path,

        bool   $debug_mode = false
    )
    {
        // Returning the value
        return new Blade
        (
            $views_folder_path,
            $cache_folder_path,

            $debug_mode
        )
        ;
    }



    # Returns [string]
    public function build (string $file_path, array $kv_data = [])
    {
        // Returning the value
        return $this->blade_one->run( $file_path, $kv_data );
    }



    # Returns [string]
    public function get_basedir ()
    {
        // Returning the value
        return $this->basedir;
    }
}



?>