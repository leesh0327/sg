<?php
$sql ="SELECT * FROM {$tara['news_tb']} WHERE DELETED_DT IS NULL ORDER BY NEWS_IX DESC";
	$result = sql_query($sql);
  
	$list = "";
	while($row = sql_fetch_array($result)) {
	  $filename = explode(".", $row['NEWS_PATH']);
	  $ftype =explode('/' , $row['FILE_TYPE']);
	  $filehtml ="";
  
	  if($ftype[0] === 'video'){
		  $filehtml ="
		  <div class='news-video' >
		  <div class='embed-responsive embed-responsive-16by9'>
			  <video controls class='embed-responsive-item' >
				  <source src='./".$row['NEWS_PATH']."' type='".$row['FILE_TYPE']."'>
			  </video>
		  </div></div>";
	  }elseif($ftype[0] === 'image'){
		  $filehtml ="<div class='news-img' style='background: url(./".$row['NEWS_PATH'].") no-repeat 50% 50%; background-size: contain;'></div>";
	  }elseif($ftype[0] === 'application'){  
		  $filehtml ="<a href='{$row['NEWS_PATH']}' target='_blank'>
		  <div class='news-pdf' style='background: url(./assets/img/pdf.png) no-repeat 50% 50%;  background-size: contain; '>
		  </div>
		  </a>
		  ";
	  }
	  $list = $list."<div class='item'><div class='card'>";
	  if($row['NEWS_URL']){
		  $list = $list."<a class='d-block' href='{$row['NEWS_URL']}' target='_blank'>";
	  }
	  $list = $list.$filehtml;
	  if($row['NEWS_URL']){
		  $list = $list."</a>";
	  }
	  $list = $list."<div class='card-body'>";
	  $list = $list."<h5 class='card-title text-center'>".$row['NEWS_TI']."</h5>";
	  $list = $list."</div></div></div>";
  } 
?>