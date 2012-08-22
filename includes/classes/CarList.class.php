<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CarList - Class File
 * 
 * CarList - Database and script to show Year, Make, Model and Style on your website.
 * Can be used as a search tool, adding a vehicle to a site and more.
 *
 * @package		CarList
 * @author		Ideal Web Solutions
 * @copyright	Copyright (c) 2011, Ideal Web Solutions, LLC.
 * @version		Version 1.1
 */

class CarList {
    
    /**
	 * database name
	 *
	 * @var string
	 */
     protected $dbname;
     
     /**
	 * database host
	 *
	 * @var string
	 */
     protected $dbhost;
     
     /**
	 * database user
	 *
	 * @var string
	 */
     protected $dbuser;
     
     /**
	 * database password
	 *
	 * @var string
	 */
     protected $dbpass;
     
     /**
	 * database table
	 *
	 * @var string
	 */
     protected $dbtable;
     
     /**
	 * vehicle types table
	 *
	 * @var string
	 */
     protected $dbtblvt;
     
     /**
	 * database connection
	 *
	 * @var string
	 */
     protected $dbconnect;
     
     /**
	 * query results
	 *
	 * @var string
	 */
     protected $sqlresults;

	/**
	 * CarList::__construct()
	 * 
     * Setup contructor to connect to the DB
     * 
	 * @param mixed $dbhost
	 * @param mixed $dbname
	 * @param mixed $dbuser
	 * @param mixed $dbpass
	 * @param mixed $dbtable
	 * @param mixed $dbtblvt
	 */
	public function __construct($dbhost, $dbname, $dbuser, $dbpass, $dbtable, $dbtblvt) {
		$this->dbname  = $dbname;
		$this->dbhost  = $dbhost;
		$this->dbuser  = $dbuser;
		$this->dbpass  = $dbpass;
        $this->dbtable = $dbtable;
        $this->dbtblvt = $dbtblvt;
        // Lets try and make a database connection
		$this->MySQLConnect();
	}

	/**
	 * CarList::MySQLConnect()
	 * 
     * Connect to DB
     * 
	 * @return integer
	 */
	private function MySQLConnect() {
		// We need to try and connect to the database from here.
		$conn = mysql_connect($this->dbhost,$this->dbuser,$this->dbpass);
		if($conn) {
			$this->dbconnect = $conn;
			// Lets select the database we are going to be using
			if(mysql_select_db($this->dbname)) {
				$this->dbselected = 1;
			}else{
				echo "Unable to select database. Please try again";
			}				
		}	
	}

    /**
     * CarList::getyears()
     * 
     * Get the years from the DB
     * 
     * @param string $order
     * @return array
     */
    public function getyears($order = 'DESC') {
        // Setup the SQL statement
        $sql = "SELECT DISTINCT year FROM ".$this->dbtable." ORDER BY year ".$order;
        $this->sqlresults = mysql_query($sql, $this->dbconnect);
        if($this->sqlresults){
            return $this->sqlresults;
        }
    }

    /**
     * CarList::getmakes()
     * 
     * Get the makes from the DB based on the selected year
     * 
     * @param string $year
     * @param string $order
     * @return array
     */
    public function getmakes($year = '') {
    	// Setup the SQL statement
        $sql = "SELECT DISTINCT make FROM ".$this->dbtable." WHERE year='".$year."'";
        $this->sqlresults = mysql_query($sql, $this->dbconnect);
        if($this->sqlresults){
            return $this->sqlresults;
        }
    }

    /**
     * CarList::getmodels()
     * 
     * Get the models from the DB based on the selected year and make
     * 
     * @param string $year
     * @param string $make
     * @param string $order
     * @return array
     */
    public function getmodels($year = '', $make = '') {
        // Setup the SQL statement
        $sql = "SELECT DISTINCT model FROM ".$this->dbtable." WHERE year='".$year."' AND make='".$make."'";
        $this->sqlresults = mysql_query($sql, $this->dbconnect);
        if($this->sqlresults){
            return $this->sqlresults;
        }
    }

    /**
     * CarList::getstyles()
     * 
     * Get the trims from the DB based on the selected year, make and model selected
     * 
     * @param string $year
     * @param string $make
     * @param mixed $model
     * @param string $order
     * @return array
     */
    public function getstyles($year = '', $make = '', $model = '') {
        // Setup the SQL statement
        $sql = "SELECT style FROM ".$this->dbtable." WHERE year='".$year."' AND make='".$make."' AND model='".$model."'";
        $this->sqlresults = mysql_query($sql, $this->dbconnect);
        if($this->sqlresults){
            return $this->sqlresults;
        }
    }
    
    /**
     * CarList::getvehicletypes()
     * 
     * Get the vehicle types from the DB
     * 
     * @param string $order
     * @return array
     */
    public function getvehicletypes($order = 'ASC') {
        // Setup the SQL statement
        $sql = "SELECT id,name FROM ".$this->dbtblvt." ORDER BY name ".$order;
        $this->sqlresults = mysql_query($sql, $this->dbconnect);
        if($this->sqlresults){
            return $this->sqlresults;
        }
    }
}

/* End of file CarList.class.php */
/* Location: ./carlistdb/include/CarList.class.php */