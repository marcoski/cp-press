<ol class="breadcrumb">
<?php 
	foreach($elements as $element){
		echo '<li';
		if($element['active']){
			echo ' class="active">';
		}else{
			echo '>';
			echo '<a href="' . $element['link'] . '">';
		}
		echo $element['title'];
		if(!$element['active']){
			echo '</a>';
		}
		echo '</li>';
		
	}
?>
</ol>