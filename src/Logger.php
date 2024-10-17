<?php



namespace Solenoid\Core;



use \Solenoid\System\File;



class Logger
{
    private static array $entries = [];

    public string $file_path;



    # Returns [self]
    public function __construct (string $file_path)
    {
        // (Getting the value)
        $this->file_path = $file_path;
    }



    # Returns [void]
    public static function add (string $id, Logger $logger)
    {
        // (Getting the value)
        self::$entries[ $id ] = $logger;
    }

    # Returns [Logger|false]
    public static function select (string $id)
    {
        // Returning the value
        return self::$entries[ $id ] ?? false;
    }



    # Returns [self|false]
    public function push (string $message)
    {
        if ( File::select( $this->file_path )->write( date('c') . ' :: ' . $message . "\n", 'append' ) === false )
        {// (Unable to write to the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }

    # Returns [self|false]
    public function reset ()
    {
        if ( File::select( $this->file_path )->write( '', 'replace' ) === false )
        {// (Unable to write to the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return File::select( $this->file_path )->read();
    }
}



?>