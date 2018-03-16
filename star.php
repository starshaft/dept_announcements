<?php 

/**
1. Get all announcement where date is acceptable today
2. Filter what department
3. If department no result = no further announcement on this 
4. Else get post_type
5. Render announcement based on post type
**/


define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "@dm1n");
define("DB_NAME", "cmmb2");

function db_connect() {
    $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    confirm_db_connect();
    return $connection;
  }

  function db_disconnect($connection) {
    if(isset($connection)) {
      mysqli_close($connection);
    }
  }

  function db_escape($connection, $string) {
    return mysqli_real_escape_string($connection, $string);
  }

  function confirm_db_connect() {
    if(mysqli_connect_errno()) {
      $msg = "Database connection failed: ";
      $msg .= mysqli_connect_error();
      $msg .= " (" . mysqli_connect_errno() . ")";
      exit($msg);
    }
  }

  function confirm_result_set($result_set) {
    if (!$result_set) {
      exit("Database query failed.");
    }
  }

  $db = db_connect();


function get_all_announcement_today(){
	global $db;

	$today = strtotime("today");
	$today = date('Y-m-d', $today);

	$sql = "SELECT * FROM announcement ";
	$sql .= "WHERE startdate <= '".$today."' ";
	$sql .= "AND enddate >= '".$today."'"; 
	$result = mysqli_query($db, $sql);
	confirm_result_set($result);
	return $result;
}


function get_announcement_today_by_department($department){
	global $db;

	$today = strtotime("today");
	$today = date('Y-m-d', $today);

	$sql = "SELECT * FROM announcement ";
	$sql .= "WHERE startdate <= '".$today."' ";
	$sql .= "AND enddate >= '".$today."' "; 
	$sql .= "And department = '".$department."' LIMIT 1";
	$result = mysqli_query($db, $sql);
	confirm_result_set($result);
	if(mysqli_num_rows($result) > 0){
		return $result->fetch_assoc();
	}
	return false;
}


function templater($announcement,$class){
	$a = $announcement;
	if($a["post_type"] == "text"){
		$template = "<div class='{$class}'><p><span class='label'>Department: </span>{$a['department']}</p><h3 class='text_post'>";
		$template .= $a["content"]."</h3></div>";
		return $template;
	}elseif($a["post_type"] == "image"){
		$template = "<div class='{$class}'><p><span class='label'>Department:</span>{$a['department']}</p><div class='img-wrapper'><img src='images/";
		$template .= $a["content"]."' class='img_post'></div></div>";
		return $template;
	}elseif($a["post_type"] == "video"){
		$template = "<div class='{$class}'><p><span class='label'>Department: </span>{$a['department']}</p><div style='text-align:center'><video height='150' src='videos/";
		$template .= $a["content"]."' class='u-max-width'></div></div>";
		return $template;
	}else{
		$template = "<div class='{$class}'><p class='dept_text'><span class='label'>Department: </span>".$dept;
		$template .= "</p><h3>No Announcement<h3></div>";
		return $template;
	}
}



function div_generator($dept1, $dept2)
{
	$a = get_announcement_today_by_department($dept1);
	$b = get_announcement_today_by_department($dept2);
	return templater($a, "show").templater($b, "hide");
}