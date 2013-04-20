<?php
/**
 * Plugin Name: Replace WP-Version
 * Plugin URI: http://bueltge.de/wordpress-version-verschleiern-plugin/602/
 * Description: Replace the WP-version with a random string &lt; WP 2.4 and eliminate WP-version &gt; WP 2.4
 * Author: Frank Bueltge
 * Version: 1.1.2
 * License: GPLv3
 * Author URI: http://bueltge.de/
 */

/**
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( function_exists( 'add_filter' ) ) {
	add_action( 'plugins_loaded', array( 'Replace_Wp_Version', 'get_object' ) );
} else {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists('Replace_Wp_Version') ) {
	class Replace_Wp_Version {
		
		static private $classobj;
		
		static public $wpversion;
		
		/**
		 * construct
		 * 
		 * @uses add_filter
		 * @access public
		 * @since 0.0.1
		 * @return void
		 */
		public function __construct () {
			
			if ( is_admin() || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) )
				return NULL;
			
			$this -> wpversion = $GLOBALS['wp_version'];
			
			add_action( 'init',              array( $this, 'replace_wp_version' ), 2 );
			add_filter( 'script_loader_src', array( $this, 'filter_script_loader' ), 1 );
			add_filter( 'style_loader_src',  array( $this, 'filter_script_loader' ), 1 );
		}
		
		/**
		 * Handler for the action 'init'. Instantiates this class.
		 *
		 * @since   1.1.0
		 * @access  public
		 * @return  $classobj
		 */
		public function get_object () {
			
			if ( NULL === self :: $classobj )
				self :: $classobj = new self;
		
			return self :: $classobj;
		}
		
		/**
		 * return plugin comment data
		 * 
		 * @since   1.1.0
		 * @access  public
		 * @param   $value string, default = 'Version'
		 *          Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
		 * @return  string
		 */
		public function get_plugin_data ( $value = 'Version' ) {
			
			$plugin_data  = get_plugin_data( __FILE__ );
			$plugin_value = $plugin_data[$value];
			
			return $plugin_value;
		}
		
		public function replace_wp_version () {
			
			// random values
			$GLOBALS['wp_version'] = intval( rand(0, 9999) );
			$wp_db_version         = intval( rand(9999, 99999) );
			$manifest_version      = intval( rand(99999, 999999) );
			$tinymce_version       = intval( rand(999999, 9999999) );
			
			// eliminate version for wordpress >= 2.4
			if ( function_exists('the_generator') ) {
				
				remove_filter( 'wp_head', 'wp_generator' );
				$actions = array( 
					'rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 
					'comments_atom_head', 'opml_head', 'app_head'
				);
				foreach ( $actions as $action )
					remove_action( $action, 'the_generator' );
				
			} else {
				
				// for wordpress < 2.4
				add_filter( "bloginfo_rss('version')", create_function( '$a', "return $wp_version;" ) );
				
			}
		
		}
		
		public function filter_script_loader ( $src ) {
			
			return remove_query_arg( 'ver', $src );
		}
		
	} // end class
} // end if class
