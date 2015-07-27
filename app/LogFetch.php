<?php

namespace VacStatus;

use DateTime;

class LogFetch {

	private $logPath;
	private $logRegex = '/\[(?<time>(?:[0-9]|\-|\:|\s)*?)\](?:\s)(?<type>.*?)(?:.\s)(?<message>.*?)(?:\nStack trace:\n)(?<trace>(?:#\d.*(?:\n|))*)/i';

	function __construct()
	{
		$this->logPath = base_path()."/storage/logs/";
	}

/************************\
|                        |
|   REGEX TO PARSE LOG   |
|________________________|

	/
		\[
			(?<time>(?:[0-9]|\-|\:|\s)*?)
		\]
		(?:\s)
		(?<type>.*?)
		(?:.\s)
		(?<message>.*?)
		(?:\nStack trace:\n)
		(?<trace>(?:#\d.*(?:\n|))*)
	/g
*/

	public function __toString()
	{
		$logPath = $this->logPath;
		$logList = [];

		$openLogs = scandir($logPath, 1);
		$openLogs = array_diff($openLogs, ['..', '.', '.gitignore']);
		$openLogs = array_slice($openLogs, 0, 5);

		foreach($openLogs as $log)
		{
			$logList[] = [
				'filename' => $log,
				'filesize' => number_format((float) (filesize($logPath.$log) / 1024), 2, '.', '')
			];
		}
	}

	public function viewLog($filename)
	{
		$logPath = $this->logPath;
		$logRegex = $this->logRegex;

		if(!file_exists($logPath.$filename)) return false;

		$data = [
			'filename' => $filename,
			'filesize' => number_format((float) (filesize($logPath.$filename) / 1024), 2, '.', ''),
			'log' => []
		];

		$file = file($logPath.$filename);

		$logTemp = [];

		foreach($file as $line) {
			switch(substr($line, 0, 1))
			{
				case '[':
					if(count($logTemp) > 0) $data['log'][] = $logTemp;

					preg_match('/\[(?<time>(?:[0-9]|\-|\:|\s)*?)\](?:\s.*?\.)(?<type>.*?)(?:.\s)(?<message>.*)/i', $line, $logLine);

					$logTemp = [
						'time' => new DateTime($logLine['time']),
						'type' => strtolower($logLine['type']),
						'message' => $logLine['message'],
						'trace' => []
					];
					break;
				case '#':
					$line = str_replace("\n", "", $line);
					$line = str_replace("\r", "", $line);
					$line = str_replace(base_path()."/", "", $line);
					$logTemp['trace'][] = $line;
					break;
			}
		}

		return $data;
	}
}