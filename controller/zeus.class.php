<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

include_once( AROOT . 'model'.DS.'zeus.class.php' );

class zeusController extends appController
{
	function __construct()
	{
		parent::__construct();
		$this->zeusModel = new zeusModel();
	}

	function index()
	{
		$data['source'] = array(
		    'tingapi',
		); 
		render($data);
	}
	
	function dashboard()
	{
		$page = intval(v('page')) > 0 ? intval(v('page')) : 1;
		$size = intval(v('size')) > 0 ? v('size') : 50; 
		$offset = ($page-1) * $size;
		
		$cuid =  v('cuid') == "" ? "" : v('cuid');
		$method =  v('method') == "" ? "" : v('method');
		$ip =  v('ip') == "" ? 0 : ip2long(v('ip'));
		$ts =  v('timestart') == "" ? 0 : strtotime(v('timestart'));
		$te =  v('timeend') == "" ? "" : strtotime(v('timeend'));
		$source =  v('source') == "" ? "" : v('source');

		$param = "cuid={$cuid}&method={$method}&ip={$ip}&ts={$ts}&te={$te}&source={$source}&size={$size}";

		$ret = $this->zeusModel ->getLogs($offset,$size,$cuid,$ip,$ts,$te,$method,$source);
		$logs = array();
		if(is_array($ret) && !empty($ret))
		{
			foreach($ret as $log)
			{
				$log['time'] = date('Y-m-d H:i:s',$log['ctime']);
				$log['ip'] = long2ip($log['ip']);
				$log['method'] = substr($log['method'],11);
				foreach($log as $k => $v)
				{
					$logs[$log['log_id']][$k] = $v;
				}
			}
		}
		$ret = $this->zeusModel ->getLogsCount($cuid,$ip,$ts,$te,$method,$source);
		$totle = intval(intval($ret)/$size)+1;
		$data['title'] = $data['top_title'] = '首页';
		$data['logs'] = $logs;
		$data['total'] = $totle;
		$data['page'] = $page;
		$data['param'] = $param;
		$startPage = ($page-1) > 0 ? ($page-1) : 1;
		for($i = $startPage;$i <= $totle;$i++)
		{
			$data['pages'][] = $i;
			if(count($data['pages']) > 4)
				break;
		}
		render( $data , null,'frame');
	}

	function detail()
	{

		$logid =  intval(v('logid'));
		if($logid <=0)
			return array();

		$ret = $this->zeusModel->getLogDataByLogIds(array($logid),true);
		if(is_array($ret) && !empty($ret))
		{
			$url = $ret[0]['url'];
			foreach($ret as $i =>$log)
			{
				$log['time'] = date('Y-m-d H:i:s',$log['ctime']);
				$log['ip'] = long2ip($log['ip']);
				$log['method'] = substr($log['method'],11);
				$log['log_data'] = urldecode($log['log_data']);
				if($log['msg'] == 'return_result')
				{
					if(preg_match('/result\[(.*)\]/',$log['log_data'],$m))
					{
						$return_data = json_decode($m[1],true);
						$return_data =var_export($return_data, true);
						$return_data = nl2br(str_replace(" ", "&nbsp;", $return_data));
						continue;
					}
				}
				if(substr($log['log_data'],0,6) == 'NOTICE')
				{
					$log['log_type'] = 'notice';
					$logs[] = $log;
				}
				else
				{
					$log['log_type'] = 'wf';
					$logs[] = $log;
				}
				if($log['msg'] == '===index_end===')
				{
					$data['log'] = $log;
				}
			}
		}
		$data['url'] = $url;
		$data['return_data']  = $return_data;
		$data['logs'] = $logs;
		render($data);
	}
	
	function ajax_test()
	{
		return ajax_echo('1234');
	}
	
	function rest()
	{
		$data = array(  );
		if( intval(v('o')) == 1 )
		{
			$data['code'] = 123;
			$data['message'] = 'RPWT';
		}
		else
		{
			$data['code'] = 0 ;
			$data['data'] = array( '2' , '4' , '6' , '8' ); 
		}
		
		return render( $data , 'rest' );
	}
	
	function mobile()
	{
		$data['title'] = $data['top_title'] = 'JQMobi';
		return render( $data , 'mobile' );
	}
	
	function ajax_load()
	{
		return ajax_echo('Hello ' . date("Y-m-d H:i:s"));
	}
	
	function about()
	{
		return info_page( "" , "Music web-rd 荣誉出品" );
	}
	
	function contact()
	{
		return info_page( "Sina Weibo - <a href='http://weibo.com/easy' target='_blank'>@Easy</a> |  Twitter - @Easychen" , "Follow Me" );
	}
	
	function test()
	{
		$data['title'] = $data['top_title'] = '自动测试页';
		$data['info'] = '根据访问来源自动切换Layout';
		
		return render( $data );
	}
	
	function sql()
	{
		db();
		echo $sql = prepare( "SELECT * FROM `user` WHERE `name` = ?s AND `uid` = ?i AND `level` = ?s LIMIT 1" , array( "Easy'" , '-1', '9.56' ) );	
	}

	function binding
	( 
		$c1 = ':is_mail|请输入正确的email地址' , 
		$b1 = ':not_empty|B1不能为空',
		$a1 = ':intval|setback'
	)
	{
		echo $c1 . '-' . $b1 . "-" . $a1;
	}
	
	
}
	
