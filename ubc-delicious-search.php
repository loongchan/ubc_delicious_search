<?php
/**
 * Plugin Name: UBC Delicious Search
 * Plugin URI:
 * Description: Allows you to filter and search on your Delicious account
 * Version: 0.1
 * Author: ctlt-dev, loongchan
 * Author URI: 
 * License: GPL2
 *
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class UBC_Delicious_Search {
	private $ubc_delicious_attributes;
	
	function __construct() {
		add_action('init', array($this, 'register_shortcodes' ));

		wp_register_script('ubc-delicious-search', plugin_dir_url(__FILE__).'/js/ubc_delicious_search.js', array('jquery'));
		wp_register_style('ubc-delicious-search', plugin_dir_url(__FILE__).'/css/ubc_delicious_search.css');
	}
	
	/**
	 * register_shortcode function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_shortcodes() {
		/* don't do anything if the shortcode exists already */
		$this->add_shortcode( 'ubc_delicious_results', 'ubc_delicious_results' );
		$this->add_shortcode( 'ubc_delicious_search', 'ubc_delicious_search' );
		$this->add_shortcode( 'ubc_delicious_dropdown', 'ubc_delicious_dropdown' );
	}
	
	/**
	 * has_shortcode function.
	 *
	 * @access public
	 * @param mixed $shortcode
	 * @return void
	 */
	function has_shortcode( $shortcode ) {
		global $shortcode_tags;
	
		return ( in_array( $shortcode, array_keys ($shortcode_tags ) ) ? true : false);
	}
	
	/**
	 * add_shortcode function.
	 *
	 * @access public
	 * @param mixed $shortcode
	 * @param mixed $shortcode_function
	 * @return void
	 */
	function add_shortcode( $shortcode, $shortcode_function ) {
		if( !$this->has_shortcode( $shortcode ) )
			add_shortcode( $shortcode, array( $this, $shortcode_function ) );
	}
	

	
	/**
	 * creates a search box
	 * @param unknown $atts
	 * @param string $content
	 */
	function ubc_delicious_search($atts, $content = null) {
		//enqueue script/css
		wp_enqueue_script('ubc-delicious-search');
		wp_enqueue_style('ubc-delicious-search');
		
		$this->ubc_delicious_attributes['search'] = shortcode_atts(array(
				'placeholder'	=> "Search Words",	//input placeholder 
				'submittext'	=> "Submit",		//submit button text
				'searchtitle'	=> "Search"			//search title text
		), $atts );

		$placeholder = $this->ubc_delicious_attributes['search']['placeholder'];
		$submittext = $this->ubc_delicious_attributes['search']['submittext'];
		$searchtitle = $this->ubc_delicious_attributes['search']['searchtitle'];
		ob_start();
		?>
		<div class="ubc-delicious-search-area">
			<label class="ubc-delicious-search-title"><?php echo $searchtitle;?></label>
			<input type="text" id="ubc-delicious-search-term" name="ubc-delicious-search-term" placeholder="<?php echo $placeholder;?>">
			<?php
				if (!is_null($content)) {
					do_shortcode($content);
				}
			?>		
			<input type="submit" id="ubc-delicious-submit">
		</div> <!-- end of ubc-delicious-search-area -->
		<?php 
		return ob_get_clean();
	}
	
	/**
	 * creates options for the dropdown.
	 * 
	 * eg1: [ubc_delicious_dropdown optionlist="value::label, value2"
	 * @param unknown $atts
	 * @param string $content
	 * @return string
	 */
	function ubc_delicious_dropdown($atts, $content = null) {
		//enqueue script/css
		wp_enqueue_script('ubc-delicious-search');
		wp_enqueue_style('ubc-delicious-search');

		$this->ubc_delicious_attributes['dropdown'] = shortcode_atts(array(
			'useshowall' => 'Show All',		//if false or empty, then don't use show all option, else show the text
			'optionslist' => '',				//list of options
			'defaultoption' => '',			//selected VALUE of option to make default
			'optiontitle' => ''				//label for the dropdown
		), $atts);

		//figure out and create options for the select
		if (!empty($this->ubc_delicious_attributes['dropdown']['optionslist'])) {
			$raw_parsed = explode(',', $this->ubc_delicious_attributes['dropdown']['optionslist']);
			$dropdown_options = '';

			//first see if "Show All" is wanted
			$trimmed_useshowall = trim($this->ubc_delicious_attributes['dropdown']['useshowall']);
			if ($this->ubc_delicious_attributes['dropdown']['useshowall'] != 'false' && $trimmed_useshowall != "") {
				$dropdown_options .= '<option value="Show All">'.esc_html($trimmed_useshowall).'</option>';
			}
			
			//add rest of options
			foreach ($raw_parsed as $single_option) {
				$dropdown_raw = explode('::', $single_option);
				if ($dropdown_raw === false) {
					continue;
				} else if (count($dropdown_raw) == 1) {
					$dropdown_raw[] = $dropdown_raw[0];
				}
			
				//if option value matches optiontitle, then make it default
				$is_selected = trim($dropdown_raw[0]) == trim($this->ubc_delicious_attributes['dropdown']['defaultoption']);
				$dropdown_options .= '<option '.($is_selected? 'selected="selected"':'').' value="'.esc_attr(trim($dropdown_raw[0])).'">'.esc_html(trim($dropdown_raw[1])).'</option>';
			}

			//output dropdown
			?>
			<div class="ubc-delicious-dropdown-area">
				<label class="ubc-delicious-dropdown-label"><?php echo esc_html($this->ubc_delicious_attributes['dropdown']['optiontitle']);?></label>
				<select class="ubc-delicious-dropdown">
					<?php echo $dropdown_options;?>
				</select>
			</div>
			<?php 
		}		

		return "";
	}
	
	function ubc_delicious_results($atts, $content = null) {
		$this->ubc_delicious_attributes['result'] = shortcode_atts(array(
			'limit' => 20,
			'defaulttag' => '',
			'defaultuser' => 'eubc'
		), $atts);
		
		echo	'<div class="ubc_delicious_results resource_listings" '.
				'data-defaulttag="'.esc_attr($this->ubc_delicious_attributes['result']['defaulttag']).'" '.
				'data-user="'.esc_attr($this->ubc_delicious_attributes['result']['defaultuser']).'" '.
				'data-limit="'.esc_attr($this->ubc_delicious_attributes['result']['limit']).'"></div>';
	}
}

$UBCDelicious = new UBC_Delicious_Search();
