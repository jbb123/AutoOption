<?php 

class Database
{
	public function __construct()
	{
		$this->sqlHost = "localhost";
		$this->sqlUser = "root";
		$this->sqlPass = "48826514";
		$this->database = "auto";
		$this->port = "3306";
		$this->mysqli = mysqli_connect($this->sqlHost, $this->sqlUser, $this->sqlPass, $this->database, $this->port);
		
		mysqli_select_db($this->mysqli, $this->database);
		
	}
	
	public function foundRows()
	{
		$row = $this->queryFirst("SELECT FOUND_ROWS()");
		
				

		return (int)$row['FOUND_ROWS()'];
	}
	
	public function parseSafeQuery($args)
	{
		$sql	= array_shift($args);
		$i		= 0;
		$pos	= strpos($sql, '%');

		while ($pos !== false)
		{
			$skip = false;
			$type = substr($sql, $pos, 2);

			switch ($type)
			{
				case '%%':
					$r_value = '%';
					$skip = true;
					break;

				case '%d':
					$r_value = (int)$args[$i];
					break;

				case '%f':
					$r_value = '\'' . (float)$args[$i] . '\'';
					break;

				case '%s':
					$r_value = '\'' . mysqli_real_escape_string($this->mysqli, $args[$i]) . '\'';
					break;

				case '%S':
					$r_value = strlen($args[$i]) ? '\'' . mysqli_real_escape_string($args[$i]) . '\'' : 'NULL';
					break;
			}

			$sql = substr($sql, 0, $pos) . $r_value . substr($sql, $pos + 2);
			$offset = (int)($pos + strlen($r_value) + 1);
			$pos	= @strpos($sql, '%', $offset);

			//allows skipping of %% values
			$i += $skip ? 0 : 1;
		}

		return $sql;
	}
	
	
	public function query($sql)
	{
		
		$retval = false;

		$result = mysqli_query($this->mysqli, $sql);

		return $retval;
	}
	
	public function queryFirst($sql)
	{
		if ($result = mysqli_query($this->mysqli, $sql))
		{
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			return $row;
		}
		else
		{
			return false;
		}
	}
	
	public function safeQuery()
	{
		$sql = $this->parseSafeQuery(func_get_args());
		return mysqli_query($this->mysqli, $sql);
	}

	public function safeQueryDebug()
	{
		$sql = $this->parseSafeQuery(func_get_args());

		echo $sql;
		return $sql;
	}
	
	public function safeReadQueryFirst()
	{
		$sql = $this->parseSafeQuery(func_get_args());
		return $this->queryFirst($sql);
	}

	public function safeReadQueryAll($sql)
	{
		$sql = $this->parseSafeQuery(func_get_args());
		
		$rows	= array();
		$result = mysqli_query($this->mysqli, $sql);
		
		if ($result)
		{
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				
				$rows[] = $row;
			}
		}
		
		return $rows;
	}
	
	public function affectedRows()
	{
		$num = (int)mysqli_affected_rows($this->mysqli);

		return $num;
	}
	
	public function insertId()
	{
		$id = (int)mysqli_insert_id($this->mysqli);

		return $id > 0 ? $id : false;
	}
	
}

?>