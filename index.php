<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

function getFileList($dir, &$list){

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
    	getFileList($fullpath, $list);
    }else{
    	$list[$dir][] = $file;
    }
 	}
 	return $list;
}

function displayDirMap($dir){
	$files = scandir($dir);
 	$files = array_filter($files, function ($file) {
     return !in_array($file, array('.', '..'));
 	});

 	$list = array();
 	echo "<ul class=\"".$dir."\">";
  foreach ($files as $file) {
   	// 隠しファイルをリストから取り除く
  	if(substr($file,0,1) == '.'){
  		continue;
  	}

    $fullpath = rtrim($dir, '/') . '/' . $file;
    if (is_dir($fullpath)) {
			$class = str_replace("/", "_", $dir);
    	echo "<li><span class='dirname ".$class."'>".$file."</span>";
    	displayDirMap($fullpath);
      echo "</li>";
    }
 	}
 	echo "</ul>";
 	return $list;
}
$file_list = array();
// $start_dir = posix_getpwuid(posix_geteuid())['dir']; //Userディレクトリ以降のファイルを取得したい場合
$start_dir = __DIR__; // このプログラムのディレクトリ以降のファイルを取得したい場合
getFileList($start_dir,$file_list);

?>

<!DOCTYPE html>
<html>
<head>
<link rel='stylesheet' href='mystyle.css' />
<script type="text/javascript" src='jquery-1.11.2.min.js'></script>
<script type="text/javascript" src='myscript.js'></script>
<script type="text/javascript">
var bc = ["#FFFFFF","#00BFFF","#7FFF00","#FFFF00","#000000","#FF0000"];
var fs = ["15px","20px","25px"];

function chBackGround(e) {
	e.style.color = bc[e.selectedIndex];
	e.style.backgroundColor = bc[e.selectedIndex];
}

function changeFileColor(e,number) {
	var id = "#fav"+number;
	$(id).css('background-color',bc[e.selectedIndex]);
}

function changeFontSize(e,number) {
	var file = ".file"+number;
	$(file).css('font-size',fs[e.selectedIndex]);
}

function changeFav(e) {
	if(e.style.color == 'white'){
		e.style.color = 'Gold';
	}else{
		e.style.color = 'white';
	}
}

$(function() {
	$('.color').css("background-color", bc[0]);
	$('.color').css("color", bc[0]);

	$(".dirname").click(function(){
		var path = ($(this).attr('class').slice(8) + "/" + $(this).text()).replace(/_/gi,"/");
		$(".now-directory-path").text(path);

		var classes = [".names",".extentions",".sizes",".createtimes",".lastedittimes",".importantlevels",".colors"];
		classes.forEach(function(classname){
			$(classname).hide();
		});

		var display_class = "." + $(this).attr('class').slice(8) + "_" + $(this).text();
		$(display_class).show();
	});
});
</script>

</head>
<body>
<div class='contents'>


<table rules="all">
	<tr>
		<td rowspan= '3' colspan='2' class='directory-map'>
			<div>
			<ul class="<?php echo dirname($start_dir) ?>">
				<li><span class="dirname <?php echo str_replace("/", "_", dirname($start_dir)) ?>"><?php echo basename($start_dir) ?></span>
				<?php displayDirMap(__DIR__);?>
				</li>
			</ul>
			</div>
		</td>
		<td height='30px' colspan='4' class='now-directory-path'><?php echo __DIR__ ?></td>
		<td height='30px' colspan='3'>検索:
			<input class='search' type="text" name="search" size='30'>
		</td>
	</tr>

	<tr height='10px'>
		<th>ファイル名</th>
		<th>拡張子</th>
		<th>サイズ</th>
		<th>作成日</th>
		<th>最終更新日時</th>
		<th>重要度</th>
		<th>色</th>
	</tr>

	<tr>
		<td rowspan='2' class='name-map'>
			<?php
			foreach($file_list as $dir => $filenames){ 
				$loop_count = 0; 
				$class = str_replace("/", "_", $dir); ?>
				<div class='names <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $filename){?>
					<div class='file<?php echo $loop_count ?>'>
					<span id='fav<?php echo $loop_count ?>'class='favstar' style='color:white' onClick='changeFav(this);'>★</span>
					<span class='name'><?php echo $filename ?></span><br />
					</div><?php
					$loop_count++;
				} ?>
				</div> <?php 
			}
			?>
		</td>
		<td rowspan='2' class='extention-map'>
			<?php
			foreach($file_list as $dir => $filenames){ 
				$class = str_replace("/", "_", $dir); ?>
				<div class='extentions <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){
					$info = new SplFileInfo($filename);
			  	?><div class='file<?php echo $number ?>'><?php echo $info->getExtension() ?><br /></div><?php
		  	} ?>
		  	</div> <?php
			}
			?>
		</td>
		<td rowspan='2' class='size-map'>
			<?php
			foreach($file_list as $dir => $filenames){ 
				$class = str_replace("/", "_", $dir); ?>
				<div class='sizes <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){
					$size = stat($dir."/".$filename)['size'];
		  		?><div class='file<?php echo $number ?>'><?php echo $size ?><br /></div><?php
		  	} ?>
		  	</div> <?php
		  }
		  ?>
		</td>

		<td rowspan='2' class='createtime-map'>
			<?php
			foreach($file_list as $dir => $filenames){
				$class = str_replace("/", "_", $dir); ?>
				<div class='createtimes <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){
					$filepath = $dir."/".$filename;
					?><div class='file<?php echo $number ?>'><?php echo strftime("%Y-%m-%d",filectime($filepath)) ?><br /></div><?php
				} ?>
				</div> <?php
		  }
		  ?>
		</td>

		<td rowspan='2' class='lastedittime-map'>
			<?php
			foreach($file_list as $dir => $filenames){ 
				$class = str_replace("/", "_", $dir); ?>
				<div class='lastedittimes <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){
					$last_edit_time = stat($dir."/".$filename)['mtime']
					?><div class='file<?php echo $number ?>'><?php echo strftime("%Y-%m-%d %T",$last_edit_time) ?><br /></div><?php
		  	} ?>
		  	</div> <?php
		  }
		  ?>
		</td>

		<td rowspan='2' class='importantlevel-map'>
			<?php 
			foreach($file_list as $dir => $filenames){ 
				$class = str_replace("/", "_", $dir); ?>
				<div class='importantlevels <?php echo $class ?>' style='display:none'><?php
				$count = 0;
				foreach($filenames as $number => $filename){
					while($count < count($filenames)): ?>
						<div>
						<select name="important" class='important' onChange='changeFontSize(this,<?php echo $count ?>);'>
						<option value="row" class='row'>低</option>
						<option value="normal"  class='normal'>中</option>
						<option value="high"   class='high'>高</option>
						</select>
						</div>
						<?php
						$count++;
					endwhile;
				} ?>
				</div> <?php
			}
			?>
		</td>
		<td rowspan='2' class='color-map'>
			<?php 
			$count = 0;
			foreach($file_list as $dir => $filenames){ 
				$class = str_replace("/", "_", $dir); ?>
				<div class='colors <?php echo $class ?>' style='display:none'><?php
				$count = 0;
				foreach($filenames as $number => $filename){
					while($count < count($filenames)): ?>
						<div>
						<select name="color" class='color' onChange="chBackGround(this); changeFileColor(this,<?php echo $count ?>);">
							<option value="red"   class='white'>白</option>
							<option value="blue"  class='blue'>青</option>
							<option value="green" class='green'>緑</option>
							<option value="yellow" class='yellow'>黄</option>
							<option value="black" class='black'>黒</option>
							<option value="red" class='red'>赤</option>
						</select>
						</div>
						<?php
						$count++;
					endwhile;
				} ?>
				</div> <?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			説明<br />
		</td>
	</tr>
</table>

<a class="btn save" href="#">保存</a>
</div>

<script>
$(function(){
	$(<?php echo "\".".str_replace("/", "_", $start_dir)."\"" ?>).show();
});
</script>

</body>
</html>