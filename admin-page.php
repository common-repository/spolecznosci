<?php
add_action('admin_menu', 'spolecznosci_plugin_menu');

function spolecznosci_plugin_menu() {
	add_options_page('Miniblog.pl ustawienia', 'Miniblog.pl', 'manage_options', 'spolecznosci-net-options', 'spolecznosci_plugin_options');
}

function spolecznosci_plugin_options() {
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	$title = 'Miniblog.pl ustawienia wtyczki';

	$flvvideo = get_option('spolecznosci_flvvideo', 0);
	$videodashboard = get_option('spolecznosci_videodashboard', 0);
	$excerpt = get_option('spolecznosci_excerpt', 0);

	if (!empty($_POST)) {
		$flvvideo = (!empty($_POST['flvvideo'])) ? (int)$_POST['flvvideo'] : 0;
		$videodashboard = (!empty($_POST['videodashboard'])) ? (int)$_POST['videodashboard'] : 0;
		$excerpt = (!empty($_POST['excerpt'])) ? (int)$_POST['excerpt'] : 0;

		update_option('spolecznosci_flvvideo', $flvvideo);
		update_option('spolecznosci_videodashboard', $videodashboard);
		update_option('spolecznosci_excerpt', $excerpt);


		update_option("spolecznosci_excerpt_length", $_POST["excerpt_length"]);
		update_option("spolecznosci_excerpt_moretext", $_POST['excerpt_text']);
		update_option("spolecznosci_excerpt_thumb",$_POST['excerpt_thumb']);
		if ($_POST['excerpt_rss'] == "yes") {
			update_option("spolecznosci_excerpt_rss", $_POST['excerpt_rss']);
		} else {
			update_option("spolecznosci_excerpt_rss", "no");
		}
		
	}
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
		<h2><?php echo esc_html($title); ?></h2><br />
		<p>
		<form action="" method="post">
			<table>
				<tr>
					<td>
						<label for="flvvideo">Włącz możliwośc osadzania wideo w formacie FLV</label>
					</td>
					<td>
						<input id="flvvideo" type="checkbox" value="1" name="flvvideo" <?= ($flvvideo) ? 'checked="checked"' : '' ?>/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="videodashboard">Pokazuj filmy instruktażowe w Kokpicie</label>
					</td>
					<td>
						<input id="videodashboard" type="checkbox" value="1" name="videodashboard" <?= ($videodashboard) ? 'checked="checked"' : '' ?>/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="is_excerpt">Automatyczne skracanie treści wpisów</label>
					</td>
					<td>
						<input id="is_excerpt" type="checkbox" value="1" name="excerpt" <?= ($excerpt) ? 'checked="checked"' : '' ?>/>
					</td>
				</tr>

				<tr>
					<td colspan="2"><b>Opcje skracania treści:</b></td>
				</tr>
				<tr>
					<td><label for="excerpt_length">Ilość znaków</label></td>
					<td><input id="excerpt_length" name="excerpt_length" type="text" value="<?php echo get_option("spolecznosci_excerpt_length", '500'); ?>" /></td></tr>
				<tr>
					<td><label for="excerpt_text">Etykieta odnośnika do pełnego wpisu</label></td>
					<td><input id="excerpt_text" name="excerpt_text" type="text" value="<?php echo get_option("spolecznosci_excerpt_moretext", 'Czytaj dalej'); ?>" /></td>
				</tr>
				<tr>
					<td>Dołącz ikonę do wpisu</td>
					<?php $whatthumb = get_option("spolecznosci_excerpt_thumb", 'thumbnail'); ?>
					<td><input id="excerpt_thumb_0" name="excerpt_thumb" type="radio" value="none" <?php if ($whatthumb == "none") {
						echo 'checked="checked"';
					} ?> /><label for="excerpt_thumb_0">Brak</label>&nbsp;&nbsp;&nbsp;<input id="excerpt_thumb_1" name="excerpt_thumb" type="radio" value="thumbnail" <?php if ($whatthumb == "thumbnail") {
						echo 'checked="checked"';
					} ?> /><label for="excerpt_thumb_1">Miniaturka</label>&nbsp;&nbsp;&nbsp;<input id="excerpt_thumb_2" name="excerpt_thumb" type="radio" value="medium" <?php if ($whatthumb == "medium") {
						echo 'checked="checked"';
					} ?> /><label for="excerpt_thumb_2">Śrerni rozmiar</label>&nbsp;&nbsp;&nbsp;<input id="excerpt_thumb_3" name="excerpt_thumb" type="radio" value="large" <?php if ($whatthumb == "large") {
						echo 'checked="checked"';
					} ?> /><label for="excerpt_thumb_3">Duży rozmiar</label></td>
				</tr>
				<tr><td><label for="excerpt_rss">Wyłącz w kanałach RSS</label></td>
					<?php $rss_disable = get_option("spolecznosci_excerpt_rss",'no'); ?>
					<td><input id="excerpt_rss" name="excerpt_rss" type="checkbox" value="yes" <?php if ($rss_disable == "yes") {
					echo 'checked="checked"';
					} ?> /></td>
				</tr>
			</table><br />

			<?
			submit_button();
			?>
		</form>
	</p>
	</div>
	<?php
}
