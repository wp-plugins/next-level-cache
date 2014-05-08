<?php
/** 
 * ################################################################################
 * ADMIN/SETTINGS UI
 * ################################################################################
 */

// hook the plugin menu link into the admin menu
add_action('admin_menu', 'next_level_cache_create_menu');

/**
 * Fired on "installed plugins" page 
 */
function next_level_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=next-level-cache/settings.php">Settings</a>';
  	array_push( $links, $settings_link );
  	return $links;
}

/**
 * Create the menu link to the plugin settings page and hook into admin_init
 */
function next_level_cache_create_menu() 
{

	//create new top-level menu
	add_options_page('Next Level Cache Plugin Settings', 'Next Level Cache', 'administrator', __FILE__, 'next_level_cache_settings_page');

	//call register settings function
	add_action( 'admin_init', 'next_level_cache_register_settings' );
}

/**
 * Registers all of the plugin settings on admin_init
 */
function next_level_cache_register_settings() 
{
	//register our settings
	register_setting( 'next-level-cache-settings-group', 'next_level_cache_is_enabled' );
}

/**
 * Render the settings page
 * @author (logo image) http://rebloggy.com/post/lips-denim-lipstick-gold-asap-rocky-gold-teeth-grillz-grill-goldie-itssofire-gol/32332639139
 */
function next_level_cache_settings_page() 
{
	global $wpdb;
	$db_driver_class = get_class($wpdb);
	$db_driver_is_loaded = 'next_level_cache_wpdb' == $db_driver_class;
	$driver_version = ($db_driver_is_loaded) 
		? next_level_cache_wpdb::$DRIVER_VERSION 
		: '';
?>
<style>

	#next_level_cache_header {
		border: solid 2px #ccc;
		margin: 12px 2px 8px 2px;
		padding: 20px;
		background-color: #f2f2f2;
		-moz-border-radius: 5px;
		border-radius: 5px;
		color: #666;
	}
	
	#next_level_cache_header h4 {
		margin: 0px 0px 0px 0px;
	}
	
	#next_level_cache_header tr {
		vertical-align: top;
	}
	
	.next_level_cache_section_header {
		border: solid 1px #c6c6c6;
		margin: 12px 2px 8px 2px;
		padding: 20px;
		background-color: #e1e1e1;
	}
	
	.banner-image {
	}
	
	.banner-image img {
		width: 100%;
	}
	
</style>
	
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2>Next Level Cache Is All Up In Your Grill</h2>
	
	<div class="banner-image"><img alt="Next Level Cache" src="<?php echo NEXT_LEVEL_CACHE_URL_ROOT; ?>images/logo.gif" /></div>
	
	<div id="next_level_cache_header">
		
		<h2>What The What?</h2>
		
		<?php 

		if ($driver_version) {
			?>
			<div style='margin: 10px 0px 10px 0px; padding: 25px; background-color: #99CC99; border-radius: 5px;'>
			NLC DB Drop-in version <?php echo $driver_version; ?> is enabled
			</div>
			<?php 
		}
		elseif ($db_driver_class != 'wpdb') {
			?>
			<div style='margin: 10px 0px 10px 0px; padding: 25px; background-color: #FFCCCC; border-radius: 5px;'>
			<h3>WARNING: A conflicting DB Drop-in is installed.</h3>
			<p>The DB Driver '<?php  echo $db_driver_class; ?>' is installed at <code><?php echo NEXT_LEVEL_DRIVER_PATH; ?></code>
			If you wish to use Next Level Cache you will need to replace this driver file with the one located 
			at <code>wp-content/plugins/next-level-cache/db.php</code>.</p>
			</div>
			<?php
		}
		else {
			?>
			<div style='margin: 10px 0px 10px 0px; padding: 25px; background-color: #FFCCCC; border-radius: 5px;'>
			<h3>WARNING: The DB Drop-in is NOT installed.</h3>
			
			<p>To enable Next Level Cache, a special "Drop-in" file must be copied to the "wp-content"
			directory.  This Drop-in file is located at <code><?php echo NEXT_LEVEL_CACHE_URL_ROOT; ?>db.php</code>.<p>
			
			<p>Optional: Instead of copying the Drop-in file, you can create a symbolic link instead. The benefit of using a 
			symbolic link is that the Drop-in file will be updated automatically every time the plugin is updated.
			The command for creating a symbolic link (from within the wp-content directory) is: <code>ln -s plugins/next-level-cache/db.php</code>.
			</p>
			</div>
						
			<?php 
		}
		
		?>
		
		<p>Did you know that a fresh, stock WordPress install with the default
		theme and no plugins will execute over 30 database queries every single time a visitor
		views the home page?  After installing an feature-rich theme and a few basic plugins WordPress can easily run 100 or
		more queries on every single page.  This puts a enormous amount of strain on the database server.<p> 
		
		<p>Next Level Cache is a lightweight plugin that intercepts DB queries and selectively caches them.
		A special type of plugin file called a "Drop-in" is included to override Wordpress's default DB functionality.
		Every page is still generated dynamically, but WordPress is coerced into using cached data for many 
		of the DB calls. This hybrid approach doesn't eliminate all database queries, but keeps them down to
		a reasonable number (usually between 1 and 5 queries per page, depending on your theme and plugins).</p>
		
		<p>Next Level Cache can be used in combination with other caching plugins, as long as their DB caching
		feature is not enabled.</p>
		
		<?php 
		if ($db_driver_is_loaded) {
		
			$last_save = $wpdb->get_cache_info('last_saved',0);
			$last_prune = $wpdb->get_cache_info('last_pruned',0);
			$num_prunes_today = $wpdb->get_cache_info('num_prunes_today',0);
			$num_resets_today = $wpdb->get_cache_info('num_resets_today',0);
			
			$cache_size_kb = $wpdb->get_cache_size() / 1024;
			$cache_percentage = round( $cache_size_kb / next_level_cache_wpdb::$MAX_CACHE_SIZE * 100, 0);
			
			echo "<h3>Cache Contents</h3>\n";
			echo "<div>Number of Cached Items: ". $wpdb->get_cache_count()."</div>";
			echo "<div>Cache Size: ".$wpdb->get_cache_size_formatted()
				. " <div style='display: inline-block; width: 100px;background-color: #ddd; padding: 0px;'><div style='width: "
				. $cache_percentage
				. "px;background-color:#666;height:10px;'></div></div>"
				. "</div>";
			echo "<div>Last Saved: ". ($last_prune ? date('Y-m-d H:i:s',$last_save) : 'N/A') ."</div>";
			echo "<div>Last Prune: ". ($last_prune ? date('Y-m-d H:i:s',$last_prune) : 'N/A') ."</div>";
			echo "<div>Number of Resets Today: ".$num_resets_today."</div>";
			echo "<div>Number of Prunes Today: ".$num_prunes_today."</div>";
			

			
			
			if ($num_prunes_today > next_level_cache_wpdb::$PRUNE_WARNING_LIMIT) {
				echo "<div style='margin: 10px 0px 10px 0px; padding: 25px; background-color: #FFCCCC; border-radius: 5px;'>The number of prunes is high.  Next Level Cache may not be improving performance on this site</div>";
			}
			
			echo "<div><i>Updating any page or setting will clear the cache</i></div>";

			//$items = $wpdb->get_raw_cache_items();
			//echo "<textarea style='width: 100%; height: 400px;'>" . htmlspecialchars(print_r($items,1)) . "</textarea>";
			
		}
		?>
		
		<h3>Query Debugging</h3>
		
		<p>You can view all SQL queries executed by Wordpress by adding the following two lines to the top of your wp-config.php file:</p>
		
		<div><code>define('SAVEQUERIES', true);</code></div>
		<div><code>define('DEBUGQUERIES', true);</code></div>
		
		<h3>Plugin Details</h3>
		<div>Plugin Version: <?php echo NEXT_LEVEL_CACHE_VERSION; ?></div>
		<div>DB Drop-in Location: <?php echo NEXT_LEVEL_DRIVER_PATH; ?></div>
		<div>DB Drop-in Version: <?php echo $driver_version ? $driver_version : 'NOT INSTALLED'; ?></div>
		<div>Author: <a href="http://verysimple.com/">Jason Hinkle</a></div>
		
		<form method="post" action="options.php" autocomplete="off">
			<?php settings_fields( 'wpbh-settings-group' ); ?>
			<table class="form-table"> 
			</table>
		</form>
	</div>
</div>

<?php 
}