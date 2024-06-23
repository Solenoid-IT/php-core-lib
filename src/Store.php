<?php



namespace Solenoid\Core;



use \Solenoid\Core\App\App;



class Store
{
    private static $instance;

    protected App $app;



    # Returns [self]
    private function __construct (App &$app)
    {
        // (Getting the value)
        $this->$app = &$app;
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
}



?>