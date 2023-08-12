<?php
/**
 *  Implemented by Zubidev (https://github.com/zubidev/).
 *
 * @package WP_Media_Crawler
 */

namespace WP_Media\Crawler\Custom\Tasks;

/**
 * Class AbstractTask. Configures a generic class flow.
 */
abstract class AbstractTask {

    /**
     * The task event_name.
     *
     * @var string $event_name
     */
    protected $event_name;

    /**
     * The task event recurrence.
     *
     * @var string $recurrence
     */
    protected $recurrence;

    /**
     * The task event arguments.
     *
     * @var array $args
     */
    protected $args;

    /**
     * Constructor method.
     *
     * @param string $event_name The task event_name.
     * @param string $recurrence The task event recurrence.
     * @param array  $args       The task event arguments.
     */
    public function __construct( $event_name, $recurrence, $args = [] ) {
        $this->event_name = $event_name;
        $this->recurrence = $recurrence;
        $this->args       = $args;

        add_action( $this->event_name, [ $this, 'run' ] );

        register_activation_hook( WP_MEDIA_CRAWLER_FILE, [ $this, 'schedule' ] );
        register_deactivation_hook( WP_MEDIA_CRAWLER_FILE, [ $this, 'unschedule' ] );
    }

    /**
     * Run method. The task implementation.
     */
    abstract public function run() : void;

    /**
     * Schedule method. It should run only during the plugin activation.
     */
    public function schedule() : void {
        if ( ! wp_next_scheduled( $this->event_name, $this->args ) ) {
            wp_schedule_event( time(), $this->recurrence, $this->event_name, $this->args );
        }
    }

    /**
     * Unschedule method. It should run only during the plugin deactivation.
     */
    public function unschedule() : void {
        wp_clear_scheduled_hook( $this->event_name, $this->args );
    }
}
