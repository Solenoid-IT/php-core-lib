<?php



namespace Solenoid\Core;



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
        #'WEEK'   => 3600 * 7,
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
        $this->db                = $db ?? new JDB( $this->basedir . '/scheduler.json' );
        $this->config            = $config ?? new JDB( $this->basedir . '/tasks/scheduler.json' );
        $this->executor          = $executor;
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
                // (Initializing the JDB)
                $this->db->init();
            }
        )
        ;

        // (Running the daemon)
        $daemon->run
        (
            function () use ($daemon)
            {// (StartUp-Event)
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
                $day_ts = strtotime( date('Y-m-d') . ' 00:00:00' );

                // Printing the value
                #echo "\n\nDay TS -> $day_ts ( " . date( 'Y-m-d H:i:s', $day_ts ) . " )\n\n\n";



                // (Getting the value)
                $current_ts = time();



                // (Loading the JDB)
                $this->db = JDB::load( $this->db->file_path );



                // (Setting the directory)
                chdir( $this->basedir );



                // (Getting the value)
                $this->config = JDB::load( $this->config->file_path );



                if ( !$this->config['enabled'] ) return false;



                foreach ( $this->config['tasks'] as $task )
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



                    // (Getting the values)
                    $parts      = explode( ' ', $task['rule'] );

                    $num        = (int) $parts[1];
                    $unit       = $parts[2];

                    $start_ts   = $parts[4] ? strtotime( $parts[4] ) : $day_ts;

                    $period     = $num * self::TIME_UNITS[$unit];



                    if ( ( $current_ts - $start_ts ) % $period === 0 )
                    {// (Delta-Timestamp is a multiple of period)
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



                        // (Starting the process)
                        $process = Process::start( "$this->executor " . $task_id . ' ' . $task['fn'] . ' ' . implode( ' ', array_map( function ($arg) { return "\"$arg\""; }, $task['args'] ) ) );



                        // Printing the value
                        echo "\n\n" . date( 'c', $current_ts ) . " -> $task_class -> " . $task['rule'] . " -> $period s" . ( $process ? ' -> ' . $process->pid : '' ) . "\n\n";



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