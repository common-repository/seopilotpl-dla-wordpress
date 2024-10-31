<?php
/*
Plugin Name: SeoPilot.pl dla WordPress
Description: Zobacz jakie reklamy z platformy SeoPilot.pl są wyświetlane w Twoim serwisie
Author: Tomasz Topa
Author URI: http://tomasz.topa.pl
Version: 1.3.0
License: GPL2

	Copyright 2012  Tomasz Topa  (email : tomasz [at] topa.pl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/* 
	Actions
*/
add_action('admin_menu', 'spdwp_menu_add');


/* 
	18+ keywords
*/

$spdwp_k18 = array('seks','sex','laski','laska','anonse','cipki','nagi','fotki','pocieracz','porno','penis','dziewczyny','mucha','doznan','erotyczne','amork');


/*
	Functions
*/


if(get_option('spdwp_divide')=='yes' && get_option('spdwp_display')!='manual'){
	add_action('wp_head', 'spdwp_get_css');
}

if(get_option('spdwp_display')=='footer'){
	if(get_option('spdwp_divide')=='yes'){
		add_action('wp_footer', 'spdwp_get_ads');
	} else {
		add_action('wp_footer', 'spdwp_get_code');
	}
}

function spdwp(){
	if(get_option('spdwp_display')=='manual2'){
		if(get_option('spdwp_divide')=='yes'){
			spdwp_get_ads();
		} else {
			spdwp_get_code('full');
		}
	}
}



// Dodanie opcji do menu
function spdwp_menu_add() {
  add_menu_page('SeoPilot.pl dla WordPress', 'SeoPilot.pl dla WordPress', 'administrator', __FILE__, 'spdwp_main'); 
  add_submenu_page( __FILE__,'Konfiguracja','Konfiguracja','administrator','spdwp_config','spdwp_config');
}



// Główna funkcja
function spdwp_main() {
	if (!current_user_can('read'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
  
	if($_POST['spdwp_siteid']){
		spdwp_options_save();
	}
	spdwp_autofind_id();
  
	
	
	if(get_option('spdwp_siteid') && get_option('spdwp_display')){
		echo '<div class="wrap">
			<h2>SeoPilot.pl dla WordPress</h2>';
			echo '<div style="float:right;width:150px; margin: 20px 0 20px 20px;text-align:center;">
	<p><strong>Plugin się przydał?</strong></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TSXSRFJCTLFLJ">
<input type="image" src="https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — Płać wygodnie i bezpiecznie">
<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
</form>
</div>';
		if(spdwp_check_clientfile()){
			spdwp_get_admin_links();
		} else {
			if(spdwp_check_linksfile()){
				echo '
					<p><strong>Nie odnaleziono pliku z bazą danych SeoPilot.pl</strong>.</p>
					<p>Jeśli jest to nowa instalacja i serwis nie został jeszcze zindeksowany, <a href="'.site_url('?seopilot_test='.get_option('spdwp_siteid')).'" target="_blank">Spróbuj umieścić link testowy</a>, a następnie <a href="'.$_SERVER['REQUEST_URI'].'">odśwież tę stronę</a>. </p>
					<p>Upewnij się, że na serwerze znajduje się plik <code>'.$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.get_option('spdwp_siteid').'/'.get_option('spdwp_siteid').'.links.db</code></p>
				';
			} else {
				echo '
					<p><strong>Nie odnaleziono pliku z klientem SeoPilot.pl</strong>.</p>
					<p>Upewnij się, że na serwerze znajduje się plik <code>'.$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.get_option('spdwp_siteid').'/SeoPilotClient.php</code></p>
				';
			}
		}		
		echo '</div><!--wrap-->';
	} else {
		spdwp_config(true);
	}
  
	
}



/* Konfiguracja */

function spdwp_config($spdwp_firstrun=false) {
	if (!current_user_can('read'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
  
	spdwp_autofind_id();
	
	if($_POST['spdwp_siteid']){
		spdwp_options_save();
	}
  
	echo '<div class="wrap">';
	
	if($spdwp_firstrun){
		echo '<h2>SeoPilot.pl dla WordPress - Instalacja</h2>';
		echo '<div id="message error" class="updated fade"><p><strong>Aby kontynuować, uzupełnij kod serwisu oraz wybierz sposób wyświetlania reklam.</strong></p></div>';
	} else {
		echo '<h2>SeoPilot.pl dla WordPress - Konfiguracja</h2>';
	}
	
	echo '<div style="float:right;width:150px; margin: 20px 0 20px 20px;text-align:center;">
	<p><strong>Plugin się przydał?</strong></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TSXSRFJCTLFLJ">
<input type="image" src="https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — Płać wygodnie i bezpiecznie">
<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
</form>
</div>';
	
	echo'<form name="spdwp_config" method="post" action="">
			<h3>Identyfikator serwisu przypisany przez SeoPilot.pl:</h3>
			<p><input type="text" name="spdwp_siteid" value="'.htmlspecialchars(get_option('spdwp_siteid')).'" size="70"> </p>
			
			<h3>Sposób dodania kodu reklam:</h3>
			<p><label><input type="radio" name="spdwp_display" value="manual"'; if(!get_option('spdwp_display') || get_option('spdwp_display')=='manual'){echo 'checked="checked" '; } echo '><strong>Ręcznie - domyślny kod</strong> - domyślny kod reklam samodzielnie umieszczony w kodzie szablonu. Nie pozwala na skorzystanie z przenoszenia kodu CSS do sekcji &lt;head&gt;</label><br/>
			<label><input type="radio" name="spdwp_display" value="manual2"'; if(get_option('spdwp_display')=='manual2'){echo 'checked="checked" '; } echo '><strong>Ręcznie - specjalna funkcja</strong> - funkcja <code>&lt;?php spdwp(); ?&gt;</code> umieszczona samodzielnie w kodzie szablonu</label><br/>
			<label><input type="radio" name="spdwp_display" value="widget"'; if(get_option('spdwp_display')=='widget'){echo 'checked="checked" '; } echo '><strong>Widget</strong> - reklamy umieszczone w formie widgetu</label><br/>
			<label><input type="radio" name="spdwp_display" value="footer"'; if(get_option('spdwp_display')=='footer'){echo 'checked="checked" '; } echo '><strong>Stopka</strong> - reklamy umieszczone w stopce bloga</label></p>
			
			<h3>Przenoszenie arkusza stylów do sekcji &lt;head&gt;</h3>
			<p><label><input type="radio" name="spdwp_divide" value="no"'; if(!get_option('spdwp_divide') || get_option('spdwp_divide')=='no'){echo 'checked="checked" '; } echo '><strong>NIE</strong> -  wyświetlaj kod normalnie</label><br/>
			<label><input type="radio" name="spdwp_divide" value="yes"'; if(get_option('spdwp_divide')=='yes'){echo 'checked="checked" '; } echo '><strong>TAK</strong> - przenoś definicje arkusza stylów do sekcji &lt;head&gt;</label></p>
			
			<p><input type="submit" class="button-primary" value="Zapisz"></p>
		</form>
		<p>Nie korzystasz jeszcze z SeoPilot? <a href="http://bit.ly/seopilotpl" target="_blank">Zarejestruj się już teraz</a> i zacznij zarabiać na swojej stronie WWW!</p>
		<p>&nbsp;</p>
	';
	
	echo '</div><!--wrap-->';
	
}



function spdwp_autofind_id(){
	if(!get_option('spdwp_siteid')){
		if($spdwp_dir=opendir($_SERVER['DOCUMENT_ROOT'])){
			while (false !== ($spdwp_item = readdir($spdwp_dir))) {
				if(strlen($spdwp_item)==32 && file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$spdwp_item.'/'.$spdwp_item.'.links.db')){
					update_option('spdwp_siteid', $spdwp_item);
				}
			}
		}
	}
}

function spdwp_options_save(){
	update_option('spdwp_siteid', mysql_escape_string($_POST['spdwp_siteid']));
	update_option('spdwp_display', mysql_escape_string($_POST['spdwp_display']));
	update_option('spdwp_divide', mysql_escape_string($_POST['spdwp_divide']));
	
	echo '<div id="message" class="updated fade"><p><strong>Ustawienia zapisane.</strong></p></div>';
}

function spdwp_check_clientfile(){
	if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.get_option('spdwp_siteid').'/SeoPilotClient.php')){
		return true;
	} else { 
		return false;
	}
}

function spdwp_check_linksfile(){
	if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.get_option('spdwp_siteid').'/'.get_option('spdwp_siteid').'.links.db')){
		return true;
	} else { 
		return false;
	}
}

function spdwp_get_css(){
	spdwp_get_code('css');
}

function spdwp_get_ads(){
	spdwp_get_code('ads');
}
	
function spdwp_get_code($part='full'){	
	define('SEOPILOT_USER', get_option('spdwp_siteid'));
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.SEOPILOT_USER.'/SeoPilotClient.php');
	$o2['charset'] = get_bloginfo('charset');//kodowanie strony
	$seopilot2 = new SeoPilotClient($o2);
	unset($o2);
	$spdwp_code=explode('</style>',$seopilot2->build_links());
	switch($part){
		case 'css':
			echo $spdwp_code[0].'</style>';
		break;
		
		case 'ads':
			echo $spdwp_code[1];
		break;
		
		default: 
			echo $seopilot2->build_links();
		break;
	
	}
}

function spdwp_get_admin_links(){
	global $spdwp_k18;
	$spdwp_links=file($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.get_option('spdwp_siteid').'/'.get_option('spdwp_siteid').'.links.db');
	if(count($spdwp_links)==0){
		$spdwp_content='<p>Brak reklam wyświetlanych na stronie.</p>';
	} else {
		$spdwp_content.='<ul style="list-style-type: disc; margin-left: 2em;"><li>Liczba linków w serwisie: '.count($spdwp_links).'</li>';
		$spdwp_content2.= '
			<table id="spdwp_links" class="tablesorter">
			<thead>
				<tr>
					<th>Podstrona</th>
					<th>Wygląd reklamy</th>
					<th>URL docelowy</th>
				</tr>
			</thead>
			<tbody>
		';
		$spdwp_content18=$spdwp_content2;
		$spdwp_links18=0;
		$spdwp_pattern  = '/'.implode('|', array_map('preg_quote', $spdwp_k18)).'/i';
		foreach($spdwp_links as $spdwp_link){
			$spdwp_link_parts=explode('~',$spdwp_link);
			$spdwp_ad_text=str_replace(array('#a#','#/a#__#h#__'),array('<u>','</u><br/>'),$spdwp_link_parts[3]);
			$spdwp_links2[]=$spdwp_link_parts[2];
			$spdwp_link_parts_parts=parse_url($spdwp_link_parts[2]);
			$spdwp_links3[]=str_replace('www.','',$spdwp_link_parts_parts['host']);
			
			$spdwp_content2.='
				<tr>
					<td>
						<a target="_blank" href="'.site_url().$spdwp_link_parts[1].'">'.$spdwp_link_parts[1].'</a>
					</td>
					<td>
						'.$spdwp_ad_text.'
					</td>
					<td>
						<a target="_blank" href="'.$spdwp_link_parts[2].'">'.$spdwp_link_parts[2].'</a>
					</td>
				</tr>
			';
			
			
			if(preg_match($spdwp_pattern, $spdwp_ad_text) || preg_match($spdwp_pattern, $spdwp_link_parts_parts['host'])) {
				$spdwp_links18++;
				$spdwp_content18.='
				<tr>
					<td>
						<a target="_blank" href="'.site_url().$spdwp_link_parts[1].'">'.$spdwp_link_parts[1].'</a>
					</td>
					<td>
						'.$spdwp_ad_text.'
					</td>
					<td>
						<a target="_blank" href="'.$spdwp_link_parts[2].'">'.$spdwp_link_parts[2].'</a>
					</td>
				</tr>
				';
			}
		}
		$spdwp_links_uq=array_unique($spdwp_links2);
		$spdwp_links_uq2=array_unique($spdwp_links3);
		$spdwp_content.='<li>Liczba unikalnych linków: '.count($spdwp_links_uq).'</li>';
		$spdwp_content.='<li>Liczba unikalnych reklamodawców: '.count($spdwp_links_uq2).'</li>';
		$spdwp_content.='<li>Liczba reklam o treści erotycznej: '.$spdwp_links18.' (<a href="#spdwp18">zobacz listę</a>)</li></ul>';
		$spdwp_content18.='</tbody></table>';
		$spdwp_content2.= '
			</tbody>
		</table>
		
		<link rel="stylesheet" href="'.plugin_dir_url(__FILE__).'assets/style.css" type="text/css" media="screen" />
		<script src="'.plugin_dir_url(__FILE__).'assets/jquery.tablesorter.min.js"></script>
		<script>
			jQuery(document).ready(function() { 
				jQuery(".tablesorter").tablesorter(); 
			}); 
		</script>
		';
		
	}
	echo '<h3>Reklamy w serwisie:</h3>';
	echo $spdwp_content;
	echo '<h3>Lista reklam:</h3>';
	echo $spdwp_content2;
	echo $spdwp_content3;
	echo '<h3 id="spdwp18">Lista reklam 18+:</h3>';
	echo $spdwp_content18;
}



/* 
	Widget
*/
class spdwp_widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'spdwp_widget', // Base ID
			'SeoPilot.pl', // Name
			array( 'description' => 'Wyświetla reklamy SeoPilot.pl', ) // Args
		);
	}

 	public function form( $instance ) {
		// outputs the options form on admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}

	public function widget( $args, $instance ) {
		if(get_option('spdwp_display')=='widget'){
			if(get_option('spdwp_divide')=='yes'){
				spdwp_get_ads();
			} else {
				spdwp_get_code();
			}
		}
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("spdwp_widget");') );


?>
