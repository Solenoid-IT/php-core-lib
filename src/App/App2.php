<?php



namespace Solenoid\Core\App;



abstract class App2
{
    public string $basedir;
    public string $id;
    public string $name;



    # Returns [self]
    public function __construct (array $config)
    {
        // (Getting the values)
        $this->basedir = realpath( $config['basedir'] );

        $this->id      = $config['id'];
        $this->name    = $config['name'];
    }



    # Returns [self]
    abstract public function run () : self;



    # Returns [string]
    public static function fetch_context ()
    {
        // Returning the value
        return isset( $_SERVER['REQUEST_METHOD'] ) ? 'http' : 'cli';
    }
}



?>