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
 * Defines the renderer for the deferred feedback behaviour.
 *
 * @package    qbehaviour
 * @subpackage deferredfeedbackstudentfb
 * @copyright  2022 Jakob Heinemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../deferredfeedback/renderer.php');

/**
 * Renderer for outputting parts of a question belonging to the deferred
 * feedback behaviour.
 *
 * @copyright  2022 Jakob Heinemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredfeedbackstudentfb_renderer extends qbehaviour_deferredfeedback_renderer {
    public function controls(question_attempt $qa, question_display_options $options) {
        $controls = parent::controls($qa, $options);
        $studentfeedback = html_writer::div(html_writer::div($this->studentfeedback($qa, $options), 'answer'), 'ablock');
        return $controls . $studentfeedback;
    }

    /**
     * Render the studentfeedback as either a HTML editor, or read-only, as applicable.
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    protected function studentfeedback(question_attempt $qa, question_display_options $options) {
        $step = $qa->get_last_step_with_behaviour_var('studentfeedback');

        if (empty($options->readonly)) {
            $answer = $this->studentfeedback_input($qa, $step, $options->context);
        } else {
            $answer = $this->studentfeedback_read_only($step);
        }

        return $answer;
    }

    /**
     * Render the studentfeedback in read-only form.
     *
     * @param question_attempt_step $step from which to get the current studentfeedback.
     * @return string
     */
    public function studentfeedback_read_only(question_attempt_step $step) : string {
        $output = '';
        if ($step->has_behaviour_var('studentfeedback')) {
            $formatoptions = new stdClass();
            $formatoptions->para = false;
            $explanation = $step->get_behaviour_data('studentfeedback');
            $step->get_behaviour_var('studentfeedbackformat');
            if ($explanation['studentfeedback'] > '') {
                $output .= html_writer::tag('span', get_string('studentfeedback', 'qbehaviour_deferredfeedbackstudentfb'),
                 ['class' => 'studentfeedback_header']);
                $output .= html_writer::div(format_text($step->get_behaviour_var('studentfeedback'),
                    $step->get_behaviour_var('studentfeedbackformat'), $formatoptions), 'studentfeedback_readonly');
            }

        }
        return $output;
    }

/**
     * Render the studentfeedback in a HTML editor.
     * @param question_attempt $qa a question attempt.
     * @param question_attempt_step $step from which to get the current studentfeedback.
     * @param context $context
     * @return string HTML fragment.
     */
    public function studentfeedback_input(question_attempt $qa, question_attempt_step $step, context $context) :string {
        global $CFG;
        require_once($CFG->dirroot . '/repository/lib.php');

        $inputname = $qa->get_behaviour_field_name('studentfeedback');
        $studentfeedback = $step->get_behaviour_var('studentfeedback');
        $studentfeedbackformat = $step->get_behaviour_var('studentfeedbackformat');
        $id = $inputname . '_id';

        $editor = editors_get_preferred_editor($studentfeedbackformat);
        $strformats = format_text_menu();
        $formats = $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }

        $output = html_writer::start_div("studentfeedback");
        $output .= html_writer::start_tag('details',$studentfeedback?["open"=>""]:[]);
        $output .= html_writer::tag("summary", get_string('problem_with_question_header', 'qbehaviour_deferredfeedbackstudentfb'));
        $output .= html_writer::tag('p', get_string('giveyourstudentfeedback', 'qbehaviour_deferredfeedbackstudentfb'));
        $output .= html_writer::div(html_writer::tag('textarea', s($studentfeedback),['id' => $id, 'name' => $inputname, 'rows' => 4, 'style'=>"width:100%;background-color:white;", 'core-auto-rows'=>""]));
        $output .= html_writer::start_div();
        if (count($formats) == 1) {
            reset($formats);
            $output .= html_writer::empty_tag('input', array('type' => 'hidden',
                'name' => $inputname . 'format', 'value' => key($formats)));

        } else {
            $output .= html_writer::label(get_string('format'), 'menu' . $inputname . 'format', false);
            $output .= ' ';
            $output .= html_writer::select($formats, $inputname . 'format', $studentfeedbackformat, '');
        }
        $output .= html_writer::end_div();
        $output .= html_writer::end_tag('details');
        return $output;
    }
}
