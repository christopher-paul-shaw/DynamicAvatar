<?php


$iterator = new DirectoryIterator('./assets/layers');
$layer = false;
foreach ($iterator as $i) {

	if ($i->isDot() || !$i->isDir()) continue;

	$directory = $i->getFileName();
	$layerDir = new DirectoryIterator("./assets/layers/{$directory}");
	
	$items[$directory] = [];
	foreach ($layerDir as $l) {
		if ($l->isDot() || $l->isDir()) continue;
		$items[$directory][] = $l->getFileName();

	}

}


foreach ($items as $layer => $item) {

	$label = explode('-',$layer)[1];
	$tabs[$layer] = <<<HTML
		<label for="{$layer}" class="o-tabs__label c-tabs__label">
			{$label}
		</label>
HTML;
	$content = [];
	foreach ($item as $item_name) {
		$content[] = <<<HTML
	<img src="./assets/layers/{$layer}/{$item_name}" data-target = "{$layer}" class="js-item" />
HTML;

	}	
	$content = implode('', $content);

	$visual[$layer] = <<<HTML
	<img src="./assets/layers/{$layer}/{$item[0]}" class="layer-current"/>
	<input type="hidden" name="{$layer}" value="./assets/layers/{$layer}/{$item[0]}" />
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

<div class="display">
{$visual}
</div>
<br />
<br />
<br />
<br />
<br />
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
HTML;
