<?php
/*
 * Class to handle Flot Graphs - http://www.flotcharts.org
 */

if ( ! class_exists('KeboSE_Graphs')) :

    class KeboSE_Graphs {

        /**
         * Type of Graph e.g. Pie, Line, etc.
         *
         * @var string
         */
        private $type;

        /**
         * The SQL Query to get Graph Data
         *
         * @var string
         */
        private $sql;

        /**
         * Graph Options
         * @var array
         */
        private $options = array();
        
        /**
         * All Data for the Graph, if not using an SQL Query
         * @var array
         */
        private $data = array();

        public function __construct($directory = null, $view = null) {
            
            // do stuff
            
        }

        /**
         * Sets directory
         * @param string | $directory | The full root location
         */
        public function set_directory($directory) {
            
            $this->directory = $directory;
            return $this;
            
        }

        /**
         * Sets the type
         * @param string | $type | The graph type
         */
        public function set_type($type) {
            
            $this->type = $type;
            return $this;
            
        }
        
        /**
         * Gets the set graph type
         * @return string | The set graph type
         */
        public function get_type() {
            
            return $this->type;
            
        }
        
        /**
         * Sets the SQL query
         * @param string | $sql | The SQL query
         */
        public function set_sql($sql) {
            
            $this->sql = $sql;
            return $this;
            
        }

        /**
         * Gets the set SQL query
         * @return string | The set SQL query
         */
        public function get_sql() {
            
            return $this->sql;
            
        }
        
        /**
         * Sets the graph data
         * @param string | $data | The graph data
         */
        public function set_data($data) {
            
            $this->data = $data;
            return $this;
            
        }

        /**
         * Gets the set graph data
         * @return string | The set graph data
         */
        public function get_data() {
            
            return $this->data;
            
        }

        /**
         * Sets options for the graph
         * @param string | $variable | The name of the variable in the options
         * @param string | $value | The value of that variable
         */
        public function set($variable, $value) {
            
            $this->options[$variable] = $value;
            return $this;
            
        }

        /**
         * Loads the view to the browser
         *
         * @return void
         */
        public function render() {
            
            // Checks class requirements and is valid file
            if (!$file = $this->determine_file())
                return false;

            // Determines if data to extract
            if (!empty($this->data))
                extract($this->data);

            ob_start();

            include( $file );

            ob_get_contents();
            
        }

        /**
         * Returns the view
         *
         * @return string | The view file
         */
        public function retrieve() {
            
            // Check class requirements and is valid file
            if (!$file = $this->determine_file())
                return false;

            // Determines if data to extract
            if (!empty($this->data))
                extract($this->data);

            ob_start();

            include( $file );

            $buffer = ob_get_contents();
            @ob_end_clean();

            return $buffer;
            
        }

    }
    
endif;