<?php



class DynamicAvatar {


	public function __construct ($baseDirectory) {
		$this->baseDirectory = $baseDirectory;
	}


	public function getWardrobe () {
		$iterator = new DirectoryIterator($this->baseDirectory);
		$layer = false;
		foreach ($iterator as $i) {

			if ($i->isDot() || !$i->isDir()) continue;

			$directory = $i->getFileName();
			$layerDir = new DirectoryIterator($this->baseDirectory."/{$directory}");
			$add = [];
			ksort($layerDir);
			foreach ($layerDir as $l) {
				if ($l->isDot() || $l->isDir()) continue;
				$add[$l->getFileName()] = $l->getFileName();
			}
			asort($add);
			$items[$directory] = array_values($add);

		}	
		ksort($items);
		return $items;
	}

	public function render ($layers) {
		$final_image = imagecreatefrompng($this->baseDirectory.'/002-base/skin001.png');
		foreach ($_POST['layers'] as $key => $value) {
			$image2 = imagecreatefrompng($value);
			imagecopy($final_image, $image2, 0, 0, 0, 0, 80, 100);
		}
		imagealphablending($final_image, false);
		imagesavealpha($final_image, true);
		header("Content-Type: image/png");
		imagepng($final_image);
		exit;
	}

}




$dynamicAvatar = new DynamicAvatar('./assets/layers/');
# Build and Output Final Image
if (isset($_POST['build'])) {
	$dynamicAvatar->render($_POST['layers'] ?? []);
}


$items = $dynamicAvatar->getWardrobe();
ksort($items);
foreach ($items as $layer => $item) {

	$label = explode('-',$layer)[1];
	$tabs[$layer] = <<<HTML
		<label for="{$layer}" class="o-tabs__label c-tabs__label">
			{$label}
		</label>
HTML;
	$content = [];
	asort($item);
	foreach ($item as $item_name) {
		$content[$item_name] = <<<HTML
	<img src="./assets/layers/{$layer}/{$item_name}" data-target = "{$layer}" class="js-item" />
HTML;

	}	
	$content = implode('', $content);



	$visual[$layer] = <<<HTML
	<img src="./assets/layers/{$layer}/{$item[0]}" id="{$layer}_item" class="layer-current"/>
	<input type="hidden" name="layers[]" id="{$layer}_input" value="./assets/layers/{$layer}/{$item[0]}" />
HTML;

	$sections[$layer] = <<<HTML
	<input type="radio" class="o-tabs__toggle" name="tabs" id="{$layer}" checked="">
        <div class="o-tabs__tab c-tabs__tab u-padding--medium">{$content}</div>
HTML;

}
$tabs = implode('', $tabs);
$sections = implode('', $sections);
$visual = implode('' ,$visual);

echo <<<HTML
<link rel="stylesheet" media="screen" href="https://toolkit.chris-shaw.com/css/toolkit.css" />

<form method="post">
<div class="display">
{$visual}
<br />
<br />
<br />
<br />
<br />
</div>
<input type="submit" name="build" value="Build" />
</form>
<div class="o-tabs c-tabs">
	{$tabs}
	{$sections}
</div>


<style>
	.display { width: 80px; marign: 200px; border: 1px solid red; }

	.layer-current {
		position: absolute;
		top: 0;
		left: 0;
	}


</style>

<script>
let items = document.querySelectorAll('.js-item');


items.forEach(function(e) {
    e.addEventListener("click", function() {
	let target  = e.getAttribute('data-target'); 
    	document.getElementById(target+'_item').src = e.src;
    	document.getElementById(target+'_input').value = e.src;
    });
});


</script>
HTML;
