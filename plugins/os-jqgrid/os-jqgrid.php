<?php
/*
Plugin Name: jqGrid
Version: 0.3
Description: Integrate jqGrid to Wordpress. Use [jqgrid] shortcode to insert. jqGrid version 4.2.0 (oct 11.11) used.
Author: dimas@odminstudios.ru
Author URI: http://odminstudios.ru
*/

/* 
 * ТЗ
 * 1. выводим все посты, без каких либо условий, на той странице где будет Шорткод (в формате для jqGrid)
 * 2. поля нужны такие же как в админке: Заголовок, Автор, Категории, Теги, Дата регистрации.
 * И для примера какое нибудь мета-поле вывести. Любое. Пусть будует что то типа: test-field
 * 3. я пока еще не знаю зачем мне админка, но давай сделаем, потом че нить туда засунем, а пока просто напишем там Hello World )
 * 
 * шорткод [jqgrid]
 */

function os_JQ_settings() {
	include_once 'os-jqgrid-settings.php'; //файл с настройками в админке
}

function jqgrid_tag() {
	include_once 'os-jqgrid-front.php';
}

function os_JQ_AdminPage() {
    add_options_page('jqGrid', 'jqGrid', 8, __FILE__, 'os_JQ_Settings');
}

function jqga() {
	global $wpdb;

	$postID = $_POST['postID'];
	$page = $_POST['page']; 
	$limit = $_POST['rows']; 
	$sidx = $_POST['sidx']; 
	$sord = $_POST['sord'];
	$search = $_POST['_search'];
	
		if(!$sidx) $sidx =1;
	
	$count=wp_count_posts('post');
	$count= $count->publish;
	
	if($count > 0 && $limit > 0) {
		$total_pages = ceil($count/$limit);
	} else {
		$total_pages = 0; 
	}
	 
	if($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; 
	if($start <0) $start = 0; 


switch ($sidx) {
	case 'cat':
			$SQL = $wpdb->get_results("SELECT * FROM $wpdb->posts
					LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
					LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
					LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
					WHERE $wpdb->posts.post_type = 'post' 
					AND $wpdb->posts.post_status = 'publish'
					AND $wpdb->term_taxonomy.taxonomy ='category'
					GROUP BY $wpdb->posts.ID
					ORDER BY $wpdb->terms.name $sord
					LIMIT $start, $limit");
		break;
	case 'tag':
			$SQL = $wpdb->get_results("SELECT * FROM $wpdb->posts
					LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
					LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
					LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
					WHERE $wpdb->posts.post_type = 'post' 
					AND $wpdb->posts.post_status = 'publish'
					AND $wpdb->term_taxonomy.taxonomy = 'post_tag'
					GROUP BY $wpdb->posts.ID
					ORDER BY $wpdb->terms.name $sord
					LIMIT $start, $limit");		
		break;
	case 'meta':
			$meta_custm=get_option('os_JQ_meta');
			$subquery="CREATE TEMPORARY TABLE os_table SELECT post_id,meta_key,meta_value FROM $wpdb->postmeta WHERE meta_key='$meta_custm' ";
			$SQL = $wpdb->query($subquery);
			$SQL = $wpdb->get_results("SELECT * FROM $wpdb->posts
					LEFT JOIN os_table ON ($wpdb->posts.id = os_table.post_id) 
					WHERE $wpdb->posts.post_status='publish'
					AND $wpdb->posts.post_type='post'
					GROUP BY $wpdb->posts.ID
					ORDER BY os_table.meta_value $sord
					LIMIT $start, $limit");
		break;
	default:
			$SQL = $wpdb->get_results("SELECT * FROM $wpdb->posts 
					WHERE post_status='publish'
					AND post_type='post'
					GROUP BY $wpdb->posts.ID
					ORDER BY $sidx $sord
					LIMIT $start, $limit");
		break;
}

	$s = "<?xml version='1.0' encoding='utf-8'?>";
	$s.=  "<rows>";
	$s.= "<page>".$page."</page>";
	$s.= "<total>".$total_pages."</total>";
	$s.= "<records>".$count."</records>";
	
	foreach($SQL as $item){
	    $s.= "<row id='".$item->ID."'>";            
		$s.= "<cell><![CDATA[<a href='".$item->guid.get_option('os_JQ_jump')."'>".$item->ID."</a>]]></cell>";
		$s.= "<cell><![CDATA[<a href='".$item->guid.get_option('os_JQ_jump')."'>".$item->post_title."</a>]]></cell>";
		$s.= "<cell>".get_the_author_meta('display_name', $item->post_author)."</cell>";
		$s.= "<cell>".get_the_title(implode(',',get_post_meta($item->ID,'initiator')))."</cell>";
		$s.= "<cell>".get_the_title(implode(',',get_post_meta($item->ID,'responsible')))."</cell>";
		$s.= "<cell>".get_post_meta($item->ID,'date_end')."</cell>";
		$s.= "<cell>".get_the_title(implode(',',get_post_meta($item->ID,'object')))."</cell>";
		
		$s.= "<cell>";
		$post_categories=wp_get_post_categories($item->ID);
		$i=0;
		if($post_categories){
			foreach($post_categories as $c){
				if($i>0){$s.= ', ';}
				$s.= get_category($c)->cat_name;
				$i++;
			};
		};
	$s.= "</cell>";
	
	   
	  /*$s.= "<cell><![CDATA[";
			$i=0;
			$post_tags = get_the_tags($item->ID,'post_tag');
			if($post_tags){
				foreach($post_tags as $tg){
					if($i>0){$s .= ', ';}
					$s .= $tg->name;
					$i++;
				};
			};
		$s.= "]]></cell>";*/
		$s.= "<cell>".implode(', ',get_post_meta($item->ID,get_option('os_JQ_meta')))."</cell>";
		$s.= "<cell></cell>";
		$s.= "<cell>".$item->post_date."</cell>";
	   $s.= "</row>";
	}
	$s .= "</rows>";
	//может конфликтовать с некоторыми плагинами\темами, закомментировать нижнюю строку
	header("Content-type: text/xml;charset=utf-8");
	echo $s;
	exit;
};


add_shortcode('jqgrid','jqgrid_tag');
add_action('admin_menu', 'os_JQ_AdminPage');
wp_enqueue_script('jqgrid',plugins_url('library/js/jquery.jqGrid.min.js',__FILE__),array('jquery'),'4.1.2',true);
wp_enqueue_script('jqgridloc',plugins_url('library/js/i18n/grid.locale-ru.js',__FILE__),array('jquery'));
add_action('wp_header','os_JQ_css');
wp_enqueue_script('jqg_ajax',plugin_dir_url(__FILE__).'js/cases.js',array('jquery'));

$caption=get_option('os_JQ_capt');

wp_localize_script('jqg_ajax','jQGajax',array('ajaxurl'=>admin_url('admin-ajax.php'),'caption'=>$caption));
add_action('wp_ajax_nopriv_jqga','jqga');
add_action('wp_ajax_jqga','jqga');
wp_enqueue_style('jqg_css', plugin_dir_url(__FILE__).'library/css/ui.jqgrid.css');
wp_enqueue_style('jq_ui_css', plugin_dir_url(__FILE__).'library/css/'.get_option('os_JQ_style').'/jquery-ui-1.8.14.custom.css');

function os_JQ_Activate() {
	add_option('os_JQ_meta','jqg_custom');
	add_option('os_JQ_jump','#comments');
	add_option('os_JQ_capt','Grid');
	add_option('os_JQ_style','redmond');
}

function os_JQ_Deactivate() {
	delete_option('os_JQ_meta');
	delete_option('os_JQ_jump');
	delete_option('os_JQ_capt');
	delete_option('os_JQ_style');
	remove_shortcode('jqgrid');
}

register_activation_hook( __FILE__, 'os_JQ_Activate');
register_deactivation_hook( __FILE__, 'os_JQ_Deactivate');
?>