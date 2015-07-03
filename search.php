<?php

$search_result = array();
function searchFileList($dir, $search_word){
	global $search_result;

 	$files = scandir($dir);
 	$files = array_filter($files, function ($file) {
     return !in_array($file, array('.', '..'));
 	});
 
  foreach ($files as $file) {
  	// 隠しファイルをリストから取り除く
  	if(substr($file,0,1) == '.'){
  		continue;
  	}


    $fullpath = rtrim($dir, '/') . '/' . $file;

    if (is_dir($fullpath)) {
    	searchFileList($fullpath, $search_word);
    }else{
    	if(preg_match("/".$search_word."/", $fullpath)){
    		$search_result[$dir][] = $file;
    	}
    }
 	}
}

$target_directory = $_GET['dir'];
$search_word      = $_GET['search'];
searchFileList($target_directory, $search_word);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style type="text/css">
	body {
		width: 100%;
		height: 1000px;
	}
	article {
		display: block;
		text-align: center;
	}

	table {
		margin:0 auto;
		border: solid 2px;
	}
	td {
		height: 50px;
	}
	.btn {
		display: block;
		margin:0 auto;
  	text-decoration: none;
  	font-weight: bold;
  	text-align: center;
  	font-size: 13px;
		width:80px;
		background-color:#4169E1;
		color:white;
		padding:10px 20px;
		border-radius:5px;
	}

	.btn:hover {
		opacity:0.7;
	}
</style>
</head>
<body>
<article>
<p>検索結果</p>
<table rules='all'>
<thead>
	<tr>
		<th>ディレクトリ名</th>
		<th>ファイル名</th>
	</tr>
</thead>
<tbody>
<?php 
	foreach($search_result as $dir => $files){
		foreach($files as $file){
			echo "<tr><td>".$dir."</td><td>".$file."</td></tr>";
		}
	}
?>
</tbody>
</table>
<p><a class='btn' href='index-generator.php'>戻る</a></p>
</article>

</body>
</html>