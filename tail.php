<?php

function get_log_file($file_path)
{
	if(file_exists($file_path))
	{
		return file_get_contents($file_path);
	}
	return false;
}

function get_lines($string)
{
	return explode("\r\n", $string);
}

function tail($file)
{
    $size = 0;
	$linenum = 0;
    while (true) 
	{
        clearstatcache();		
        $currentSize = filesize('%SystemDrive%\inetpub\logs\LogFiles');
        if ($size == $currentSize) 
		{
            usleep(60000000);
            continue;
        }
		
		$lines = get_lines( get_log_file('%SystemDrive%\inetpub\logs\LogFiles') );
		$currentlinenum = count($lines);
		
		
  
		$mysqli = new mysqli('hostname', 'user', 'password', 'db');
		
		if($mysqli->connect_error)
		{
			$msg = 'Failed to connect to the database. ';
			exit;
		}
		
		for($ i = $linenum; i < $linenum; i++)
		{
			if (strlen(trim($lines[i])) > 0) 
			{
				$data = sscanf($lines[i], "%12s:%19[ -~] %s %s %s %s %s - %s %s %s %s %s");
				if(trim($data[5])=="-" || trim($data[5]=="")) 
				{
					$data[5] = '';
				} 
				else 
				{
				$data['4'] = $data[4] . "?" . $data[5];
				}
				$qry = sprintf("INSERT INTO iis_logs (`orig_report_file`, `timestamp`, `server_ip`, `request_method`, `server_port`, `client_ip`, `browser_info`, `response`, `num1`, `num2`) VALUES ('%s','%s','%s','%s',%s,'%s','%s',%s,%s,%s)", $mysqli->real_escape_string($data[0]), $data[1], $mysqli->real_escape_string($data[2]),  $mysqli->real_escape_string($data[3]),  $mysqli->real_escape_string($data[6]),  $mysqli->real_escape_string($data[7]),  $mysqli->real_escape_string($data[8]),  $data[9], $data[10], $data[11]);

				$mysqli->real_query($qry);
				
			}
		}
		
		$linenum = $currentlinenum;
		$size = $currentSize;
    }

	
}

tail("file.txt");
?>
