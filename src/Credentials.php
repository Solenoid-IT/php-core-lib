<?php



namespace Solenoid\Core;



use \Solenoid\Core\Storage;



class Credentials
{
    private static Storage $storage;



    # Returns [void]
    public static function config (string $basedir)
    {
        // (Creating a Storage)
        self::$storage = new Storage( $basedir, true );
    }



    # Returns [assoc|false]
    public static function fetch (string $file_path)
    {
        // (Getting the value)
        $file_content = self::$storage->read( $file_path );

        if ( $file_content === false )
        {// (Unable to read the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return json_decode( $file_content, true );
    }
}



?>