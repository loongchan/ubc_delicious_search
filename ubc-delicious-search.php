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
		
		$return_val = '';
		
		$this->ubc_delicious_attributes['search'] = shortcode_atts(array(
				'placeholder'	=> "Search Words",	//input placeholder 
				'submittext'	=> "Submit",		//submit button text
				'searchtitle'	=> "Search",		//search title text
				'extraclasses' => '',
				'buttonclasses' => ''
		), $atts );

		//escaping stuff
		$placeholder = esc_attr(trim($this->ubc_delicious_attributes['search']['placeholder']));
		$submittext = esc_html(trim($this->ubc_delicious_attributes['search']['submittext']));
		$searchtitle = esc_html(trim($this->ubc_delicious_attributes['search']['searchtitle']));
		$extraclasses = esc_attr(trim($this->ubc_delicious_attributes['search']['extraclasses']));
		$buttonclasses = esc_attr(trim($this->ubc_delicious_attributes['search']['buttonclasses']));
		
		ob_start();
		?>
		<div class="ubc-delicious-search-area-container">
			<div class="ubc-dellicious-search-area <?php echo $extraclasses;?>">
				<label class="ubc-delicious-search-title"><span class="ubc-delicious-label-title"><?php echo $searchtitle;?></span>
					<input type="text" id="ubc-delicious-search-term" name="ubc-delicious-search-term" placeholder="<?php echo $placeholder;?>">
				</label>
			</div>
		<?php 
			$return_val .= ob_get_clean();
			 if (!is_null($content)) {
				$return_val .= do_shortcode($content);
			}
			ob_start();
		?>
		<div class="ubc-delicious-search-submit-area">
				<button <?php echo !empty($buttonclasses) ? 'class="'.$buttonclasses.'"' : '';?> type="submit" id="ubc-delicious-submit" ><?php if (!empty($submittext)) { echo $submittext;}?></button>
			</div>
		</div><!-- end of ubc-delicious-search-area-container -->
		<?php 
		$return_val .= ob_get_clean();
		return $return_val;
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
			'optionslist' => '',			//list of options
			'defaultoption' => '',			//selected VALUE of option to make default
			'optiontitle' => '',			//label for the dropdown
			'extraclasses' => ''			//extra classes!
		), $atts);

		//escaping values 
		$optiontitle = esc_html(trim($this->ubc_delicious_attributes['dropdown']['optiontitle']));
		$extraclasses = esc_attr(trim($this->ubc_delicious_attributes['dropdown']['extraclasses']));
		
		//output for the function
		$return_val = '';

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
			ob_start();
			?>
			<div class="ubc-delicious-dropdown-area <?php echo $extraclasses;?>">
				<label class="ubc-delicious-dropdown-label"><span class="ubc-delicious-label-title"><?php echo $optiontitle;?></span>
					<select class="ubc-delicious-dropdown">
						<?php echo $dropdown_options;?>
					</select>
				</label>
			</div>
			<?php 
			$return_val = ob_get_clean();
		}		

		return $return_val;
	}
	
	/**
	 * creates the div where the results should show
	 *
	 * @access public
	 * @param array $atts 
	 * @param string $content
	 * @return string
	 *
	 *@TODO
	 * - need to give errors when leaving default user blank.  Maybe make it into settings???
	 *
	 */ 
	function ubc_delicious_results($atts, $content = null) {
		//enqueue script/css
		wp_enqueue_script('ubc-delicious-search');
		wp_enqueue_style('ubc-delicious-search');
		
		$this->ubc_delicious_attributes['result'] = shortcode_atts(array(
			'limit' => 20,
			'defaulttag' => '',
			'defaultuser' => '',
			'view' => 'list'
		), $atts);

		$results = 	'<div class="ubc_delicious_results resource_listings" '.
					'data-defaulttag="'.esc_attr($this->ubc_delicious_attributes['result']['defaulttag']).'" '.
					'data-user="'.esc_attr($this->ubc_delicious_attributes['result']['defaultuser']).'" '.
					'data-limit="'.esc_attr($this->ubc_delicious_attributes['result']['limit']).'" '.
					'data-view="'.esc_attr($this->ubc_delicious_attributes['result']['view']).
					'"></div>';
		
		return $results;
	}
}

$UBCDelicious = new UBC_Delicious_Search();
