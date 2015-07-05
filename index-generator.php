<?php

// $start_dir = posix_getpwuid(posix_geteuid())['dir']; //Userディレクトリ以降のファイルを取得したい場合
$start_dir = __DIR__; // このプログラムのディレクトリ以降のファイルを取得したい場合

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
getFileList($start_dir,$file_list);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel='stylesheet' href='mystyle.css' />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript">
var bc = ["#FFFFFF","#00BFFF","#7FFF00","#FFFF00","#000000","#FF0000"];
var fs = ["15px","20px","25px"];

$(function() {
	$('.color').css("background-color", bc[0]);
	$('.color').css("color", bc[0]);

	$(".dirname").click(function(){
		var path = ($(this).attr('class').slice(8) + "/" + $(this).text()).replace(/_/gi,"/");
		$(".now-directory-path").text(path);

		var classes = [".names",".extensions",".sizes",".createtimes",".lastedittimes",".importantlevels",".colors"];
		classes.forEach(function(classname){
			$(classname).hide();
		});

		var display_class = $(this).attr('class').slice(8) + "_" + $(this).text();
		$("." + display_class).show();
		$("input.target_dir").attr('value', path);
		$(".explains").hide();
		$(".explain").hide();
		$(".explains textarea").prop("disabled", true);
		$(".colors select").prop("disabled", true);
		$(".importantlevels select").prop("disabled", true);

		$("div[class=\'explains " + display_class + "\']").show();
		$("div[class=\'explains " + display_class + "\'] textarea").prop("disabled", false);
		$("div[class=\'colors " + display_class + "\'] select").prop("disabled", false);
		$("div[class=\'importantlevels " + display_class + "\'] select").prop("disabled", false);
	});

	$(".name-map span.name").click(function(){
		$(".explain").hide();

		var display_class = $(this).parent().get(0).className.slice(6);
		$("div[class=\'explain " + display_class + "\']").show();
	});

	$(".favstar").click(function(){
		var color = $(this).css('color');
		if(color == 'rgb(255, 255, 255)' || color == '#FFFFFF'){
			$(this).css('color','Gold');
		}else{
			$(this).css('color','white');
		}
	});

	$(".color-map .color").change(function(){
	  // 色欄の色を変更
	  var selected_index = $(this).prop('selectedIndex');
	 	$(this).css('color', bc[selected_index]);
	 	$(this).css('backgroundColor', bc[selected_index]);

	 	// お気に入り欄の背景色を変更
	 	var class_name = $(this).attr('class').slice(6);
		$("span[class=\'favstar " + class_name + "\']").css('background-color', bc[selected_index]);
	});

	$(".importantlevel-map .important").change(function(){
		var target_class = $(this).attr('class').slice(10);

	  var selected_index = $(this).prop('selectedIndex');
	  var classes = ["name","extension","size","createtime","lastedittime"];
	  classes.forEach(function(class_name){
    	$("span[class=\'" + class_name + " " + target_class + "\']").css('font-size', fs[selected_index]);
  	});
	});

	$("input.search").bind('keyup', function(){
		if($(this).val().length == 0 || $(this).val().length > 30){
			$(".search-submit").prop("disabled", true);
		}else{
			$(".search-submit").prop("disabled", false);
		}
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
		<td height='30px' colspan='3'>
			<form action='search.php' action='post'>
				<input type='hidden'  name='dir' value='<?php echo $start_dir ?>'/>
				検索:<input class='search' type="text" name="search" size='30' />
				<input class='search-submit' type='submit' value='GO' disabled/>
			</form>
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

	<form action='generate-index.php' method='post'>
	<tr>
		<td rowspan='2' class='name-map'>
			<?php
			foreach($file_list as $dir => $filenames){
				$loop_count = 0;
				$class = str_replace("/", "_", $dir); ?>
				<div class='names <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $filename){
					$name = pathinfo($filename); ?>
					<div class='file<?php echo $loop_count ?> <?php echo $class."_".$filename ?>'>
					<span class='favstar <?php echo $class."_".$filename ?>'><input name='fav_<?php echo $filename ?>'type='checkbox' value='true'/></span>
					<span class='name <?php echo $class."_".$filename ?>'><?php echo $name['filename']; ?></span><br />
					</div><?php
					$loop_count++;
				} ?>
				</div> <?php
			}
			?>
		</td>
		<td rowspan='2' class='extension-map'>
			<?php
			foreach($file_list as $dir => $filenames){
				$class = str_replace("/", "_", $dir); ?>
				<div class='extensions <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){
					$info = pathinfo($dir."/".$filename); ?>
			  	<div class='file<?php echo $number ?> <?php echo $class."_".$filename?>'>
			  	<span class='extension <?php echo $class."_".$filename?>'>
            <?php echo $info['extension'] ?>
          </span><br />
			  	</div><?php
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
					$stat = stat($dir."/".$filename);
          $size = $stat['size']; ?>
		  		<div class='file<?php echo $number ?> <?php echo $class."_".$filename?>'>
		  		<span class='size <?php echo $class."_".$filename?>'><?php echo $size ?></span><br />
		  		</div><?php
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
					$filepath = $dir."/".$filename; ?>
					<div class='file<?php echo $number ?> <?php echo $class."_".$filename?>'>
					<span class='createtime <?php echo $class."_".$filename?>'><?php echo strftime("%Y-%m-%d",filectime($filepath)) ?></span><br />
					</div><?php
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
					$stat = stat($dir."/".$filename);
          $last_edit_time = $stat['mtime'] ?>
					<div class='file<?php echo $number ?> <?php echo $class."_".$filename?>'>
					<span class='lastedittime <?php echo $class."_".$filename?>'><?php echo strftime("%Y-%m-%d %T",$last_edit_time) ?></span><br />
					</div><?php
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
				foreach($filenames as $number => $filename){ ?>
						<div>
						<select name="important_<?php echo $filename ?>" class='important <?php echo $class."_".$filename?>'>
						<option value="1" class='row'>低</option>
						<option value="2" class='normal'>中</option>
						<option value="3" class='high'>高</option>
						</select>
						</div>
						<?php
				} ?>
				</div> <?php
			}
			?>
		</td>
		<td rowspan='2' class='color-map'>
			<?php
			foreach($file_list as $dir => $filenames){
				$class = str_replace("/", "_", $dir); ?>
				<div class='colors <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){ ?>
						<div>
						<select name="color_<?php echo $filename ?>" class='color <?php echo $class."_".$filename?>'>
							<option value="white"   class='white'>白</option>
							<option value="blue"  class='blue'>青</option>
							<option value="green" class='green'>緑</option>
							<option value="yellow" class='yellow'>黄</option>
							<option value="black" class='black'>黒</option>
							<option value="red" class='red'>赤</option>
						</select>
						</div>
						<?php
				} ?>
				</div> <?php
			}
			?>
		</td>
	</tr>
	<tr>
		<td colspan='2' class='explain-file'>
			<span>説明</span>
			<?php
			foreach($file_list as $dir => $filenames){
				$class = str_replace("/", "_", $dir); ?>
				<div class='explains <?php echo $class ?>' style='display:none'><?php
				foreach($filenames as $number => $filename){ ?>
					<div class='explain <?php echo $class."_".$filename?>' style='display:none'>
					<textarea name='explain_<?php echo $filename ?>' cols='30' rows='12'></textarea>
					</div> <?php
		  	} ?>
		  	</div> <?php
		  } ?>
			<br />
		</td>
	</tr>
</table>
<input class='target_dir' name='target_dir' value='<?php echo $start_dir ?>' type='hidden' />
<input name='start_dir' value='<?php echo $start_dir ?>' type='hidden' />
<p><input class='btn save' type="submit" value="保存" /></p>

</form>
</div>

<script>
$(function(){
	$("<?php echo ".".str_replace("/", "_", $start_dir) ?>").show();
	$(".explain").hide();

	$("textarea").prop("disabled", true);
	$("select").prop("disabled", true);
	$("<?php echo ".".str_replace("/", "_", $start_dir) ?> textarea").prop("disabled", false);
	$("<?php echo ".".str_replace("/", "_", $start_dir) ?> select").prop("disabled", false);
});
</script>

</body>
</html>
