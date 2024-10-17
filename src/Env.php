<?php



namespace Solenoid\Core;



use \Solenoid\Core\App\App;



class Env
{
    const TYPE_DEV    = 'dev';
    const TYPE_PROD   = 'prod';
    const TYPE_CUSTOM = 'custom';



    private static array $entries = [];



    public string $type;
    public array  $hosts;
    public array  $data;



    # Returns [self]
    public function __construct (string $type = self::TYPE_CUSTOM, array $hosts = [ 'localhost' ], array $data = [])
    {
        // (Getting the values)
        $this->type  = $type;
        $this->hosts = $hosts;
        $this->data  = $data;
    }



    # Returns [void]
    public static function add (string $id, Env $env)
    {
        // (Getting the value)
        self::$entries[ $id ] = $env;
    }

    # Returns [Env|false]
    public static function detect ()
    {
        // (Setting the value)
        $host = null;

        switch ( App::fetch_context() )
        {
            case 'cli':
                // (Getting the value)
                $host = gethostname();
            break;

            case 'http':
                // (Getting the value)
                $host = $_SERVER['SERVER_NAME'];
            break;
        }

    

        foreach ( self::$entries as $entry )
        {// Processing each entry
            if ( in_array( $host, $entry->hosts ) )
            {// Match OK
                // Returning the value
                return $entry;
            }
        }



        // Returning the value
        return false;
    }
}



?>