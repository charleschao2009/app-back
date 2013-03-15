<?php

class Logger
{
	var $queries = array();
	var $blocks = array();
	var $extra = array();
	var $files = array();
	var $logstart = array();
	var $logend = array();

	public function __construct()
	{

	}

	/**
	 * get a reference to the only instance of this class
	 *
     * @return	object XoopsLogger  reference to the only instance
	 */
	public static function &instance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new Logger();
		}
		return $instance;
	}

	/**
	 * start a timer
	 *
     * @param	string  $name   name of the timer
     *
	 */
	function startTime($name = 'MySite')
	{
		$this->logstart[$name] = explode(' ', microtime());
	}

	/**
	 * stop a timer
	 *
     * @param	string  $name   name of the timer
	 */
	function stopTime($name = 'MySite')
	{
		$this->logend[$name] = explode(' ', microtime());
	}

	/**
	 * log a database query
	 *
     * @param	string  $sql    SQL string
     * @param	string  $error  error message (if any)
     * @param	int     $errno  error number (if any)
	 */
	function addQuery($sql, $error=null, $errno=null)
	{
		$this->queries[] = array('sql' => $sql, 'error' => $error, 'errno' => $errno);
	}

	/**
	 * log display of a block
	 *
     * @param	string  $name       name of the block
     * @param	bool    $cached     was the block cached?
     * @param	int     $cachetime  cachetime of the block
	 */
	function addBlock($name, $cached = false, $cachetime = 0)
	{
		$this->blocks[] = array('name' => $name, 'cached' => $cached, 'cachetime' => $cachetime);
	}

	/**
	 * log extra information
	 *
     * @param	string  $name       name for the entry
     * @param	int     $cachetime  cachetime for the entry
	 */
	function addExtra($name, $cachetime = 0)
	{
		$this->extra[] = array('name' => $name, 'cachetime' => $cachetime);
	}

	/**
	 * log extra information
	 *
     * @param	string  $name       name for the entry
     * @param	int     $time  		time for the entry
	 */
	function addFile($file)
	{
		$this->files[] = $file;
	}

	/**
	 * get the logged queries in a HTML table
	 *
     * @return	string  HTML table with queries
	 */
	function dumpQueries()
	{
		global $db_config;
		$ret = '<table class="outer" width="100%" cellspacing="1"><tr><th>Queries</th></tr>';
		$class = 'even';
		foreach ($this->queries as $q) {
			if (isset($q['error'])) {
				$ret .= '<tr class="'.$class.'"><td><span style="color:#ff0000;">'.$q['sql'].'<br /><b>Error number:</b> '.$q['errno'].'<br /><b>Error message:</b> '.$q['error'].'</span></td></tr>';
			} else {
				$ret .= '<tr class="'.$class.'"><td>'.$q['sql'].'</td></tr>';
			}
			$class = ($class == 'odd') ? 'even' : 'odd';
		}
		$ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->queries).'</span> queries</td></tr></table><br />';
		return $ret;
	}

	/**
	 * get the logged blocks in a HTML table
	 *
     * @return	string  HTML table with blocks
	 */
	function dumpBlocks()
	{
		$ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Blocks</th></tr>';
		$class = 'even';
		foreach ($this->blocks as $b) {
			if ($b['cached']) {
				$ret .= '<tr><td class="'.$class.'"><b>'.$b['name'].':</b> Cached (regenerates every '.$b['cachetime'].' seconds)</td></tr>';
			} else {
				$ret .= '<tr><td class="'.$class.'"><b>'.$b['name'].':</b> No Cache</td></tr>';
			}
			$class = ($class == 'odd') ? 'even' : 'odd';
		}
		$ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->blocks).'</span> blocks</td></tr></table><br />';
		return $ret;
	}

	/**
	 * get the logged flie in a HTML table
	 *
     * @return	string  HTML table with flie
	 */
	function dumpFlies()
	{
		print_r($this->files);
		$ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Files</th></tr>';
		$class = 'even';
		foreach ($this->files as $b) {
			if ($b['type']) {
				$ret .= '<tr><td class="'.$class.'"><b>'.$b['name'].':</b> ' . $b['type'] . ' (regenerates every '.$b['time'].' seconds)</td></tr>';
			} else {
				$ret .= '<tr><td class="'.$class.'"><b>'.$b['name'].':</b> No type </td></tr>';
			}
			$class = ($class == 'odd') ? 'even' : 'odd';
		}
		$ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->files).'</span> blocks</td></tr></table><br />';
		return $ret;
	}

	/**
	 * get the current execution time of a timer
	 *
     * @param	string  $name   name of the counter
     * @return	float   current execution time of the counter
	 */
	function dumpTime($name = 'MySite')
	{
		if (!isset($this->logstart[$name])) {
			return 0;
		}
		if (!isset($this->logend[$name])) {
			$stop_time = explode(' ', microtime());
		} else {
			$stop_time = $this->logend[$name];
		}
		return ((float)$stop_time[1] + (float)$stop_time[0]) - ((float)$this->logstart[$name][1] + (float)$this->logstart[$name][0]);
	}

	/**
	 * get extra information in a HTML table
	 *
     * @return	string  HTML table with extra information
	 */
	function dumpExtra()
	{
		$ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Extra</th></tr>';
		$class = 'even';
		foreach ($this->extra as $ex) {
			$ret .= '<tr><td class="'.$class.'"><b>'.$ex['name'].':</b> Cached (regenerates every '.$ex['cachetime'].' seconds)</td></tr>';
			$class = ($class == 'odd') ? 'even' : 'odd';
		}
		$ret .= '</table><br />';
		return $ret;
	}

	/**
	 * get all logged information formatted in HTML tables
	 *
     * @return	string  HTML output
	 */
	function dumpAll()
	{
		$ret = $this->dumpQueries();
		$ret .= $this->dumpBlocks();
		if (count($this->logstart) > 0) {
			$ret .= '<table class="outer" width="100%" cellspacing="1"><tr><th>Execution Time</th></tr>';
			$class = 'even';
			foreach ($this->logstart as $k => $v) {
				$ret .= '<tr><td class="'.$class.'"><b>'.$k.'</b> took <span style="color:#ff0000;">'.$this->dumpTime($k).'</span> seconds to load.</td></tr>';
				$class = ($class == 'odd') ? 'even' : 'odd';
			}
			$ret .= '</table><br />';
		}
		$ret .= $this->dumpExtra();
		return $ret;
	}
}
