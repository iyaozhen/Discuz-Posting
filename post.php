<?php 

	if(!defined('IN_DISCUZ')) {
		exit('Access Denied');
	}

	$uid = $_G['uid'];	// 用户id
	$username = addslashes($_G['username']);	// 用户名
	$ip = $_G['clientip'];	// 用户ip
	$port = $_G['remoteport'];	
	$time = time();
	$view = rand(5, 60);	// 阅读量

	// 首先创建一个主题
	$insert = "INSERT INTO pre_forum_thread (fid, typeid, author, authorid, subject, dateline, lastpost, lastposter, views) VALUES ('$fid', '$typeid', '$author', '$uid', '$subject', '$time', '$time', '$username', '$view')";	// subject：帖子标题
	mysql_query($insert);

	// 获取到刚刚插入主题的id（tid）
	$query = mysql_query("SELECT LAST_INSERT_ID()");
	$result = mysql_fetch_array($query);
	$tid = $result[0];

	// 向帖子分表记录表里面插入帖子id
	$insert = "INSERT INTO pre_forum_post_tableid VALUES (NULL)";
	mysql_query($insert);
	// 获取刚插入的帖子id（pid）及马上要创建的帖子id
	$query = mysql_query("SELECT LAST_INSERT_ID()");
	$result = mysql_fetch_array($query);
	$pid = $result[0];

	// 创建帖子
	$insert = "INSERT INTO pre_forum_post (pid, fid, tid, author, authorid, subject, dateline, message, useip, port, position) VALUES ('$pid', '$fid', '$tid', '$username', '$uid', '', '$time', '$message', '$ip', '$port', '1')";	// subject（标题需要留空），message：帖子信息，position：楼层
	mysql_query($insert);

	// 更新版块新帖记录
	$sql = "SELECT threads, posts FROM pre_forum_forum WHERE fid = '$fid'";
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);
	$threads = $result['threads']+1;	// 主题数+1
	$posts = $result['posts']+1;	// 帖子数+1
	$lastpost = $tid."\t".$subject."\t".$time."\t".$username;
	$sql = "UPDATE pre_forum_forum SET threads = '$threads', posts = '$posts', lastpost = '$lastpost' WHERE fid = '$fid'";
	mysql_query($sql);

?>