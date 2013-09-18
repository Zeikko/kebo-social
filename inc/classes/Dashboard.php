<?php
/*
 * Class to handle Dashboard Widgets.
 */

if ( ! class_exists( 'KeboSE_Dashboard' ) ) {

    class KeboSE_Dashboard {

        /**
         * Current User ID
         * @var string
         */
        private $user_id = array();
        
        /**
         * User dashboard configuration
         * @var array
         */
        private $user_config = array();
        
        /**
         * Graph Options
         * @var array
         */
        private $options = array();
        
        /**
         * Default Widget Config
         * @var array
         */
        private $default_order = array();
        
        /**
         * User Widget Config
         * @var array
         */
        private $user_order = array();
        
        /**
         * Current Widget.
         * @var array
         */
        private $widget = array();
        
        /**
         * All Widgets.
         * @var array
         */
        private $widgets = array();
        
         /**
         * All Scripts.
         * @var array
         */
        private $scripts = array();

        /**
         * Autorun on Creation
         */
        public function __construct() {
            
            /*
             * Get current user ID
             */
            $this->user_id = get_current_user_id();
            
            /*
             * Load user preferences from User Meta
             */
            $this->user_config = get_user_meta( $this->user_id, 'kebo_se_dashboard_config' );
            
        }
        
        /**
         * Register a new Widget
         * @param string | $variable | The name of the variable in the options
         * @param string | $value | The value of that variable
         */
        public function add_widget( $title, $slug, $content, $javascript = null, $scripts = null, $styles = null ) {
            
            $id = rand( 0, 9999 );
            
            // do sanitisation ?????
            
            /*
             * Prepare new Widget Data
             */
            $this->widget = array(
                'ID' => $id,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'scripts' => $scripts,
                'styles' => $styles,
            );
            
            $exists = 0;
            
            foreach ( $this->widgets as $widget ) {
                
                if ( $this->widget->slug == $widget->slug ) {
                    $exists = 1;
                }
                
            }
            
            if ( 0 == $exists ) {
                
                /*
                 * Add new Widget to all Widgets
                 */
                array_push( $this->widgets, $this->widget );
                
                // Widget added successfully.
                return true;
                
                
            } else {
                
                // Widget already exists.
                return false;
                
            }
            
        }
        
        /**
         * Enqueues Scripts
         * @param array | $scripts | Array containing the slugs of every script to be enqueued.
         */
        public function enqueue_scripts( $scripts ) {
            
            /*
             * Remove duplicates.
             */
            $scripts = array_unique( $scripts, SORT_STRING );
            
            /*
             * Enqueue all scripts required by Widgets.
             */
            foreach ( $scripts as $script ) {
                
                wp_enqueue_script( $script );
                
            }
            
            /*
             * Return list of all scripts enqueued.
             */
            return $scripts;
            
        }
        
        /**
         * Renders all Widgets
         * @param string | $variable | The name of the variable in the options
         * @param string | $value | The value of that variable
         */
        public function render_widgets( $column ) {
            
            
            
            return $this;
            
        }
        
        /**
         * Returns HTML output for Widget in $this->widget.
         * @param array | $this->widget | Currently selected Widget
         */
        public function output_widget() {
            
            // Begin Output Buffering
            ob_start();
            
            /*
             * Add Widget scripts to all scripts
             */
            array_push( $this->scripts, $this->widget->scripts );
            
            ?>

            <div class="dashboard-box" data-id="<?php echo $this->widget->ID; ?>">
                            
                <div class="dash-header">
                    
                    <div class="toggle"><div class="arrow"></div></div>
                    <h3><?php echo $this->widget->title; ?></h3>
                    
                </div>
                            
                <div class="dash-content">
                    
                    <?php echo $this->widget->content; ?>
                    
                </div>
                            
            </div>

            <?php
            
            // End Output Buffering and Clear Buffer
            $output = ob_get_contents();
            ob_end_clean();
            
            return $output;
            
        }
    
    }
    
}

