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
 * view.php
 *
 * @package   mod_statistics
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$cm = get_coursemodule_from_id("statistics", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$statistics = $DB->get_record("statistics", ["id" => $cm->instance], "*", MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability("mod/statistics:view", $context);

$PAGE->set_url("/mod/statistics/view.php", ["id" => $cm->id]);
$PAGE->set_title(format_string($statistics->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->requires->strings_for_js([
    "invalidvalue",
    "needtwovalues",
    "nomode",
    "population",
    "sample",
], "mod_statistics");
$PAGE->requires->js_call_amd("mod_statistics/calculator", "init");

$templatecontext = [
    "title" => format_string($statistics->name),
    "intro" => format_module_intro("statistics", $statistics, $cm->id),
    "labeldata" => get_string("labeldata", "mod_statistics"),
    "placeholder" => get_string("placeholder", "mod_statistics"),
    "inputhelp" => get_string("inputhelp", "mod_statistics"),
    "calculationtype" => get_string("calculationtype", "mod_statistics"),
    "population" => get_string("population", "mod_statistics"),
    "sample" => get_string("sample", "mod_statistics"),
    "clear" => get_string("clear", "mod_statistics"),
    "count" => get_string("count", "mod_statistics"),
    "sum" => get_string("sum", "mod_statistics"),
    "mean" => get_string("mean", "mod_statistics"),
    "median" => get_string("median", "mod_statistics"),
    "mode" => get_string("mode", "mod_statistics"),
    "minimum" => get_string("minimum", "mod_statistics"),
    "maximum" => get_string("maximum", "mod_statistics"),
    "range" => get_string("range", "mod_statistics"),
    "variance" => get_string("variance", "mod_statistics"),
    "standarddeviation" => get_string("standarddeviation", "mod_statistics"),
    "sortedvalues" => get_string("sortedvalues", "mod_statistics"),
    "varianceexplanation" => get_string("varianceexplanation", "mod_statistics"),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_statistics/calculator", $templatecontext);
echo $OUTPUT->footer();
