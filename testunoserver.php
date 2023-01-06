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
 * Test that unoserver is configured correctly
 *
 * @package   fileconverter_unoserver
 * @copyright 2023 Wakey <wakey@inzsu.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');

$sendpdf = optional_param('sendpdf', 0, PARAM_BOOL);

$PAGE->set_url(new moodle_url('/files/converter/unoserver/testunoserver.php'));
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/site:config', context_system::instance());

$strheading = get_string('test_unoserver', 'fileconverter_unoserver');
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('assignmentplugins', 'mod_assign'));
$PAGE->navbar->add(get_string('feedbackplugins', 'mod_assign'));
$PAGE->navbar->add(get_string('pluginname', 'fileconverter_unoserver'),
        new moodle_url('/admin/settings.php', array('section' => 'fileconverterunoserver')));
$PAGE->navbar->add($strheading);
$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);

$converter = new \fileconverter_unoserver\converter();

if ($sendpdf) {
    require_sesskey();

    $converter->serve_test_document();
    die();
}

$result = \fileconverter_unoserver\converter::test_unoserver_path();
switch ($result->status) {
    case \fileconverter_unoserver\converter::UNOSERVERPATH_OK:
        $msg = $OUTPUT->notification(get_string('test_unoserverok', 'fileconverter_unoserver'), 'success');
        $pdflink = new moodle_url($PAGE->url, array('sendpdf' => 1, 'sesskey' => sesskey()));
        $msg .= html_writer::link($pdflink, get_string('test_unoserverdownload', 'fileconverter_unoserver'));
        $msg .= html_writer::empty_tag('br');
        break;

    default:
        $msg = $OUTPUT->notification(get_string("test_unoserver{$result->status}", 'fileconverter_unoserver'), 'warning');
        break;
}
$returl = new moodle_url('/admin/settings.php', array('section' => 'fileconverterunoserver'));
$msg .= $OUTPUT->continue_button($returl);

echo $OUTPUT->header();
echo $OUTPUT->box($msg, 'generalbox');
echo $OUTPUT->footer();
