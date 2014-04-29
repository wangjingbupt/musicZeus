<?php
/**
 *
 *
 * @author wangjing(wangjing25@baidu.com)
 * @date: Fri 25 Apr 2014 04:20:58 PM CST
 *
 */

class zeusModel 
{
	public function __construct() 
	{

	}

	public function getLogs($offset,$size,$cuid = '',$ip=0,$ts=0,$te=0,$method='',$source = '')
	{
		$logIds = $this->getLogIds($offset,$size,$cuid,$ip,$ts,$te,$method,$source);


		if(empty($logIds) || !is_array($logIds))
		{
			return array();
		}

		foreach($logIds as $id)
		{
			$ids[] = $id['log_id'];
		}

		$data = $this->getLogDataByLogIds($ids);
		if(empty($data) || !is_array($data))
		{
			return array();
		}

		return $data;

	}

	public function getLogsCount($cuid = '',$ip=0,$ts=0,$te=0,$method='',$source = '')
	{
		$sql = "SELECT count(distinct(log_id)) as `num` FROM `music_api_log_data` ";

		$w = array();
		if($cuid != '')
			$w[] = " `cuid` = '{$cuid}' ";
		if($ip > 0)
			$w[] = " `ip` = {$ip} ";
		if($ts > 0)
			$w[] = " `ctime` >= {$ts} ";
		if($te > 0)
			$w[] = " `ctime` <= {$te} ";
		if($method != '')
			$w[] = " `method` LIKE '%{$method}%' ";
		if($source != '')
			$w[] = " `source` = '{$source}' ";


		if(!empty($w))
			$sql .= "WHERE ". implode(' AND ',$w);


		$db = db();
		$data = get_line($sql);
		return $data['num'];

	}

	public function getLogDataByLogIds($logIds,$isAll = false)
	{
		$ids = implode(',',$logIds);
		if($isAll)
			$sql = "SELECT * FROM `music_api_log_data` WHERE `log_id` IN ({$ids})  ORDER BY `ctime` DESC";
		else
			$sql = "SELECT * FROM `music_api_log_data` WHERE `log_id` IN ({$ids}) AND `msg` = '===index_end==='   ORDER BY `ctime` DESC";

		$db = db();
		$data = get_data($sql);

		return $data;


	}

	private function getLogIds($offset,$size,$cuid = '',$ip=0,$ts=0,$te=0,$method='',$source = '')
	{
		$sql = "SELECT distinct(log_id) FROM `music_api_log_data` ";

		$w = array();
		if($cuid != '')
			$w[] = " `cuid` = '{$cuid}' ";
		if($ip > 0)
			$w[] = " `ip` = {$ip} ";
		if($ts > 0)
			$w[] = " `ctime` >= {$ts} ";
		if($te > 0)
			$w[] = " `ctime` <= {$te} ";
		if($method != '')
			$w[] = " `method` LIKE '%{$method}%' ";
		if($source != '')
			$w[] = " `source` = '{$source}' ";


		if(!empty($w))
			$sql .= "WHERE ". implode(' AND ',$w);

		$sql .= "ORDER BY `ctime` DESC LIMIT {$offset},{$size}";

		$db = db();
		$data = get_data($sql);
		return $data;

	}


}

