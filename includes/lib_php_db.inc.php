<?php

// $Id: lib_php_db.inc 314 2007-11-30 13:41:34Z cpo $

class database
{
	private $host;
	private $database;
	private $user;
	private $password;
	private $port;
	private $database_type;

	private $sql;
	private $con;
	
	public $errors;
	public $error_count=0;

	function database($database_type,$host,$database,$user,$password,$port=false)
	{
		$this->host=$host;
		$this->database=$database;
		$this->user=$user;
		$this->password=$password;
		$this->port=$port;
		$this->database_type=$database_type;

		if (!in_array($database_type,array("mysqli","mysql")))
		{
			return false;
		}

		if(!$this->connect())
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function connect()
	{
		global $logger;
		
		for ($i=0;$i<3;$i++)
		{
			if ($this->database_type=="mysqli")
			{
				$this->con = mysqli_connect($this->host, $this->user,$this->password);
			}
			else
			{
				$this->con = mysql_connect($this->host, $this->user,$this->password);
			}
			
			if ($this->con !== FALSE)
			{
				break;
			}
			
			sleep(1);
			
			if ($this->database_type=="mysqli")
			{
				$logger->warn(LOG_SQL, 'Echec de connexion #'.($i+1).' : '.mysqli_connect_error());
			}
			else
			{
				$logger->warn(LOG_SQL, 'Echec de connexion #'.($i+1).' : '.mysql_error());
			}
		}

						
		if ($this->con === FALSE)
		{
			if ($this->database_type=="mysqli")
			{
				$msg = 'ECHEC DE CONNEXION A LA BASE : '.mysqli_connect_error();
				$logger->error(LOG_SQL, $msg);
				$this->errors[$this->error_count]=$msg;
			}
			else
			{
				$msg = 'ECHEC DE CONNEXION A LA BASE : '.mysql_error();
				$logger->error(LOG_SQL, $msg);
				$this->errors[$this->error_count]=$msg;
			}
			
			$this->error_count++;
			return false;
		}
		else if ($i!=0)
		{
			$logger->warn(LOG_SQL, 'CONNEXION Ok ('.$i.')');
		}

		if ($this->database_type=="mysqli")
		{
			$return = mysqli_select_db($this->con,$this->database);
		}
		else
		{
			$return = mysql_select_db($this->database,$this->con);
		}

		if(!$return)
		{
			$this->errors[$this->error_count] = $this->error();
			$this->error_count++;
			return false;
		}
		else
		{
			return true;
		}
	}

	function disconnect()
	{
		if ($this->database_type=="mysqli")
		{
			mysqli_close($this->con);
		}
		else
		{
			mysql_close($this->con);
		}
	}

	function query($sql_statement, $debug=false)
	{
		global $logger;

		$this->sql=$sql_statement;

		if ($this->database_type == 'mysqli')
		{
			$result = mysqli_query($this->con,$this->sql);
		}
		else
		{
			$result = mysql_query($this->sql,$this->con);
		}

		if (!$result)
		{
			ob_clean();
			$tmp = debug_backtrace();
				
			$error = 'Echec de la requete : '.$tmp[count($tmp)-1]['file'].' (line: '.$tmp[count($tmp)-1]['line'].")\n  ".$sql_statement."\n  ".$this->error();
				
			$logger->error(LOG_SQL, $error);
			echo $error;
			exit();
		}
		else
		{
			//$logger->debug(LOG_SQL, $sql_statement);
		}

		if ($debug)
		{
			echo "<table><tr><td>$sql_statement</td></tr><tr><td>";

			if ($result !== true && $row = $this->get_row($result))
			{
				echo "<table border=1><tr><th></th>";
				while (list ($key, $val) = each ($row))
				{
					if ((string)$key == (string)intval($key))
					continue;
					echo "<th>$key</th>";
				}
				echo "</tr>";

				$this->data_seek($result, 0);
				$i = 1;

				while ($row = $this->get_row($result))
				{
					echo "<tr><td>$i</td>";

					while (list ($key, $val) = each ($row))
					{
						if ((string)$key == (string)intval($key))
						{
							continue;
						}

						if (is_null($val))
						{
							echo "<td><i>NULL</i></td>";
						}
						else if ($val == "")
						{
							echo "<td>&nbsp;</td>";
						}
						else
						{
							echo "<td>$val</td>";
						}
					}
					echo "</tr>";
					$i++;
				}
				echo "</table></td></tr></table>";

				if ($this->num_rows($result) > 0)
				{
					$this->data_seek($result, 0);
				}
			}
			else
			{
				echo "Pas de résultat</td></tr></table>";
			}
		}

		return $result;
	}

	function get_row($query_result, $htmlencode=true)
	{
		if ($this->database_type == 'mysqli')
		{
			$row = mysqli_fetch_assoc($query_result);
		}
		else
		{
			$row = mysql_fetch_assoc($query_result);
		}
		
		if (is_array($row) && (count($row) > 0) && ($htmlencode == true))
		{
			foreach ($row as $key => $val)
			{
				if (!is_null($val))
				{
					$row[$key] = htmlentities($val, ENT_COMPAT, 'UTF-8');
				}
			}
		}

		return $row;
	}

	function num_rows($query_result)
	{
		if ($this->database_type == 'mysqli')
		{
			return mysqli_num_rows($query_result);
		}
		else
		{
			return mysql_num_rows($query_result);
		}
	}

	function data_seek($query_result,$rownum)
	{
		if ($this->database_type == 'mysqli')
		{
			return mysqli_data_seek($query_result, $rownum);
		}
		else
		{
			return mysql_data_seek($query_result, $rownum);
		}
	}

	function get_last_table_index($sql_table, $sql_col)
	{
		$sql="select MAX($sql_col) as MAXINDEX from $sql_table";
		$result = $this->query($sql);

		if ($this->num_rows($result) == 0)
		{
			return 0;
		}

		$row = $this->get_row($result);

		return $row['MAXINDEX'];
	}

	function get_insert_id()
	{
		if ($this->database_type == 'mysqli')
		{
			return mysqli_insert_id($this->con);
		}
		else
		{
			return mysql_insert_id($this->con);
		}
	}
	
	function escape_string($txt)
	{
		if ($this->database_type == 'mysqli')
		{
			return mysqli_real_escape_string($this->con, $txt);
		}
		else
		{
			return mysql_real_escape_string($txt, $this->con);
		}
	}
	
	function error()
	{
		if ($this->database_type == 'mysqli')
		{
			return mysqli_error($this->con);
		}
		else
		{
			return mysql_error($this->con);
		}
	}

	function get_errors()
	{
		return $this->errors;
	}

	function dump()
	{
		set_time_limit(60*15);

		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Cache-control: private");
		header("Content-Type: application/octetstream");
		header("Content-Disposition: attachment; filename=\"dump-".$this->database."-".date("Ymd-His").".sql\"");

		echo "# Serveur ".$this->host."\n";
		echo "# Base ".$this->database."\n";
		echo "# Généré le ".date("d/m/Y H:i:s")."\n\n";

		$result = $this->query("SHOW TABLES");

		while($row = $this->get_row($result, false))
		{
			$table = $row[0];

			$result2 = $this->query("SHOW CREATE TABLE `$table`");

			$row2 = $this->get_row($result2, false);

			echo $row2["Create Table"].";\n\n";

			$result3 = $this->query("SELECT * FROM `$table`");

			while($row3 = $this->get_row($result3, false))
			{
				echo "INSERT INTO `$table` VALUES (";

				$i = 0;
				foreach ($row3 as $colname=>$colvalue)
				{
					if ($i++%2 == 0)
					{
						continue;
					}

					if ($i > 2)
					{
						echo ",";
					}

					if (is_null($colvalue))
					{
						echo "NULL";
					}
					else
					{
						$colvalue = addslashes($colvalue);
						$colvalue = str_replace("\n", '\r\n', $colvalue);
						$colvalue = str_replace("\r", '', $colvalue);
						echo "'$colvalue'";
					}
				}

				echo ");\n";
			}

			echo "\n\n";
		}

		return $output;
	}
}


define('DB_Type', 'mysqli');
define('DB_Server', 'localhost');
define('DB_Login', 'root');
define('DB_Password', 'egM8s2xST5r9');
define('DB_Name', 'tpo');

$db2=new database(DB_Type,DB_Server,DB_Name,DB_Login,DB_Password);

if ($db2->error_count != 0)
{
	echo ("<html><head><title></title></head><body>ECHEC DE CONNEXION A LA BASE : <pre>");
	echo ($db->errors[0]);
	echo ("</pre></body></html>");
	exit();
}

$db2->query("SET NAMES 'utf8';");
