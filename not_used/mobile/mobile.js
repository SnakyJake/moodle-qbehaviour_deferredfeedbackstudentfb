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
 * support for the mdl35+ mobile app. PHP calls this from within
 * classes/output/mobile.php
 */
/* jshint esversion: 6 */
/* eslint-disable no-console */

var that = this;
var result = {
    /*
     * maybe for later use
    handleQuestion: function(question) {
        const element = that.CoreDomUtilsProvider.convertToElement(question.html);
        const matches = Array.from(element.querySelectorAll(".studentfeedback"));

        // Get the last element and check it's not in the question contents.
        let last = matches.pop();
        if(last){
            question["studentfeedbacktitle"] = last.querySelector("summary").textContent;
            question["studentfeedbacklabel"] =   last.querySelector("p").textContent;
            question["studentfeedback"] = last.querySelector("textarea");
            question["studentfeedbackformat"] = last.querySelector("input");
            let retval = that.CoreSitePluginsQuestionBehaviourComponent;
            return [retval];
        }
    },
    */
};
result;

