<?php
if (isset($_POST['capt'])) {
	update_option('os_JQ_capt', $_POST['capt']);
	$i = 1;
}
if (isset($_POST['comm'])) {
	update_option('os_JQ_jump', $_POST['comm']);
	$i = 1;
}
if (isset($_POST['meta'])) {
	update_option('os_JQ_meta', $_POST['meta']);
	$i = 1;
}
if (isset($_POST['ui-s'])) {
	update_option('os_JQ_style', $_POST['ui-s']);
	$i = 1;
}
?>
<style type="text/css">
	.tglogo{background-image:url("<?php echo plugins_url('img/logo.png',__FILE__);?>");background-repeat:no-repeat;padding-left:45px}.a{width:200px;display:inline-block;font-weight:bolder}.a small{font-size:8px;font-weight:normal;display:block}.b input{margin-left:80px}
</style>

<h2 class="tglogo">Настройка</h2>
<form method="post" action="">

<fieldset>
	<label class="a" for="meta" title="Данные из какого поля показывать в таблице?">Мета поле</label><input type="text" value="<?php echo get_option('os_JQ_meta');?>" id="meta" name="meta" /><br />
	<label class="a" for="capt" title="Заголовок, добавляется перед таблицей">Заголовок <small>(оставить пустым, если не нужен)</small></label><input type="text" value="<?php echo get_option('os_JQ_capt');?>" id="capt" name="capt" /><br />
	<label class="a" for="comm" title="ID элемента для перехода по ссылке. Зависит от темы. По умолчанию #comments">Анкор ссылки <small>(оставить пустым, если не нужен)</small></label><input type="text" value="<?php echo get_option('os_JQ_jump');?>" id="comm" name="comm" /><br />
</fieldset>
	
<fieldset class="b">
<label class="a">Стиль:</label><br />
<input id="ui-s1" type="radio" value="flick" name="ui-s" <?php if ('flick' == get_option('os_JQ_style')) { ?> checked="checked"<?php } ?>><label for="ui-s1"> flick</label><br />
<input id="ui-s2" type="radio" value="le-frog" name="ui-s" <?php if ('le-frog' == get_option('os_JQ_style')) { ?> checked="checked"<?php } ?>><label for="ui-s2"> le-frog</label><br />
<input id="ui-s3" type="radio" value="overcast" name="ui-s" <?php if ('overcast' == get_option('os_JQ_style')) { ?> checked="checked"<?php } ?>><label for="ui-s3"> overcast</label><br />
<input id="ui-s4" type="radio" value="redmond" name="ui-s" <?php if ('redmond' == get_option('os_JQ_style')) { ?> checked="checked"<?php } ?>><label for="ui-s4"> redmond</label><br />
<input id="ui-s5" type="radio" value="sunny" name="ui-s" <?php if ('sunny' == get_option('os_JQ_style')) { ?> checked="checked"<?php } ?>><label for="ui-s5"> sunny</label>
</fieldset>
<br />
<input class="button-primary" type="submit" value="Сохранить" /> <?php if(isset($i)){echo 'Настройки записаны';}

 ?></form>