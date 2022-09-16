<?php

/*
 * this file's contents are included in both the encounter page as a 'quick summary' of a form, and in the medical records' reports page.
 */

/* for $GLOBALS[], ?? */
require_once(dirname(__FILE__).'/../../globals.php');
require_once($GLOBALS['srcdir'].'/api.inc');
/* for generate_display_field() */
require_once($GLOBALS['srcdir'].'/options.inc.php');

use OpenEMR\Common\Acl\AclMain;

/* The name of the function is significant and must match the folder name */
function intake_report( $pid, $encounter, $cols, $id) {
    $count = 0;
/** CHANGE THIS - name of the database table associated with this form **/
$table_name = 'form_intake';

require_once('array.php');

/* an array of all of the fields' names and their types. */
$field_names = array('presenting_issue' => 'textarea','symptoms_suicidal_thought_rating' => 'dropdown_list','symptoms_suicidal_thought_text' => 'textfield','symptoms_homicidal_thought_rating' => 'dropdown_list','symptoms_homicidal_thought_text' => 'textfield','symptoms_aggressiveness_rating' => 'dropdown_list','symptoms_aggressiveness_text' => 'textfield','symptoms_self_injurious_behavior_rating' => 'dropdown_list','symptoms_self_injurious_behavior_text' => 'textfield','symptoms_sexual_trauma_perpetrator_rating' => 'dropdown_list','symptoms_sexual_trauma_perpetrator_text' => 'textfield','symptoms_hallucinations_rating' => 'dropdown_list','symptoms_hallucinations_text' => 'textfield','symptoms_delusions_rating' => 'dropdown_list','symptoms_delusions_text' => 'textfield','symptoms_paranoia_rating' => 'dropdown_list','symptoms_paranoia_text' => 'textfield','symptoms_depression_rating' => 'dropdown_list','symptoms_depression_text' => 'textfield','symptoms_worthlessness_rating' => 'dropdown_list','symptoms_worthlessness_text' => 'textfield','symptoms_manic_thought_rating' => 'dropdown_list','symptoms_manic_thought_text' => 'textfield','symptoms_moodswings_rating' => 'dropdown_list','symptoms_moodswings_text' => 'textfield','symptoms_irritability_anger_rating' => 'dropdown_list','symptoms_irritability_anger_text' => 'textfield','symptoms_anxiety_rating' => 'dropdown_list','symptoms_anxiety_text' => 'textfield','symptoms_phobias_rating' => 'dropdown_list','symptoms_phobias_text' => 'textfield','symptoms_obsessions_compulsions_rating' => 'dropdown_list','symptoms_obsessions_compulsions_text' => 'textfield','symptoms_change_in_appetite_rating' => 'dropdown_list','symptoms_change_in_appetite_text' => 'textfield','symptoms_change_in_energy_level_rating' => 'dropdown_list','symptoms_change_in_energy_level_text' => 'textfield','symptoms_sleep_disturbance_rating' => 'dropdown_list','symptoms_sleep_disturbance_text' => 'textfield','symptoms_decreased_concentration_rating' => 'dropdown_list','symptoms_decreased_concentration_text' => 'textfield','symptoms_disorganized_disoriented_rating' => 'dropdown_list','symptoms_disorganized_disoriented_text' => 'textfield','symptoms_learning_problem_rating' => 'dropdown_list','symptoms_learning_problem_text' => 'textfield','symptoms_medical_complication_rating' => 'dropdown_list','symptoms_medical_complication_text' => 'textfield','symptoms_social_withdrawal_rating' => 'dropdown_list','symptoms_social_withdrawal_text' => 'textfield','symptoms_binges_purges_rating' => 'dropdown_list','symptoms_binges_purges_text' => 'textfield','symptoms_sexual_acting_out_rating' => 'dropdown_list','symptoms_sexual_acting_out_text' => 'textfield','symptoms_distractibility_impulsivity_rating' => 'dropdown_list','symptoms_distractibility_impulsivity_text' => 'textfield','symptoms_hyperactivity_rating' => 'dropdown_list','symptoms_hyperactivity_text' => 'textfield','symptoms_lying_maniuplative_rating' => 'dropdown_list','symptoms_lying_maniuplative_text' => 'textfield','symptoms_oppositional_behavior_rating' => 'dropdown_list','symptoms_oppositional_behavior_text' => 'textfield','symptoms_running_away_rating' => 'dropdown_list','symptoms_running_away_text' => 'textfield','symptoms_truancy_absenteeism_rating' => 'dropdown_list','symptoms_truancy_absenteeism_text' => 'textfield','symptoms_property_destruction_rating' => 'dropdown_list','symptoms_property_destruction_text' => 'textfield','symptoms_fire_setting_rating' => 'dropdown_list','symptoms_fire_setting_text' => 'textfield','symptoms_cruelty_to_animals_rating' => 'dropdown_list','symptoms_cruelty_to_animals_text' => 'textfield','symptoms_stealing_rating' => 'dropdown_list','symptoms_stealing_text' => 'textfield','symptoms_behavioral_other_issues_rating' => 'dropdown_list','symptoms_behavioral_other_issues_text' => 'textfield','substance_use' => 'checkbox_combo_list','substance_use_general_comments' => 'textbox','substance_use_client_acknowledgment' => 'checkbox_list','substance_use_supportive_environment' => 'checkbox_list','legal_probation' => 'dropdown_list','legal_parole' => 'dropdown_list','legal_court_dates' => 'date','legal_previous_arrests' => 'textfield','legal_officer' => 'textfield','legal_comments' => 'textbox','mh_inpatient_hospitalizations_location' => 'textfield','mh_inpatient_hospitalizations_dates' => 'date','mh_inpatient_hospitalizations_last_year' => 'textfield','mh_inpatient_hospitalizations_total_num' => 'textfield','mh_er_crisis_involvement_location' => 'textfield','mh_er_crisis_involvement_dates' => 'date','mh_er_crisis_involvement_last_year' => 'textfield','mh_er_crisis_involvement_total_num' => 'textfield','mh_outpatient_therapy_location' => 'textfield','mh_outpatient_therapy_dates' => 'date','mh_outpatient_therapy_last_year' => 'textfield','mh_outpatient_therapy_total_num' => 'textfield','mh_comments' => 'textbox','mh_currently_seeing' => 'checkbox_combo_list','med_hist_comments' => 'textbox','med_hist_routine_medical_care' => 'dropdown_list','med_hist_allergies' => 'dropdown_list','med_hist_allergies_comments' => 'textfield','med_hist_date' => 'date','med_hist_pregnant' => 'dropdown_list','med_hist_pregnant_comments' => 'textfield','medication_name_1' => 'textfield','medication_dosage_1' => 'textfield','medication_freq_1' => 'textfield','medication_date_started_1' => 'date','medication_side_effects_1' => 'textfield','medication_name_2' => 'textfield','medication_dosage_2' => 'textfield','medication_freq_2' => 'textfield','medication_date_started_2' => 'date','medication_side_effects_2' => 'textfield','medication_name_3' => 'textfield','medication_dosage_3' => 'textfield','medication_freq_3' => 'textfield','medication_date_started_3' => 'date','medication_side_effects_3' => 'textfield','medication_name_4' => 'textfield','medication_dosage_4' => 'textfield','medication_freq_4' => 'textfield','medication_date_started_4' => 'date','medication_side_effects_4' => 'textfield','medication_name_5' => 'textfield','medication_dosage_5' => 'textfield','medication_freq_5' => 'textfield','medication_date_started_5' => 'date','medication_side_effects_5' => 'textfield','medication_name_6' => 'textfield','medication_dosage_6' => 'textfield','medication_freq_6' => 'textfield','medication_date_started_6' => 'date','medication_side_effects_6' => 'textfield','medication_info_from' => 'checkbox_list','medication_effectiveness' => 'textbox','mental_status_physical_stature' => 'checkbox_list','mental_status_hygiene' => 'checkbox_list','mental_status_apparent_age' => 'checkbox_list','mental_status_dress_appearance' => 'checkbox_list','mental_status_posture_appearance' => 'checkbox_list','mental_status_consciousness_activity' => 'checkbox_list','mental_status_motor_activity' => 'checkbox_list','mental_status_eye_contact' => 'checkbox_list','mental_status_attitude' => 'checkbox_list','mental_status_speech_tone' => 'checkbox_list','mental_status_speech_rate' => 'checkbox_list','mental_status_speech_production' => 'checkbox_list','mental_status_speech_other' => 'checkbox_list','mental_status_mood' => 'checkbox_list','mental_status_mood_comments' => 'textfield','mental_status_affect' => 'checkbox_list','mental_status_affect_other' => 'textfield','mental_status_thought_process' => 'checkbox_list','mental_status_hallucinations' => 'checkbox_list','mental_status_other_distortions' => 'checkbox_combo_list','mental_status_delusions' => 'checkbox_list','mental_status_abnormal_other' => 'checkbox_combo_list','mental_status_orientation' => 'checkbox_list','mental_status_intelligence' => 'checkbox_list','mental_status_attention_concentration' => 'checkbox_combo_list','mental_status_memory_impaired' => 'checkbox_combo_list','mental_status_abstraction' => 'checkbox_list','mental_status_insight' => 'checkbox_list','mental_status_judgment_impaired' => 'checkbox_combo_list','family_social_history_name_1' => 'textfield','family_social_history_relation_1' => 'textfield','family_social_history_age_1' => 'textfield','family_social_history_cohabitate_1' => 'dropdown_list','family_social_history_name_2' => 'textfield','family_social_history_relation_2' => 'textfield','family_social_history_age_2' => 'textfield','family_social_history_cohabitate_2' => 'dropdown_list','family_social_history_name_3' => 'textfield','family_social_history_relation_3' => 'textfield','family_social_history_age_3' => 'textfield','family_social_history_cohabitate_3' => 'dropdown_list','family_social_history_name_4' => 'textfield','family_social_history_relation_4' => 'textfield','family_social_history_age_4' => 'textfield','family_social_history_cohabitate_4' => 'dropdown_list','family_social_history_name_5' => 'textfield','family_social_history_relation_5' => 'textfield','family_social_history_age_5' => 'textfield','family_social_history_cohabitate_5' => 'dropdown_list','family_social_history_name_6' => 'textfield','family_social_history_relation_6' => 'textfield','family_social_history_age_6' => 'textfield','family_social_history_cohabitate_6' => 'dropdown_list','family_social_history_name_7' => 'textfield','family_social_history_relation_7' => 'textfield','family_social_history_age_7' => 'textfield','family_social_history_cohabitate_7' => 'textfield','family_social_history_in_relationship' => 'dropdown_list','family_social_history_previous_relationships' => 'dropdown_list','family_social_history_comments' => 'textarea','family_social_history_trauma' => 'textarea','family_social_history_mh_sa_comments' => 'textbox','family_social_history_cultural_ethnic' => 'textbox','dev_history_applicable' => 'checkbox_list','dev_history_fetal_development' => 'checkbox_combo_list','dev_history_delivery_complications' => 'checkbox_combo_list','dev_history_milestones' => 'dropdown_list','dev_history_sat_alone' => 'textfield','dev_history_first_word' => 'textfield','dev_history_bladder_training' => 'textfield','dev_history_nighttime_dryness' => 'textfield','dev_history_first_sentence' => 'textfield','dev_history_walked' => 'textfield','dev_history_bowel_training' => 'textfield','dev_history_other' => 'textarea','education_in_school' => 'dropdown_list','education_highest_grade' => 'textfield','education_school_attended' => 'textfield','education_grade_level' => 'textfield','education_academic_history' => 'textarea','education_learning_disabilities' => 'dropdown_list','education_employment_hobbies' => 'textarea','education_employment_hours_per_week' => 'dropdown_list','education_employment_type' => 'checkbox_list','education_employment_peer_relations' => 'textarea','needs_assessment_skills_ability' => 'checkbox_list','needs_resources' => 'checkbox_list','assessment_presenting_problem' => 'textarea','assessment_client_attitude' => 'dropdown_list','assessment_diagnosis_1' => 'textfield','assessment_diagnosis_1_comments' => 'textfield','assessment_diagnosis_2' => 'textfield','assessment_diagnosis_2_comments' => 'textfield','assessment_diagnosis_3' => 'textfield','assessment_diagnosis_3_comments' => 'textfield','assessment_diagnosis_4' => 'textfield','assessment_diagnosis_4_comments' => 'textfield','assessment_diagnosis_5' => 'textfield','assessment_diagnosis_5_comments' => 'textfield','assessment_family_housing' => 'textfield','assessment_family_housing_z_code' => 'textfield','assessment_educational_work' => 'textfield','assessment_educational_z_code' => 'textfield','assessment_economic_legal' => 'textfield','assessment_economic_legal_z_code' => 'textfield','assessment_cultural_environmental' => 'textfield','assessment_cultural_environmental_z_code' => 'textfield','assessment_personal' => 'textfield','assessment_personal_z_code' => 'textfield','assessment_gaf' => 'textfield','assessment_disability_assessment_schedule' => 'textfield','assessment_factors_comments' => 'textarea','assessment_recommended_treatment_modalities' => 'checkbox_list','assessment_recommended_treatment_other' => 'textarea','assessment_recommended_treatment_comments' => 'textarea');/* in order to use the layout engine's draw functions, we need a fake table of layout data. */

/* an array of the lists the fields may draw on. */
$lists = array();
    $data = formFetch($table_name, $id);
    if ($data) {

        if (isset($GLOBALS['PATIENT_REPORT_ACTIVE']) && ! empty($_POST['pdf'])) { // PDF Print
            $td_style = "<td style='width:24%'><span class='bold'>";
            echo '<table style="width:775px;"><tr>';
        } elseif (isset($GLOBALS['PATIENT_REPORT_ACTIVE']) && empty($_POST['pdf'])) { // Patient report view/search and printable
            $cols = 4;
            $td_style = "<td><span class='bold'>";
            echo '<table style="width:775px;"><tr>';
        } else { // Okay an encounter view.
            $td_style = "<td><span class='bold'>";
            echo '<table><tr>';
        }

        foreach($data as $key => $value) {

            if ($key == 'id' || $key == 'pid' || $key == 'user' ||
                $key == 'groupname' || $key == 'authorized' ||
                $key == 'activity' || $key == 'date' ||
                $value == '' || $value == '0000-00-00 00:00:00' ||
                $value == 'n')
            {
                /* skip built-in fields and "blank data". */
	        continue;
            }

            /* display 'yes' instead of 'on'. */
            if ($value == 'on') {
                $value = 'yes';
            }

            /* remove the time-of-day from the 'date' fields. */
            #if ($field_names[$key] == 'date')
            if ($value != '') {
              $dateparts = explode(' ', $value);
              $value = $dateparts[0];
            }

	    echo $td_style;


            if ($key == 'presenting_issue' )
            {
                echo xl_layout_label('Presenting Issue/Chief Complaint').":";
            }

            if ($key == 'symptoms_suicidal_thought_rating' )
            {
                echo xl_layout_label('Suicidal Thought/Behavior Rating').":";
            }

            if ($key == 'symptoms_suicidal_thought_text' )
            {
                echo xl_layout_label('Suicidal Thought/Behavior').":";
            }

            if ($key == 'symptoms_homicidal_thought_rating' )
            {
                echo xl_layout_label('Homicial Thought/Behavior Rating').":";
            }

            if ($key == 'symptoms_homicidal_thought_text' )
            {
                echo xl_layout_label('Homicial Thought/Behavior').":";
            }

            if ($key == 'symptoms_aggressiveness_rating' )
            {
                echo xl_layout_label('Aggressiveness Rating').":";
            }

            if ($key == 'symptoms_aggressiveness_text' )
            {
                echo xl_layout_label('Aggressiveness').":";
            }

            if ($key == 'symptoms_self_injurious_behavior_rating' )
            {
                echo xl_layout_label('Self-Injurious Behavior Rating').":";
            }

            if ($key == 'symptoms_self_injurious_behavior_text' )
            {
                echo xl_layout_label('Self-Injurious Behavior').":";
            }

            if ($key == 'symptoms_sexual_trauma_perpetrator_rating' )
            {
                echo xl_layout_label('Sexual Trauma perpetrator Rating').":";
            }

            if ($key == 'symptoms_sexual_trauma_perpetrator_text' )
            {
                echo xl_layout_label('Sexual Trauma perpetrator').":";
            }

            if ($key == 'symptoms_hallucinations_rating' )
            {
                echo xl_layout_label('Hallucinations Rating').":";
            }

            if ($key == 'symptoms_hallucinations_text' )
            {
                echo xl_layout_label('Hallucinations').":";
            }

            if ($key == 'symptoms_delusions_rating' )
            {
                echo xl_layout_label('Delusions Rating').":";
            }

            if ($key == 'symptoms_delusions_text' )
            {
                echo xl_layout_label('Delusions').":";
            }

            if ($key == 'symptoms_paranoia_rating' )
            {
                echo xl_layout_label('Paranoia Rating').":";
            }

            if ($key == 'symptoms_paranoia_text' )
            {
                echo xl_layout_label('Paranoia').":";
            }

            if ($key == 'symptoms_depression_rating' )
            {
                echo xl_layout_label('Depressed Mood Rating').":";
            }

            if ($key == 'symptoms_depression_text' )
            {
                echo xl_layout_label('Depressed Mood').":";
            }

            if ($key == 'symptoms_worthlessness_rating' )
            {
                echo xl_layout_label('Feelings of Worthlessness Rating').":";
            }

            if ($key == 'symptoms_worthlessness_text' )
            {
                echo xl_layout_label('Feelings of Worthlessness').":";
            }

            if ($key == 'symptoms_manic_thought_rating' )
            {
                echo xl_layout_label('Manic Thought/Behavior Rating').":";
            }

            if ($key == 'symptoms_manic_thought_text' )
            {
                echo xl_layout_label('Manic Thought/Behavior').":";
            }

            if ($key == 'symptoms_moodswings_rating' )
            {
                echo xl_layout_label('Intense or Abrupt Moodswings Rating').":";
            }

            if ($key == 'symptoms_moodswings_text' )
            {
                echo xl_layout_label('Intense or Abrupt Moodswings').":";
            }

            if ($key == 'symptoms_irritability_anger_rating' )
            {
                echo xl_layout_label('Irritability/Anger Issues Rating').":";
            }

            if ($key == 'symptoms_irritability_anger_text' )
            {
                echo xl_layout_label('Irritability/Anger Issues').":";
            }

            if ($key == 'symptoms_anxiety_rating' )
            {
                echo xl_layout_label('Anxiety Rating').":";
            }

            if ($key == 'symptoms_anxiety_text' )
            {
                echo xl_layout_label('Anxiety').":";
            }

            if ($key == 'symptoms_phobias_rating' )
            {
                echo xl_layout_label('Phobias Rating').":";
            }

            if ($key == 'symptoms_phobias_text' )
            {
                echo xl_layout_label('Phobias').":";
            }

            if ($key == 'symptoms_obsessions_compulsions_rating' )
            {
                echo xl_layout_label('Obsessions/Compulsions Rating').":";
            }

            if ($key == 'symptoms_obsessions_compulsions_text' )
            {
                echo xl_layout_label('Obsessions/Compulsions').":";
            }

            if ($key == 'symptoms_change_in_appetite_rating' )
            {
                echo xl_layout_label('Change in Appetite Rating').":";
            }

            if ($key == 'symptoms_change_in_appetite_text' )
            {
                echo xl_layout_label('Change in Appetite').":";
            }

            if ($key == 'symptoms_change_in_energy_level_rating' )
            {
                echo xl_layout_label('Change in Energy Level Rating').":";
            }

            if ($key == 'symptoms_change_in_energy_level_text' )
            {
                echo xl_layout_label('Change in Energy Level').":";
            }

            if ($key == 'symptoms_sleep_disturbance_rating' )
            {
                echo xl_layout_label('Sleep Disturbance Rating').":";
            }

            if ($key == 'symptoms_sleep_disturbance_text' )
            {
                echo xl_layout_label('Sleep Disturbance').":";
            }

            if ($key == 'symptoms_decreased_concentration_rating' )
            {
                echo xl_layout_label('Decreased Concentration Rating').":";
            }

            if ($key == 'symptoms_decreased_concentration_text' )
            {
                echo xl_layout_label('Decreased Concentration').":";
            }

            if ($key == 'symptoms_disorganized_disoriented_rating' )
            {
                echo xl_layout_label('Disorganized/disoriented Rating').":";
            }

            if ($key == 'symptoms_disorganized_disoriented_text' )
            {
                echo xl_layout_label('Disorganized/disoriented').":";
            }

            if ($key == 'symptoms_learning_problem_rating' )
            {
                echo xl_layout_label('Learning Problem Rating').":";
            }

            if ($key == 'symptoms_learning_problem_text' )
            {
                echo xl_layout_label('Learning Problem').":";
            }

            if ($key == 'symptoms_medical_complication_rating' )
            {
                echo xl_layout_label('Medical Complication/Pain Rating').":";
            }

            if ($key == 'symptoms_medical_complication_text' )
            {
                echo xl_layout_label('Medical Complication/Pain').":";
            }

            if ($key == 'symptoms_social_withdrawal_rating' )
            {
                echo xl_layout_label('Social Withdrawal Rating').":";
            }

            if ($key == 'symptoms_social_withdrawal_text' )
            {
                echo xl_layout_label('Social Withdrawal').":";
            }

            if ($key == 'symptoms_binges_purges_rating' )
            {
                echo xl_layout_label('Binges/Purges Rating').":";
            }

            if ($key == 'symptoms_binges_purges_text' )
            {
                echo xl_layout_label('Binges/Purges').":";
            }

            if ($key == 'symptoms_sexual_acting_out_rating' )
            {
                echo xl_layout_label('Sexual Acting Out / Promiscuity Rating').":";
            }

            if ($key == 'symptoms_sexual_acting_out_text' )
            {
                echo xl_layout_label('Sexual Acting Out / Promiscuity').":";
            }

            if ($key == 'symptoms_distractibility_impulsivity_rating' )
            {
                echo xl_layout_label('Distractibility/Impulsivity Rating').":";
            }

            if ($key == 'symptoms_distractibility_impulsivity_text' )
            {
                echo xl_layout_label('Distractibility/Impulsivity').":";
            }

            if ($key == 'symptoms_hyperactivity_rating' )
            {
                echo xl_layout_label('Hyperactivity Rating').":";
            }

            if ($key == 'symptoms_hyperactivity_text' )
            {
                echo xl_layout_label('Hyperactivity').":";
            }

            if ($key == 'symptoms_lying_maniuplative_rating' )
            {
                echo xl_layout_label('Lying/Manipulative Rating').":";
            }

            if ($key == 'symptoms_lying_maniuplative_text' )
            {
                echo xl_layout_label('Lying/Manipulative').":";
            }

            if ($key == 'symptoms_oppositional_behavior_rating' )
            {
                echo xl_layout_label('Oppositional Behavior Rating').":";
            }

            if ($key == 'symptoms_oppositional_behavior_text' )
            {
                echo xl_layout_label('Oppositional Behavior').":";
            }

            if ($key == 'symptoms_running_away_rating' )
            {
                echo xl_layout_label('Running Away Rating').":";
            }

            if ($key == 'symptoms_running_away_text' )
            {
                echo xl_layout_label('Running Away').":";
            }

            if ($key == 'symptoms_truancy_absenteeism_rating' )
            {
                echo xl_layout_label('Truancy/Absenteeism Rating').":";
            }

            if ($key == 'symptoms_truancy_absenteeism_text' )
            {
                echo xl_layout_label('Truancy/Absenteeism').":";
            }

            if ($key == 'symptoms_property_destruction_rating' )
            {
                echo xl_layout_label('Property Destruction Rating').":";
            }

            if ($key == 'symptoms_property_destruction_text' )
            {
                echo xl_layout_label('Property Destruction').":";
            }

            if ($key == 'symptoms_fire_setting_rating' )
            {
                echo xl_layout_label('Fire Setting Rating').":";
            }

            if ($key == 'symptoms_fire_setting_text' )
            {
                echo xl_layout_label('Fire Setting').":";
            }

            if ($key == 'symptoms_cruelty_to_animals_rating' )
            {
                echo xl_layout_label('Cruelty to Animals Rating').":";
            }

            if ($key == 'symptoms_cruelty_to_animals_text' )
            {
                echo xl_layout_label('Cruelty to Animals').":";
            }

            if ($key == 'symptoms_stealing_rating' )
            {
                echo xl_layout_label('Stealing Rating').":";
            }

            if ($key == 'symptoms_stealing_text' )
            {
                echo xl_layout_label('Stealing').":";
            }

            if ($key == 'symptoms_gambling_rating' )
            {
                echo xl_layout_label('Gambling Rating').":";
            }

            if ($key == 'symptoms_gambling_text' )
            {
                echo xl_layout_label('Gambling').":";
            }

            if ($key == 'symptoms_behavioral_other_issues_rating' )
            {
                echo xl_layout_label('Other Issues Rating').":";
            }

            if ($key == 'symptoms_behavioral_other_issues_text' )
            {
                echo xl_layout_label('Other Issues').":";
            }

            if ($key == 'symptoms_gaming_rating' )
            {
                echo xl_layout_label('Gaming Rating').":";
            }

            if ($key == 'symptoms_gaming_text' )
            {
                echo xl_layout_label('Gaming').":";
            }

            if ($key == 'symptoms_other1_rating' )
            {
                echo xl_layout_label('Other Symptom #1 Rating').":";
            }

            if ($key == 'symptoms_other1_text' )
            {
                echo xl_layout_label('Other Symptom #1').":";
            }

            if ($key == 'symptoms_other2_rating' )
            {
                echo xl_layout_label('Other Symptom #2 Rating').":";
            }

            if ($key == 'symptoms_other2_text' )
            {
                echo xl_layout_label('Other Symptom #2').":";
            }

            if ($key == 'symptoms_other3_rating' )
            {
                echo xl_layout_label('Other Symptom #3 Rating').":";
            }

            if ($key == 'symptoms_other3_text' )
            {
                echo xl_layout_label('Other Symptom #3').":";
            }

            if ($key == 'substance_use' )
            {
                echo xl_layout_label('Substance Use').":";
            }

            if ($key == 'substance_use_general_comments' )
            {
                echo xl_layout_label('Comments').":";
            }

            if ($key == 'substance_use_client_acknowledgment' )
            {
                echo xl_layout_label('Client Acknowledgment').":";
            }

            if ($key == 'substance_use_supportive_environment' )
            {
                echo xl_layout_label('Supportive Recovery Environment').":";
            }

            if ($key == 'legal_probation' )
            {
                echo xl_layout_label('Probation').":";
            }

            if ($key == 'legal_parole' )
            {
                echo xl_layout_label('Parole (Junvenile or Adult)').":";
            }

            if ($key == 'legal_court_dates' )
            {
                echo xl_layout_label('Court Dates/Pending').":";
            }

            if ($key == 'legal_previous_arrests' )
            {
                echo xl_layout_label('Number of previous Arrests/Convictions').":";
            }

            if ($key == 'legal_officer' )
            {
                echo xl_layout_label('Name and # of Probation or Parole officer').":";
            }

            if ($key == 'legal_comments' )
            {
                echo xl_layout_label('Comments').":";
            }

            if ($key == 'mh_inpatient_hospitalizations_location' )
            {
                echo xl_layout_label('Inpatient Hospitalizations').":";
            }

            if ($key == 'mh_inpatient_hospitalizations_dates' )
            {
                echo xl_layout_label('Inpatient Hospitalizations').":";
            }

            if ($key == 'mh_inpatient_hospitalizations_last_year' )
            {
                echo xl_layout_label('Inpatient Hospitalizations').":";
            }

            if ($key == 'mh_inpatient_hospitalizations_total_num' )
            {
                echo xl_layout_label('Inpatient Hospitalizations').":";
            }

            if ($key == 'mh_er_crisis_involvement_location' )
            {
                echo xl_layout_label('ER/Crisis MH Involvement').":";
            }

            if ($key == 'mh_er_crisis_involvement_dates' )
            {
                echo xl_layout_label('ER/Crisis MH Involvement').":";
            }

            if ($key == 'mh_er_crisis_involvement_last_year' )
            {
                echo xl_layout_label('ER/Crisis MH Involvement').":";
            }

            if ($key == 'mh_er_crisis_involvement_total_num' )
            {
                echo xl_layout_label('ER/Crisis MH Involvement').":";
            }

            if ($key == 'mh_outpatient_therapy_location' )
            {
                echo xl_layout_label('Outpatient Therapy').":";
            }

            if ($key == 'mh_outpatient_therapy_dates' )
            {
                echo xl_layout_label('Outpatient Therapy').":";
            }

            if ($key == 'mh_outpatient_therapy_last_year' )
            {
                echo xl_layout_label('Outpatient Therapy').":";
            }

            if ($key == 'mh_outpatient_therapy_total_num' )
            {
                echo xl_layout_label('Outpatient Therapy').":";
            }

            if ($key == 'mh_comments' )
            {
                echo xl_layout_label('Comments').":";
            }

            if ($key == 'mh_currently_seeing' )
            {
                echo xl_layout_label('Currently seeing').":";
            }

            if ($key == 'med_hist_comments' )
            {
                echo xl_layout_label('Significant History/problems').":";
            }

            if ($key == 'med_hist_routine_medical_care' )
            {
                echo xl_layout_label('Routine medical care?').":";
            }

            if ($key == 'med_hist_allergies' )
            {
                echo xl_layout_label('Allergies?').":";
            }

            if ($key == 'med_hist_allergies_comments' )
            {
                echo xl_layout_label('Allergy Comments').":";
            }

            if ($key == 'med_hist_date' )
            {
                echo xl_layout_label('Date last seen by primary care doctor').":";
            }

            if ($key == 'med_hist_pregnant' )
            {
                echo xl_layout_label('If female, currently pregnant?').":";
            }

            if ($key == 'med_hist_pregnant_comments' )
            {
                echo xl_layout_label('If yes, history?').":";
            }

            if ($key == 'medication_name_1' )
            {
                echo xl_layout_label('Medication #1 Name').":";
            }

            if ($key == 'medication_dosage_1' )
            {
                echo xl_layout_label('Medication #1 Dosage').":";
            }

            if ($key == 'medication_freq_1' )
            {
                echo xl_layout_label('Medication #1 Frequency').":";
            }

            if ($key == 'medication_date_started_1' )
            {
                echo xl_layout_label('Medication #1 Date Started').":";
            }

            if ($key == 'medication_side_effects_1' )
            {
                echo xl_layout_label('Medication #1 Side Effects').":";
            }

            if ($key == 'medication_name_2' )
            {
                echo xl_layout_label('Medication #2 Name').":";
            }

            if ($key == 'medication_dosage_2' )
            {
                echo xl_layout_label('Medication #2 Dosage').":";
            }

            if ($key == 'medication_freq_2' )
            {
                echo xl_layout_label('Medication #2 Frequency').":";
            }

            if ($key == 'medication_date_started_2' )
            {
                echo xl_layout_label('Medication #2 Date Started').":";
            }

            if ($key == 'medication_side_effects_2' )
            {
                echo xl_layout_label('Medication #2 Side Effects').":";
            }

            if ($key == 'medication_name_3' )
            {
                echo xl_layout_label('Medication #3 Name').":";
            }

            if ($key == 'medication_dosage_3' )
            {
                echo xl_layout_label('Medication #3 Dosage').":";
            }

            if ($key == 'medication_freq_3' )
            {
                echo xl_layout_label('Medication #3 Frequency').":";
            }

            if ($key == 'medication_date_started_3' )
            {
                echo xl_layout_label('Medication #3 Date Started').":";
            }

            if ($key == 'medication_side_effects_3' )
            {
                echo xl_layout_label('Medication #3 Side Effects').":";
            }

            if ($key == 'medication_name_4' )
            {
                echo xl_layout_label('Medication #4 Name').":";
            }

            if ($key == 'medication_dosage_4' )
            {
                echo xl_layout_label('Medication #4 Dosage').":";
            }

            if ($key == 'medication_freq_4' )
            {
                echo xl_layout_label('Medication #4 Frequency').":";
            }

            if ($key == 'medication_date_started_4' )
            {
                echo xl_layout_label('Medication #4 Date Started').":";
            }

            if ($key == 'medication_side_effects_4' )
            {
                echo xl_layout_label('Medication #4 Side Effects').":";
            }

            if ($key == 'medication_name_5' )
            {
                echo xl_layout_label('Medication #5 Name').":";
            }

            if ($key == 'medication_dosage_5' )
            {
                echo xl_layout_label('Medication #5 Dosage').":";
            }

            if ($key == 'medication_freq_5' )
            {
                echo xl_layout_label('Medication #5 Frequency').":";
            }

            if ($key == 'medication_date_started_5' )
            {
                echo xl_layout_label('Medication #5 Date Started').":";
            }

            if ($key == 'medication_side_effects_5' )
            {
                echo xl_layout_label('Medication #5 Side Effects').":";
            }

            if ($key == 'medication_name_6' )
            {
                echo xl_layout_label('Medication #6 Name').":";
            }

            if ($key == 'medication_dosage_6' )
            {
                echo xl_layout_label('Medication #6 Dosage').":";
            }

            if ($key == 'medication_freq_6' )
            {
                echo xl_layout_label('Medication #6 Frequency').":";
            }

            if ($key == 'medication_date_started_6' )
            {
                echo xl_layout_label('Medication #6 Date Started').":";
            }

            if ($key == 'medication_side_effects_6' )
            {
                echo xl_layout_label('Medication #6 Side Effects').":";
            }

            if ($key == 'medication_info_from' )
            {
                echo xl_layout_label('Medication information was obtained from').":";
            }

            if ($key == 'medication_effectiveness' )
            {
                echo xl_layout_label('Medication effectiveness').":";
            }

            if ($key == 'mental_status_physical_stature' )
            {
                echo xl_layout_label('Physical Stature').":";
            }

            if ($key == 'mental_status_hygiene' )
            {
                echo xl_layout_label('Hygiene').":";
            }

            if ($key == 'mental_status_apparent_age' )
            {
                echo xl_layout_label('Apparent Age').":";
            }

            if ($key == 'mental_status_dress_appearance' )
            {
                echo xl_layout_label('Dress').":";
            }

            if ($key == 'mental_status_posture_appearance' )
            {
                echo xl_layout_label('Posture').":";
            }

            if ($key == 'mental_status_consciousness_activity' )
            {
                echo xl_layout_label('Consciousness').":";
            }

            if ($key == 'mental_status_motor_activity' )
            {
                echo xl_layout_label('Motor Activity').":";
            }

            if ($key == 'mental_status_eye_contact' )
            {
                echo xl_layout_label('Eye Contact').":";
            }

            if ($key == 'mental_status_attitude' )
            {
                echo xl_layout_label('Attitude').":";
            }

            if ($key == 'mental_status_speech_tone' )
            {
                echo xl_layout_label('Tone').":";
            }

            if ($key == 'mental_status_speech_rate' )
            {
                echo xl_layout_label('Rate').":";
            }

            if ($key == 'mental_status_speech_production' )
            {
                echo xl_layout_label('Production').":";
            }

            if ($key == 'mental_status_speech_other' )
            {
                echo xl_layout_label('Other').":";
            }

            if ($key == 'mental_status_mood' )
            {
                echo xl_layout_label('Mood').":";
            }

            if ($key == 'mental_status_mood_comments' )
            {
                echo xl_layout_label('Mood comments').":";
            }

            if ($key == 'mental_status_affect' )
            {
                echo xl_layout_label('Affect').":";
            }

            if ($key == 'mental_status_affect_other' )
            {
                echo xl_layout_label('Affect comments').":";
            }

            if ($key == 'mental_status_thought_process' )
            {
                echo xl_layout_label('Thought Process').":";
            }

            if ($key == 'mental_status_hallucinations' )
            {
                echo xl_layout_label('Hallucinations').":";
            }

            if ($key == 'mental_status_other_distortions' )
            {
                echo xl_layout_label('Other Perceptual Distortions').":";
            }

            if ($key == 'mental_status_delusions' )
            {
                echo xl_layout_label('Abnormal Thoughts Delusions').":";
            }

            if ($key == 'mental_status_abnormal_other' )
            {
                echo xl_layout_label('Other Abnormal Thoughts').":";
            }

            if ($key == 'mental_status_orientation' )
            {
                echo xl_layout_label('Executive Functioning Orientation').":";
            }

            if ($key == 'mental_status_intelligence' )
            {
                echo xl_layout_label('Intelligence').":";
            }

            if ($key == 'mental_status_attention_concentration' )
            {
                echo xl_layout_label('Attn./Concentration Impaired').":";
            }

            if ($key == 'mental_status_memory_impaired' )
            {
                echo xl_layout_label('Memory Impaired').":";
            }

            if ($key == 'mental_status_abstraction' )
            {
                echo xl_layout_label('Abstraction').":";
            }

            if ($key == 'mental_status_insight' )
            {
                echo xl_layout_label('insight').":";
            }

            if ($key == 'mental_status_judgment_impaired' )
            {
                echo xl_layout_label('Judgment Impaired').":";
            }

            if ($key == 'family_social_history_name_1' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_1' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_1' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_1' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_name_2' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_2' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_2' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_2' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_name_3' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_3' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_3' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_3' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_name_4' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_4' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_4' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_4' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_name_5' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_5' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_5' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_5' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_name_6' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_6' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_6' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_6' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_name_7' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_relation_7' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_age_7' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_cohabitate_7' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'family_social_history_in_relationship' )
            {
                echo xl_layout_label('Are you currently in a significant relationship?').":";
            }

            if ($key == 'family_social_history_previous_relationships' )
            {
                echo xl_layout_label('Previous Marriages/Significant Relationships?').":";
            }

            if ($key == 'family_social_history_comments' )
            {
                echo xl_layout_label('Social History').":";
            }

            if ($key == 'family_social_history_trauma' )
            {
                echo xl_layout_label('Trauma History').":";
            }

            if ($key == 'family_social_history_mh_sa_comments' )
            {
                echo xl_layout_label('Previous Mental Health/Substance Abuse problems for Family').":";
            }

            if ($key == 'family_social_history_cultural_ethnic' )
            {
                echo xl_layout_label('Are there cultural, ethnic, or family issues that are causing you problems or might affect your treatment?').":";
            }

            if ($key == 'dev_history_applicable' )
            {
                echo xl_layout_label('Developmental History Applicable').":";
            }

            if ($key == 'dev_history_fetal_development' )
            {
                echo xl_layout_label('Fetal Development: Substance exposure?').":";
            }

            if ($key == 'dev_history_delivery_complications' )
            {
                echo xl_layout_label('Complications with delivery?').":";
            }

            if ($key == 'dev_history_milestones' )
            {
                echo xl_layout_label('Developmental Milestones Within Normal Limits').":";
            }

            if ($key == 'dev_history_sat_alone' )
            {
                echo xl_layout_label('Sat Alone').":";
            }

            if ($key == 'dev_history_first_word' )
            {
                echo xl_layout_label('First Word').":";
            }

            if ($key == 'dev_history_bladder_training' )
            {
                echo xl_layout_label('Bladder Training').":";
            }

            if ($key == 'dev_history_nighttime_dryness' )
            {
                echo xl_layout_label('Achieved Nighttime Dryness').":";
            }

            if ($key == 'dev_history_first_sentence' )
            {
                echo xl_layout_label('1st Sentence').":";
            }

            if ($key == 'dev_history_walked' )
            {
                echo xl_layout_label('Walked').":";
            }

            if ($key == 'dev_history_bowel_training' )
            {
                echo xl_layout_label('Bowel Training').":";
            }

            if ($key == 'dev_history_other' )
            {
                echo xl_layout_label('Other Developmental History').":";
            }

            if ($key == 'education_in_school' )
            {
                echo xl_layout_label('Client currently in school?').":";
            }

            if ($key == 'education_highest_grade' )
            {
                echo xl_layout_label('If no, highest grade completed').":";
            }

            if ($key == 'education_school_attended' )
            {
                echo xl_layout_label('If yes, school').":";
            }

            if ($key == 'education_grade_level' )
            {
                echo xl_layout_label('Grade level').":";
            }

            if ($key == 'education_academic_history' )
            {
                echo xl_layout_label('Academic History').":";
            }

            if ($key == 'education_learning_disabilities' )
            {
                echo xl_layout_label('Learning Disabilities / IEP:').":";
            }

            if ($key == 'education_employment_hobbies' )
            {
                echo xl_layout_label('Employment / Extracirricular Activities').":";
            }

            if ($key == 'education_employment_hours_per_week' )
            {
                echo xl_layout_label('Employment hours per week').":";
            }

            if ($key == 'education_employment_type' )
            {
                echo xl_layout_label('Employment Type').":";
            }

            if ($key == 'education_employment_peer_relations' )
            {
                echo xl_layout_label('Coworker / Peer Relations:').":";
            }

            if ($key == 'needs_assessment_skills_ability' )
            {
                echo xl_layout_label('Skills/Ability Assessment').":";
            }

            if ($key == 'needs_resources' )
            {
                echo xl_layout_label('Resource Needs').":";
            }

            if ($key == 'assessment_presenting_problem' )
            {
                echo xl_layout_label('Presenting Problem').":";
            }

            if ($key == 'assessment_client_attitude' )
            {
                echo xl_layout_label('Clients assess attitude towards treatment').":";
            }

            if ($key == 'assessment_diagnosis_1' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_1_comments' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_2' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_2_comments' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_3' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_3_comments' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_4' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_4_comments' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_5' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_diagnosis_5_comments' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_family_housing' )
            {
                echo xl_layout_label('Family/Housing').":";
            }

            if ($key == 'assessment_family_housing_z_code' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_educational_work' )
            {
                echo xl_layout_label('Educational/Work').":";
            }

            if ($key == 'assessment_educational_z_code' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_economic_legal' )
            {
                echo xl_layout_label('Economic/Legal').":";
            }

            if ($key == 'assessment_economic_legal_z_code' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_cultural_environmental' )
            {
                echo xl_layout_label('Cultural/Environmental').":";
            }

            if ($key == 'assessment_cultural_environmental_z_code' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_personal' )
            {
                echo xl_layout_label('Personal').":";
            }

            if ($key == 'assessment_personal_z_code' )
            {
                echo xl_layout_label('hidden label').":";
            }

            if ($key == 'assessment_gaf' )
            {
                echo xl_layout_label('ASSESSMENTS GAP').":";
            }

            if ($key == 'assessment_disability_assessment_schedule' )
            {
                echo xl_layout_label('Disability Assessment Schedule').":";
            }

            if ($key == 'assessment_factors_comments' )
            {
                echo xl_layout_label('Factors affective treatment and recovery').":";
            }

            if ($key == 'assessment_recommended_treatment_modalities' )
            {
                echo xl_layout_label('Recommended treatment modalities').":";
            }

            if ($key == 'assessment_recommended_treatment_other' )
            {
                echo xl_layout_label('Other').":";
            }

            if ($key == 'assessment_recommended_treatment_comments' )
            {
                echo xl_layout_label('Treatment Recommendations').":";
            }

                echo '</span><span class=text>'.generate_display_field( $manual_layouts[$key], $value ).'</span></td>';

            $count++;
            if ($count == $cols) {
                $count = 0;
                echo '</tr><tr>' . PHP_EOL;
            }
        }
    }
    echo '</tr></table><hr>';
}
?>

