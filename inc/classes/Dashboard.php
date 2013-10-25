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
        private $default_config = array();
        
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
        public $scripts = array();
        
        /**
         * Output buffer.
         * @var string
         */
        public $buffer = '';

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
            $this->user_config = get_user_meta( $this->user_id, 'kebo_se_dashboard_config', true );
            
            /*
             * Load default Dashboard config
             */
            $this->default_config = array();
            
            /*
             * Render Widgets at specified position on dashboard page.
             * Accepts $column var.
             */
            add_action( 'kbso_dashboard_col', array( $this, 'render_widgets' ), 10, 1 );
            
        }
        
        /**
         * Autorun on Creation
         */
        public function test( $column ) {
            
            echo $column;
            
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
                'javascript' => $javascript,
                'scripts' => $scripts,
                'styles' => $styles,
            );
            
            $exists = 0;
            
            foreach ( $this->widgets as $widget ) {
                
                if ( $this->widget->slug == $widget->slug ) {
                    $exists = 1;
                }
                
            }
            
            array_push( $this->widgets, $this->widget );
            
            /*
             * 
             */
            if ( 0 == $exists ) {
                
                /*
                 * Add new Widget to all Widgets
                 */
                //array_push( $this->widgets, $this->widget );
                
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
        public function enqueue_scripts() {
            
            /*
             * Remove duplicates.
             */
            if ( ! empty( $this->scripts ) ) {

                $this->scripts = array_unique( $this->scripts, SORT_STRING );
                
                /*
                 * Enqueue all scripts required by Widgets.
                 */
                foreach ( $this->scripts as $script ) {

                    wp_enqueue_script( $script );
               
                }
                
            }
            
            /*
             * Return list of all scripts enqueued.
             */
            return $this->scripts;
            
        }
        
        /**
         * Renders all Widgets
         */
        public function render_widgets( $column ) {
            
            //echo $column;
            
            if ( isset( $column ) && ( is_int( absint( $column ) ) ) ) {
                
                print_r( $this->buffer[$column] );
                
            } else {
                
                return false;
                
            }
            
        }
        
        /**
         * Renders all Widgets
         */
        public function prepare_widgets() {
            
            /*
             * Find and 
             */
            if ( isset( $this->user_config[0]['col1'] ) ) {
                
                foreach ( $this->user_config[0]['col1'] as $item ) {
                    
                    foreach ( $this->widgets as $key => $widget ) {
                        
                        if ( $item[0] == $widget['slug'] ) {
                            
                            $widget['closed'] = $item[1];
                            
                            $this->set_widget( $widget );
                
                            $output = $this->output_widget();
                            
                            $this->buffer[1] .= $output;
                            
                            unset( $this->widgets[$key] );
                            
                        }
                        
                    }
                    
                }
                
            }
            
            /*
             * Find and 
             */
            if ( isset( $this->user_config[0]['col2'] ) ) {
                
                foreach ( $this->user_config[0]['col2'] as $item ) {
                    
                    foreach ( $this->widgets as $key => $widget ) {
                        
                        if ( $item[0] == $widget['slug'] ) {
                            
                            $widget['closed'] = $item[1];
                            
                            $this->set_widget( $widget );
                
                            $output = $this->output_widget();
                            
                            $this->buffer[2] .= $output;
                            
                            unset( $this->widgets[$key] );
                            
                        }
                        
                    }
                    
                }
                
            }
            
            if ( ! empty( $this->widgets ) && is_array( $this->widgets ) ) {
            
                foreach ( $this->widgets as $key => $widget ) {

                    $widget['closed'] = 0;

                    $this->set_widget( $widget );

                    $output = $this->output_widget();

                    if ( $key & 1 ) {

                        $this->buffer[1] = $output .= $this->buffer[1];

                    } else {

                        $this->buffer[2] = $output .= $this->buffer[2];

                    }

                }
            
            }
            
            // Not Needed for Now
            //$this->enqueue_scripts();
            
            return $this->buffer;
            
        }
        
        /**
         * Sets the widget
         * @param array | $widget | The widget data
         */
        public function set_widget( $widget ) {
            
            $this->widget = $widget;
            
            return $this;
        
        }
        
        /**
         * Returns HTML output for Widget in $this->widget.
         * @param array | $widget | Currently selected Widget
         */
        public function output_widget() {
            
            // Begin Output Buffering
            ob_start();
            
            ?>

            <div class="dashboard-box Sortable<?php if ( 1 == $this->widget['closed'] ) { echo ' closed'; } ?>" data-id="<?php echo $this->widget['ID']; ?>" data-slug="<?php echo $this->widget['slug']; ?>">
                            
                <div class="dash-header">
                    
                    <div class="toggle"><div class="arrow"></div></div>
                    <h3><?php echo $this->widget['title']; ?></h3>
                    
                </div>
                            
                <div class="dash-content">
                    
                    <?php echo $this->widget['content']; ?>
                    
                </div>
                            
            </div>

            <?php
            
            // End Output Buffering and Clear Buffer
            $output = ob_get_contents();
            @ob_end_clean();
            
            /*
             * Add Widget scripts to all scripts
             */
            array_push( $this->scripts, $this->widget['scripts'] );
            
            return $output;
            
        }
    
    }
    
}

/*
 * Create new instance with global scope
 */
global $dashboard;
$dashboard = new KeboSE_Dashboard();

/*
 * Example Widget
 */
$dashboard->add_widget(
    __( 'Example Widget', 'kebo-se' ), // Widget Title
    'kebo-example-widget', // Widget Slug - must be unique
    'Hello, this is an example Widget.', // Widget Content (html, js, etc)
    '', // Scripts to Print
    array( 'jquery', 'jquery-ui-sortable' ), // Script slugs to enqueue, must already have been registered 
    array() // Style slugs to enqueue, must already have been registered  
);

/*
 * Recent Visitors Widget
 */
$dashboard->add_widget(
    __( 'Recent Visitors', 'kebo-se' ),
    'kebo-recent-visitors',
    '<div id="placeholder" class="kebo-graph"></div>',
    '',
    array(),
    array()
);

/*
 * Visitor Device Usage Widget
 */
$dashboard->add_widget(
    __( 'Visitor Device Usage', 'kebo-se' ),
    'kebo-visitor-devices',
    '<div id="device-usage" class="kebo-graph"></div>',
    '',
    array( '' ),
    array()
);

/*
 * Visitor Browser Usage Widget
 */
$dashboard->add_widget(
    __( 'Visitor Browser Usage', 'kebo-se' ),
    'kebo-browser-usage',
    '<div id="browser-usage" class="kebo-graph"></div>',
    '',
    array( '' ),
    array()
);

/*
 * Share Count Widget
 */
$dashboard->add_widget(
    __( 'Shares', 'kebo-se' ),
    'kebo-shares',
    '<div id="shares-total" class="kebo-graph"></div>',
    '',
    array( '' ),
    array()
);


/*
 * Test Text Widget
 */
$dashboard->add_widget(
    __( 'Test Text Widget', 'kebo-se' ),
    'test-text-widget',
    "The ribs were hung with trophies; the vertebrae were carved with Arsacidean annals, in strange hieroglyphics; in the skull, the priests kept up an unextinguished aromatic flame, so that the mystic head again sent forth its vapoury spout; while, suspended from a bough, the terrific lower jaw vibrated over all the devotees, like the hair-hung sword that so affrighted Damocles. It was a wondrous sight. The wood was green as mosses of the Icy Glen; the trees stood high and haughty, feeling their living sap; the industrious earth beneath was as a weaver\'s loom, with a gorgeous carpet on it, whereof the ground-vine tendrils formed the warp and woof, and the living flowers the figures. All the trees, with all their laden branches; all the shrubs, and ferns, and grasses; the message-carrying air; all these unceasingly were active. Through the lacings of the leaves, the great sun seemed a flying shuttle weaving the unwearied verdure. Oh, busy weaver! unseen weaver!—pause!—one word!—whither flows the fabric? what palace may it deck? wherefore.",
    '',
    array( '' ),
    array()
);

/*
 * Test Text Widget
 */
$dashboard->add_widget(
    __( 'Test Text Widget', 'kebo-se' ),
    'test-text-widget2',
    "The ribs were hung with trophies; the vertebrae were carved with Arsacidean annals, in strange hieroglyphics; in the skull, the priests kept up an unextinguished aromatic flame, so that the mystic head again sent forth its vapoury spout; while, suspended from a bough, the terrific lower jaw vibrated over all the devotees, like the hair-hung sword that so affrighted Damocles. It was a wondrous sight. The wood was green as mosses of the Icy Glen; the trees stood high and haughty, feeling their living sap; the industrious earth beneath was as a weaver\'s loom, with a gorgeous carpet on it, whereof the ground-vine tendrils formed the warp and woof, and the living flowers the figures. All the trees, with all their laden branches; all the shrubs, and ferns, and grasses; the message-carrying air; all these unceasingly were active. Through the lacings of the leaves, the great sun seemed a flying shuttle weaving the unwearied verdure. Oh, busy weaver! unseen weaver!—pause!—one word!—whither flows the fabric? what palace may it deck? wherefore.",
    '',
    array( '' ),
    array()
);