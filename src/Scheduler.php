<?php



namespace Solenoid\Core;



use \Solenoid\Core\Task\SystemTask;

use \Solenoid\System\JDB;
use \Solenoid\System\Daemon;
use \Solenoid\System\Process;



class Scheduler
{
    const TIME_UNITS =
    [
        'SECOND' => 1,
        'MINUTE' => 60,
        'HOUR'   => 3600,
        'DAY'    => 86400
    ]
    ;



    public string $basedir;
    public string $tasks_folder_path;
    public string $task_ns_prefix;
    public JDB    $db;
    public JDB    $config;
    public string $executor;



    # Returns [self]
    public function __construct (string $basedir, ?string $tasks_folder_path = null, string $task_ns_prefix = 'App\\Task\\', ?JDB $db = null, ?JDB $config = null, string $executor = 'php bootstrap.php')
    {
        // (Getting the values)
        $this->basedir           = $basedir;
        $this->tasks_folder_path = $tasks_folder_path ?? $this->basedir . '/tasks/src';
        $this->task_ns_prefix    = $task_ns_prefix;
        $this->db                = $db ?? new JDB( $this->basedir . '/jdb/scheduler-db.json' );
        $this->config            = $config ?? new JDB( $this->basedir . '/tasks/scheduler.json' );
        $this->executor          = $executor;
    }



    # Returns [array<string>|null]
    public static function verify_rules (array $rules, int $current_ts)
    {
        // (Getting the value)
        $day_ts = strtotime( date('Y-m-d') . ' 00:00:00' );



        // (Setting the value)
        $matches = [];

        foreach ( $rules as $rule )
        {// Processing each entry
            // (Getting the values)
            $parts = explode( ' ', $rule );

            switch ( $parts[0] )
            {
                case 'EVERY':
                    // (Getting the values)
                    $factor = (int) $parts[1];
                    $unit   = $parts[2];



                    // (Getting the value)
                    $start_ts   = $parts[4] ? strtotime( $parts[4] ) : $day_ts;



                    switch ( $unit )
                    {
                        case 'SECOND':
                        case 'MINUTE':
                        case 'HOUR':
                        case 'DAY':
                            // (Getting the value)
                            $duration = $factor * self::TIME_UNITS[$unit];
                        break;

                        case 'WEEK':
                            // (Getting the value)
                            $duration = strtotime( "+$factor week", $start_ts ) - $start_ts;
                        break;

                        case 'MONTH':
                            // (Getting the value)
                            $duration = strtotime( "+$factor month", $start_ts ) - $start_ts;
                        break;

                        case 'YEAR':
                            // (Getting the value)
                            $duration = strtotime( "+$factor year", $start_ts ) - $start_ts;
                        break;

                        default:
                            // Returning the value
                            return null;
                    }



                    if ( ( $current_ts - $start_ts ) % $duration === 0 )
                    {// (Delta-Timestamp is a multiple of duration)
                        // (Appending the value)
                        $matches[] = $rule;
                    }
                break;

                case 'AT':
                    if ( date( 'H:i:s', $current_ts ) === $parts[1] )
                    {// (HMS is the same)
                        // (Appending the value)
                        $matches[] = $rule;
                    }
                break;

                default:
                    // Returning the value
                    return null;
            }
        }



        // Returning the value
        return $matches;
    }



    # Returns [self]
    public function run ()
    {
        // (Creating a Daemon)
        $daemon = new Daemon();



        // (Handling the signal)
        $daemon->handle_signal
        (
            function ($signal)
            {
                // (Removing the JDB)
                $this->db->remove();
            }
        )
        ;

        // (Running the daemon)
        $daemon->run
        (
            function () use ($daemon)
            {// (StartUp-Event)
                if ( !$this->db->exists() )
                {// (JDB not found)
                    // (Initializing the JDB)
                    $this->db->init();
                }



                // (Loading the JDB)
                $this->db = JDB::load( $this->db->file_path );

                if ( $this->db->data['start'] )
                {// (Scheduler has been already started)
                    // Printing the value
                    echo "\n\nScheduler -> " . $this->db->data['pid'] ." (already running) \n\n\n";

                    // Closing the process
                    exit;
                }



                // (Getting the values)
                $this->db->data['start'] = date( 'c', $daemon->startup_ts );
                $this->db->data['pid']   = getmypid();



                // (Saving the JDB)
                $this->db->save();



                // Printing the value
                echo "\n\nScheduler -> " . getmypid() . "\n\n\n";
            },

            function ()
            {// (Tick-Event)
                // (Getting the value)
                $current_ts = time();



                // (Loading the JDB)
                $this->db = JDB::load( $this->db->file_path );



                // (Setting the directory)
                chdir( $this->basedir );



                // (Getting the value)
                $this->config = JDB::load( $this->config->file_path );



                if ( !$this->config->data['enabled'] ) return false;



                foreach ( $this->config->data['tasks'] as $task )
                {// Processing each entry
                    // (Getting the value)
                    $task_id = $task['id'];

                    if ( !file_exists( "$this->tasks_folder_path/$task_id.php" ) )
                    {// (Task does not exist)
                        // (Removing the element)
                        unset( $this->db->data['tasks'][$task_id] );

                        // (Saving the JDB)
                        $this->db->save();

                    

                        // Continuing the iteration
                        continue;
                    }



                    if ( $matched_rules = self::verify_rules( $task['rules'], $current_ts ) )
                    {// (There is at least one of the time-rules matched)
                        // (Getting the value)
                        $task_class = $this->task_ns_prefix . str_replace( '/', '\\', $task_id );

                        if ( $this->db->data['tasks'][$task_id]['pid'] )
                        {// (Task has been already started)
                            if ( Process::fetch_pid_info( $this->db->data['tasks'][$task_id]['pid'] ) === false )
                            {// (Task is not running)
                                // (Setting the values)
                                $this->db->data['tasks'][$task_id]['end'] = date('c');
                                $this->db->data['tasks'][$task_id]['pid'] = null;



                                // (Saving the JDB)
                                $this->db->save();
                            }
                            else
                            {// (Task is running)
                                // Continuing the iteration
                                continue;
                            }
                        }



                        if ( !$task['enabled'] ) continue;



                        // (Starting the task)
                        $process = ( new SystemTask( $task_id, $task['fn'], array_map( function ($arg) { return "\"$arg\""; }, $task['args'] ), $this->executor ) )->start();



                        // Printing the value
                        echo "\n\n" . date( 'c', $current_ts ) . " -> $task_class -> " . '[ ' . implode( ' ], [ ', $matched_rules ) . ' ]' . ( $process ? ' -> ' . $process->pid : '' ) . "\n\n";



                        // (Getting the values)
                        $this->db->data['tasks'][$task_id]['start'] = date('c');
                        $this->db->data['tasks'][$task_id]['end']   = null;

                        $this->db->data['tasks'][$task_id]['pid']   = $process->pid;



                        // (Saving the JDB)
                        $this->db->save();
                    }
                }
            }
        )
        ;



        // Returning the value
        return $this;
    }

    # Returns [self]
    public function kill ()
    {
        // (Getting the value)
        $pid = $this->db->read()['pid'];

        if ( $pid )
        {// Value found
            // (Executing the command)
            system("kill $pid");
        }



        // Returning the value
        return $this;
    }
}



?>