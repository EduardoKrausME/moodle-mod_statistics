<?php
// This file is part of Moodle - http://moodle.org/
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
 * lib.php
 *
 * @package   mod_statistics
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * statistics_supports
 *
 * @param string $feature
 * @return mixed
 */
function statistics_supports(string $feature): mixed {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_OTHER,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        default => null,
    };
}

/**
 * statistics_add_instance
 *
 * @param stdClass $data
 * @param moodleform_mod|null $mform
 * @return int
 * @throws dml_exception
 */
function statistics_add_instance(stdClass $data, ?moodleform_mod $mform = null): int {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = time();

    return $DB->insert_record("statistics", $data);
}

/**
 * statistics_update_instance
 *
 * @param stdClass $data
 * @param moodleform_mod|null $mform
 * @return bool
 * @throws dml_exception
 */
function statistics_update_instance(stdClass $data, ?moodleform_mod $mform = null): bool {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();

    return $DB->update_record("statistics", $data);
}

/**
 * statistics_delete_instance
 *
 * @param int $id
 * @return bool
 * @throws dml_exception
 */
function statistics_delete_instance(int $id): bool {
    global $DB;

    if (!$DB->record_exists("statistics", ["id" => $id])) {
        return false;
    }

    $DB->delete_records("statistics", ["id" => $id]);
    return true;
}
