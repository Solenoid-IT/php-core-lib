<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\App\App;
use \Solenoid\Core\Routing\Target;



class SysApp extends App
{
    const NS_PREFIX = 'App\\Tasks\\';



    public Target $requested_target;
    public string $task;



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
            echo "\nphp $argv[0] <task> <method> ...<args>\n\n";



            // Returning the value
            return $this;
        }



        // (Getting the values)
        $class  = self::NS_PREFIX . str_replace( '/', '\\', $args[0] );
        $method = $args[1];



        // (Getting the value)
        #$target_args = array_slice( $args, 2 );



        // (Getting the value)
        $target = Target::link( $class, $method );

        // (Setting the args)
        $target->set_args( array_slice( $args, 2 ) );



        // (Getting the value)
        #$target->args = &$target_args;

        // (Getting the value)
        #$target->tags = &$class::$tags;



        // (Getting the value)
        $this->requested_target = &$target;



        // (Getting the value)
        $this->task = (string) $target;
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



        if ( !isset( $this->requested_target ) )
        {// Value not found
            // Returning the value
            return false;
        }
        


        // (Getting the value)
        $target = $this->requested_target;



        // (Getting the value)
        App::$route_tags = $target->list_tags();



        try
        {
            // (Triggering the event)
            App::trigger_event( 'start' );



            // (Getting the value)
            $gate_lock = call_user_func_array( [ \App\Gate::class, 'run' ], [] ) === false;

            if ( !$gate_lock )
            {// (There is no a gate lock)
                // (Getting the value)
                $response = $target->run();

                if ( $response !== $target )
                {// (There is no a middleware lock)
                    if ( $response !== null )
                    {// (Function returns something)
                        // Printing the value
                        echo json_encode( $response );
                    }
                }
            }



            // (Triggering the event)
            App::trigger_event( 'end' );
        }
        catch (\Exception $e)
        {
            // (Getting the value)
            $message = (string) $e;



            // (Triggering the event)
            App::trigger_event( 'error', [ 'message' => $message ] );



            if ( App::$env->type === 'dev' )
            {// Match OK
                // Throwing an exception
                throw $e;
            }



            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }
}



?>