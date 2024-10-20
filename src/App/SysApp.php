<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\App\App;
use \Solenoid\Core\Routing\Target;



class SysApp extends App
{
    const NS_PREFIX = 'App\\Tasks\\';



    public static Target $requested_target;
    public static string $task;



    # Returns [self]
    public function __construct (array $config)
    {
        // (Calling the function)
        parent::__construct( $config );



        // (Accessing the value)
        global $argv;



        // (Getting the value)
        $args = array_slice( $argv, 1 );

        if ( count($args) < 2 )
        {// (There are no args)
            // Printing the value
            echo "\n\nphp $argv[0] <task> <method> ...<args>\n\n";



            // Returning the value
            return $this;
        }



        // (Getting the values)
        $class  = self::NS_PREFIX . str_replace( '/', '\\', $args[0] );
        $method = $args[1];



        // (Getting the value)
        $target_args = array_slice( $args, 2 );



        // (Getting the value)
        $target = Target::link( $class, $method, $target_args );



        // (Getting the value)
        $target->args = &$target_args;

        // (Getting the value)
        $target->tags = &$class::$tags;



        // (Getting the value)
        self::$requested_target = &$target;



        // (Getting the value)
        self::$task = self::$requested_target->class . '::' . self::$requested_target->fn . '()';
    }



    # Returns [self|false]
    public function run ()
    {
        if ( !App::$env )
        {// Value not found
            // Printing the value
            echo 'ENV NOT FOUND';



            // Returning the value
            return false;
        }



        // (Running the target)
        self::$requested_target->run_app( $this );



        // Returning the value
        return $this;
    }
}



?>