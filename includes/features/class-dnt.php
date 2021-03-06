<?php
/**
 * "Do Not Track" integration
 *
 * Handles "Do Not Track" integration.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Oemm\Plugin\Feature;

use Oemm\System\Logger;
use Oemm\System\Option;
use Oemm\Plugin\Feature\Integration;

/**
 * This class is responsible of the "Do Not Track" integration management.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class DNT extends Integration {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function initialize() {
		$this->add_dnt_stats();
	}

	/**
	 * Add a managed integration for "Do Not Track Stats" / Pierre Lannoy.
	 *
	 * @since 1.0.0
	 */
	private function add_dnt_stats() {
		$result                               = $this->get_template();
		$result['id']                         = 'do-not-track-stats';
		$result['name']                       = 'Do Not Track Stats';
		$result['url']                        = 'https://wordpress.org/plugins/do-not-track-stats/';
		$result['image']                      = OEMM_ADMIN_URL . 'medias/' . $result['id'] . '-icon-128x128.png';
		$result['backend_detection']['rule']  = 'defined';
		$result['backend_detection']['name']  = 'DO_NOT_TRACK_STATUS';
		$result['frontend_detection']['rule'] = 'defined';
		$result['frontend_detection']['name'] = 'DO_NOT_TRACK_STATUS';
		$result['execution']['rule']          = 'constant_value';
		$result['execution']['name']          = 'DO_NOT_TRACK_STATUS';
		$result['execution']['param']         = 'included-opposition';
		$result['execution']['reverted']      = true;
		$this->integrations[]                 = $result;
	}

}
