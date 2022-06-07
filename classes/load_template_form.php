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
 * This file contains the form for loading customcert templates.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

/**
 * The form for loading customcert templates.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class load_template_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $DB;

        $mform =& $this->_form;

        // Get the context.
        $context = $this->_customdata['context'];

        $mform->addElement('header', 'loadtemplateheader', get_string('loadtemplate', 'customcert'));

        // Display a link to the manage templates page (if user has capability 'mod/customcert:manage' in system context or on any catergory context)
        //        if ($context->contextlevel != CONTEXT_SYSTEM && $context->contextlevel != CONTEXT_COURSECAT && has_capability('mod/customcert:manage', \context_system::instance())) {
        if (has_capability('mod/customcert:manage', \context_system::instance()) || !empty(\mod_customcert\helper::get_user_manageable_contexts())) {
            $link = \html_writer::link(new \moodle_url('/mod/customcert/manage_templates.php'),
                get_string('managetemplates', 'customcert'));
            $mform->addElement('static', 'managetemplates', '', $link);
        }

        //Select all templates in a context above this module
        $parentcontextids = $context->get_parent_context_ids();
        $sqlin = $DB->get_in_or_equal($parentcontextids);
        $sql = 'SELECT cct.id, cct.name FROM {customcert_templates} cct 
                    INNER JOIN {context} co ON co.id = cct.contextid 
                    WHERE co.id '.$sqlin[0].'
                    ORDER BY cct.name ASC;';
        $arrtemplates = $DB->get_records_sql_menu($sql, $sqlin[1]);
        
        if ($arrtemplates) {
            $templates = [];
            foreach ($arrtemplates as $key => $template) {
                $templates[$key] = format_string($template, true, ['context' => $context]);
            }
            $group = array();
            $group[] = $mform->createElement('select', 'ltid', '', $templates);
            $group[] = $mform->createElement('submit', 'loadtemplatesubmit', get_string('load', 'customcert'));
            $mform->addElement('group', 'loadtemplategroup', '', $group, '', false);
            $mform->setType('ltid', PARAM_INT);
        } else {
            $msg = \html_writer::tag('div', get_string('notemplates', 'customcert'), array('class' => 'alert'));
            $mform->addElement('static', 'notemplates', '', $msg);
        }
    }
}
