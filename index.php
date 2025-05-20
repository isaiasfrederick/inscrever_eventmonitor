<?php
require_once('../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance()); // Restrito a admins

$courseid = required_param('courseid', PARAM_INT);
$ruleid = required_param('ruleid', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

// ead.uftm.edu.br/local/inscrever_eventmonitor/index.php?ruleid=<RULEID>&courseid=<COURSE_ID>

echo $OUTPUT->header();

$students = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname, u.email');
echo "Curso: " . $courseid;
echo "Regra: " . $ruleid;
echo "Context ID: " . $context->id . "<br>";
echo "Total usuários encontrados: " . count($students) . "<br>";
echo "<br><br>";
print_object($students);

$inscritos = 0;
$naoinscritos = 0;
foreach ($students as $user) {
    try {
        \tool_monitor\subscription_manager::create_subscription($ruleid, $courseid, $cmid = 0, $user->id);
        $inscritos++;
    } catch (Exception $e) {
        $naoinscritos++;
        continue;
    }
}

echo html_writer::tag('h3', get_string('result', 'local_inscrever_eventmonitor') . ": $inscritos inscritos e $naoinscritos não-inscritos na regra $ruleid para o curso $courseid.");
echo $OUTPUT->footer();
