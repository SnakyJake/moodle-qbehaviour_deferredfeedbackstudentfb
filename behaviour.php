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
 * Question behaviour where the student can submit questions one at a
 * time for deferred feedback.
 *
 * @package    qbehaviour
 * @subpackage deferredfeedbackstudentfb
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../deferredfeedback/behaviour.php');


/**
 * Question behaviour for deferred feedback.
 *
 * Each question has a submit button next to it which the student can use to
 * submit it. Once the qustion is submitted, it is not possible for the
 * student to change their answer any more, but the student gets full feedback
 * straight away.
 *
 * @copyright  2022 Jakob Heinemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredfeedbackstudentfb extends qbehaviour_deferredfeedback {
    /**
     * What fields are expected
     *
     * @return array
     */
    public function get_expected_data() :array {
        $studentfeedback = [];
        if ($this->qa->get_state()->is_active()) {
            $studentfeedback = [
                'studentfeedback'       => PARAM_RAW,
                'studentfeedbackformat' => PARAM_ALPHANUMEXT
            ];
        }
        $expected = parent::get_expected_data();
        $expected = $expected + $studentfeedback;

        return $expected;
    }
    /**
     * When restarting a quiz
     *
     * @return array
     */
    protected function get_our_resume_data() :array {
        $laststudentfeedback = $this->qa->get_last_behaviour_var('studentfeedback');
        if ($laststudentfeedback) {
            return array(
                '-studentfeedback'       => $laststudentfeedback,
                '-studentfeedbackformat' => $this->qa->get_last_behaviour_var('studentfeedbackformat'),
            );
        } else {
            return [];
        }
    }
    /** 
     * Work out whether the response in $pendingstep are significantly different
     * from the last set of responses we have stored.
     * @param question_attempt_step $pendingstep contains the new responses.
     * @return bool whether the new response is the same as we already have.
     */
    protected function is_same_response(question_attempt_step $pendingstep) {
        return parent::is_same_response($pendingstep) &&
                $this->qa->get_last_behaviour_var('studentfeedback') == $pendingstep->get_behaviour_var('studentfeedback') &&
                $this->qa->get_last_behaviour_var('studentfeedbackformat') == $pendingstep->get_behaviour_var('studentfeedbackformat');
    }

    /**
     * Not sure what this does
     * @todo Find out what this does
     *
     * @param question_attempt_step $step
     * @return void
     */
    public function summarise_action(question_attempt_step $step) {
        return $this->add_studentfeedback(parent::summarise_action($step), $step);
    }

    /**
     *  The main entry point for processing an action.
     *
     * @param question_attempt_pending_step $pendingstep
     * @return boolean
     */
    public function process_action(question_attempt_pending_step $pendingstep) : bool {
        $result = parent::process_action($pendingstep);

        if ($result == question_attempt::KEEP && $pendingstep->response_summary_changed()) {
            $studentfeedbackstep = $this->qa->get_last_step_with_behaviour_var('studentfeedback');
            $pendingstep->set_new_response_summary($this->add_studentfeedback(
                    $pendingstep->get_new_response_summary(), $studentfeedbackstep));
        }
        return $result;
    }
    /**
     * Add the text from the explanation/reason textarea
     *
     * @param string $text
     * @param question_attempt_step $step
     * @return string
     */
    protected function add_studentfeedback($text, question_attempt_step $step) : string {
        $studentfeedback = $step->get_behaviour_var('qbehaviour_deferredfeedback_studentfeedback');
        if (!$studentfeedback) {
            return $text;
        }

        $a = new stdClass();
        $a->response = $text;
        $a->studentfeedback = question_utils::to_plain_text($studentfeedback,
                $step->get_behaviour_var('studentfeedbackformat'), array('para' => false));
        return get_string('respondwithstudentfeedback', 'qbehaviour_deferredfeedback_studentfeedback', $a);
    }
}
