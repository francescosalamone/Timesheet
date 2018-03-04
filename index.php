<?php
// include configuration files
require_once(dirname(__FILE__) . '/backend/conf/conf.php');
require_once(dirname(__FILE__) . '/backend/language/english.php');
$dbConnection = new DbConnection();

$html = file_get_contents('views/index.html');
$circle_button_url = '';
$data_to_replace['{#DATA_TO_INSERT#}'] = '';
$data_to_replace['{#CIRCLE_BUTTON_ADD#}'] = '';
$data_to_replace['{#ID_START_COUNT#}'] = '';
$data_to_replace['{#IF_NEW_PROJECT#}'] = '<!--';
$data_to_replace['{#END_IF_NEW_PROJECT#}'] = '-->';
$data_to_replace['{#IF_CIRCLE_BUTTON#}'] = '<!--';
$data_to_replace['{#END_IF_CIRCLE_BUTTON#}'] = '-->';
$data_to_replace['{#IF_START_COUNT#}'] = '<!--';
$data_to_replace['{#END_IF_START_COUNT#}'] = '-->';
$data_to_replace['{#IF_TIMER_START#}'] = '<!--';
$data_to_replace['{#END_IF_TIMER_START#}'] = '-->';
$data_to_replace['{#ID_ST_COUNT#}'] = '';
$data_to_replace['{#WHILE_THERE_ARE_PROJECTS#}'] = '<!--';
$data_to_replace['{#END_WHILE_THERE_ARE_PROJECTS#}'] = '-->';
$data_to_replace['{#ID_PROJECT#}'] = '';
$data_to_replace['{#TIME_FOR_JS#}'] = '';

$queryTimerNotStopped = 'SELECT times.id, project.name, time_start FROM times INNER JOIN project ON time_end IS NULL AND times.project = project.id';
$resultTimerNotStopped = $dbConnection -> db_select($queryTimerNotStopped);
if(isset($_GET['stop_count'])){
	$id_time = $dbConnection -> db_quote($_GET['stop_count']);

    $query = 'UPDATE times SET time_end=NOW() WHERE id = '.$id_time;

    $result = $dbConnection -> db_query($query);
	$data_to_replace['{#DATA_TO_INSERT#}'] = $dbConnection -> showAll($html);
    $circle_button_url='?add_new_project';
} else if(!empty($resultTimerNotStopped)){
  	$data_to_replace['{#DATA_TO_INSERT#}'] = $resultTimerNotStopped[0]['name'];
  	$data_to_replace['{#TIME_FOR_JS#}'] = $resultTimerNotStopped[0]['time_start'];
	$data_to_replace['{#IF_TIMER_START#}'] = '';
	$data_to_replace['{#END_IF_TIMER_START#}'] = '';
	$data_to_replace['{#ID_STOP_COUNT#}'] = $resultTimerNotStopped[0]['id'];

} else {

  if(isset($_GET['show_detail'])){
      $id_project = $dbConnection -> db_quote($_GET['show_detail']);
      $data_to_replace['{#DATA_TO_INSERT#}'] = $dbConnection -> showDetail($id_project);
    $circle_button_url = '?total_project_time='. $_GET['show_detail'];
  } else if(isset($_GET['start_count'])){
      $id_project = $dbConnection -> db_quote($_GET['start_count']);

      $query = 'INSERT INTO times (project, time_start) VALUES (' .$id_project. ', NOW())';

      $id_time = $dbConnection -> db_insert($query);
      $data_to_replace['{#IF_TIMER_START#}'] = '';
      $data_to_replace['{#END_IF_TIMER_START#}'] = '';
      $data_to_replace['{#ID_STOP_COUNT#}'] = $id_time;


      //$data_to_replace['{#DATA_TO_INSERT#}'] = $id_time;

  } else if(isset($_GET['total_project_time'])){
      $id = $dbConnection -> db_quote($_GET['total_project_time']);
      $query = 'SELECT SUM(TIMESTAMPDIFF(SECOND, time_start, time_end)) AS total FROM times WHERE project = '.$id;

      $rows = $dbConnection -> db_select($query);
      if(empty($rows)){
          $data_to_replace['{#DATA_TO_INSERT#}'] = '00:00:00';

      } else {
          $data_to_replace['{#DATA_TO_INSERT#}'] = gmdate('H:i:s', $rows[0]['total']);

      }
      $data_to_replace['{#ID_START_COUNT#}'] = $_GET['total_project_time'];
      $data_to_replace['{#IF_START_COUNT#}'] = '';
      $data_to_replace['{#END_IF_START_COUNT#}'] = '';

  } else if(isset($_GET['insert_project'])){
      $name = $dbConnection -> db_quote($_POST['project_name']);

      $query = 'INSERT INTO project (name) VALUES ('.$name.')';
      $id = $dbConnection -> db_insert($query);

      if($id === 0){
          $error = $dbConnection -> db_error();
          $data_to_replace['{#DATA_TO_INSERT#}'] = DB_ERROR .' '. $error;
      } else {
          $data_to_replace['{#DATA_TO_INSERT#}'] = $name . TOTAL_TIME_WORKED . '00:00:00'. NEW_SESSION;
          $circle_button_url = '?total_project_time='. $id;
      }
  } else if(isset($_GET["add_new_project"])){
      $data_to_replace['{#DATA_TO_INSERT#}'] = PROJECT_NAME_LABEL;
      $data_to_replace['{#IF_NEW_PROJECT#}'] = '';
      $data_to_replace['{#END_IF_NEW_PROJECT#}'] = '';
  } else {
      $query = "SELECT p.name FROM project AS p";

      $rows = $dbConnection -> db_select($query);
      if($rows === false) {
          $error = $dbConnection -> db_error();
          $data_to_replace['{#DATA_TO_INSERT#}'] = DB_ERROR .' '. $error;
      } else if($rows == null){
          $data_to_replace['{#DATA_TO_INSERT#}'] = ADD_FIRST_PROJECT;
          $circle_button_url='?add_new_project';
      } else {
        $data_to_replace['{#DATA_TO_INSERT#}'] = $dbConnection ->showAll($html);

        $circle_button_url='?add_new_project';
      }
  }

  if($circle_button_url !== ""){
      $data_to_replace['{#CIRCLE_BUTTON_ADD#}'] = $circle_button_url;
      $data_to_replace['{#IF_CIRCLE_BUTTON#}'] = '';
      $data_to_replace['{#END_IF_CIRCLE_BUTTON#}'] = '';
  }
}
$html = str_replace(array_keys($data_to_replace), array_values($data_to_replace), $html);

$html = preg_replace('/<!--(.*?)-->/s', '', $html);

echo $html;

?>