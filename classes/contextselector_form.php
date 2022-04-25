<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the form for selecting a context on template management page
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');
require_once($CFG->libdir . '/formslib.php');

/**
 * The form for displayin a context-selector on template management page.
 *
 * @package    mod_customcert
 * @copyright  2022 Markus Jungbauer 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextselector_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $OUTPUT;

        $mform =& $this->_form;

        $contexts = \mod_customcert\helper::get_user_manageable_contexts();
        $mform->addElement('select', 'contextid', get_string('context', 'core_role'), $contexts);
        $mform->setType('contextid', PARAM_INT);
        
        // Add the submit buttons.
        $mform->addElement('submit', 'submitbtn', get_string('select'));
    }
}
