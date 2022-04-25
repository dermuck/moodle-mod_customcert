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
 * Provides helper functionality.
 *
 * @package    mod_customcert
 * @copyright  2021 Mark Nelson <mdjnelson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

use core_user\fields;

defined('MOODLE_INTERNAL') || die();

/**
 * Class helper.
 *
 * Helper functionality for this module.
 *
 * @package    mod_customcert
 * @copyright  2021 Mark Nelson <mdjnelson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * A centralised location for the all name fields.
     *
     * Returns a sql string snippet.
     *
     * @param string $tableprefix table query prefix to use in front of each field.
     * @return string All name fields.
     */
    public static function get_all_user_name_fields(string $tableprefix = ''): string {
        $alternatenames = [];
        foreach (fields::get_name_fields() as $field) {
            $alternatenames[$field] = $field;
        }

        if ($tableprefix) {
            foreach ($alternatenames as $key => $altname) {
                $alternatenames[$key] = $tableprefix . '.' . $altname;
            }
        }

        return implode(',', $alternatenames);
    }
	
	 /**
     * Get an array of contexts which the current or given user should be allowed to   
     * assign certificate templates. This is the system-context (contextlevel 10)
     * and any top-level-coursecatagories (contextlevel 40 and depth 2) as far as the 
     * user is granted the capability 'mod/customcert:manage' in the specific context.
     * 
     * Returns an associative array containing [contextid] => Name of the context 
     *
     * @param int|stdClass $user  userId or user-object (null means current user) 
     * @return array(string) contexts the user my manage
     */
    public static function get_user_manageable_contexts($user = null) : array {
        global $USER, $DB;
        $return = array();
        $systemcontext = \context_system::instance();
        //fill $userid with current user or passed parameter value
        if ($user === null) {
            $userid = $USER->id;
        } else {
            $userid = is_object($user) ? $user->id : $user;
        }

        //add system-context if capability is granted
        if(has_capability('mod/customcert:manage', $systemcontext, $userid)) {
            $return[$systemcontext->id] = $systemcontext->get_context_name();
        }
        
        //add all top-level-coursecategories where capability is granted
        $cats = $DB->get_records('context', array('contextlevel' => CONTEXT_COURSECAT), null, 'id');
        foreach ($cats as $cat) {
            $context = \context::instance_by_id($cat->id);
            if(has_capability('mod/customcert:manage', $context, $userid)) {
                $return[$context->id] = $context->get_context_name();
            }
        }
        return $return;
    }
}
