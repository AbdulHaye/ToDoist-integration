<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ToDoist
 *
 * @author Cresenttech
 */
class ToDoist {

    private $token;
    private $url;
    private $projects = array();
    private $tasks = array();
    private $team_task = 196539184;

    public function __construct() {
        $this->makeProjectArrayFromFile();
        $this->makeTaskArrayFromFile();
    }

    public function index() {

        if (!empty($_GET)) {
            $this->token = $_GET['token'];
            $this->url = $_GET['url'];
            $podio_project_id = $_GET['podio_project_id'];
            $podio_project_name = $_GET['podio_project_name'];
            $podio_task_name = $_GET['podio_task_name'];
            $podio_task_id = $_GET['podio_task_id'];
            $podio_email = $_GET['emails'];
            $assigned_by_uid = $_GET['assigned_by_uid'];
            $responsible_uid = $_GET['responsible_uid'];
            
        } elseif (!empty($_POST)) {
            $this->token = $_GET['token'];
            $this->url = $_GET['url'];
            $podio_project_id = $_GET['podio_project_id'];
            $podio_project_name = $_GET['podio_project_name'];
            $podio_task_name = $_GET['podio_task_name'];
            $podio_task_id = $_GET['podio_task_id'];
            $podio_email = $_GET['emails'];
            $assigned_by_uid = $_GET['assigned_by_uid'];
            $responsible_uid = $_GET['responsible_uid'];
        }

// checking url existance
        if (empty($this->url)) {
            $this->url = "https://todoist.com/API/v7/sync";
        }

// checking token existance
        if (!empty($this->token)) {
//do nothing
        } else {
            return;
        }

//cheking project name or id existance
        if (!empty($podio_project_id)) {
            $todoist_project_id = $this->findProject($podio_project_id);
            if (!empty($todoist_project_id) && !empty($podio_task_id)) {
                $todoist_task_id = $this->findTask($podio_task_id);
                if ($todoist_task_id != NULL) {
                    try {
                        $response = $this->updateTask($todoist_project_id, $todoist_task_id);
                    } catch (Exception $ex) {
                        print "<pre>";
                        print_r($ex);
                        print "</pre>";

                        return $ex;
                    }
                } elseif (!empty($podio_task_name)) {
                    $task_response = $this->createTask($todoist_project_id, $podio_task_name, $due_date_utc, $assigned_by_uid, $responsible_uid);
                    if (!$task_response == '37') {
                        $todoist_task_id = $this->createTask($todoist_project_id, $podio_task_name, $due_date_utc, $assigned_by_uid, $responsible_uid);
                        $this->writeNewTaskIdsInFile($podio_task_id, $todoist_task_id);
                        $obj2 = new Tasks($podio_task_id, $todoist_task_id);
                        $this->tasks[] = $obj2;
                    }
                }
            } else if (!empty($podio_project_name)) {
                try {
                    $todoist_project_id = $this->createProject($podio_project_name);
                    $obj = new Projects($podio_project_id, $todoist_project_id);
                    $this->projects[] = $obj;
                    $this->writeNewProjectIdsInFile($podio_project_id, $todoist_project_id);
                    $todoist_task_id = $this->createTask($todoist_project_id, $podio_task_name, $due_date_utc, $assigned_by_uid, $responsible_uid);
                    $this->writeNewTaskIdsInFile($podio_task_id, $todoist_task_id);
                    $obj2 = new Tasks($podio_task_id, $todoist_task_id);
                    $this->tasks[] = $obj2;
                    if (!empty($podio_email))
                        $collebrator_email = explode(",", $podio_email);

                    echo '<br> Before Sharing <br>';

                    foreach ($collebrator_email as $email) {
                        $this->shareProject($todoist_project_id, $email);
                    }
                } catch (Exception $ex) {
                    print "<pre>";
                    print_r($ex);
                    print "</pre>";

                    return $ex;
                }
            }
        } elseif (!empty($podio_task_id) || !empty($podio_task_name)) {
            $todoist_task_id = $this->createTask($this->team_task, $podio_task_name, $due_date_utc, $assigned_by_uid, $responsible_uid);
            $this->writeNewTaskIdsInFile($podio_task_id, $todoist_task_id);
            $obj2 = new Tasks($podio_task_id, $todoist_task_id);
            $this->tasks[] = $obj2;
        }
    }

    public function timeZoneConversion($region) {
        $la_time = new DateTimeZone($region);
        $datetime->setTimezone($la_time);
        return $datetime->format('Y-m-d H:i:s');
    }

    public function randomKey($length) {
        $key = '';
        $pool = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        for ($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }
        echo "Random Key Generated<br>";
        return $key;
    }

    public function key() {
        $key = $this->randomKey(8) . "-"
                . $this->randomKey(4) . "-"
                . $this->randomKey(4) . "-"
                . $this->randomKey(4) . "-"
                . $this->randomKey(12);
        echo "Key Maked<br>";
        return $key;
    }

    public function createProject($project_name) {
        $uuid = $this->key();
        $temp_id = $this->key();

        $post_data = [
            'token' => $this->token,
            'commands' =>
//              Worked For Creating tasks and Projects by this script
            '[{"type": "project_add", "temp_id": "' . $temp_id . '",'
            . ' "uuid": "' . $uuid . '", "args": {"name": "' . $project_name . '"}}]'];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        curl_close($ch);

        $todoist_project_id = $this->getProjectId($output, $uuid, $temp_id);
        echo "Project Created<br>";
        return $todoist_project_id;
    }

    public function shareProject($todoist_project_id, $collebrator_email) {
        $uuid = $this->key();
        $temp_id = $this->key();

        $post_data = [
            'token' => $this->token,
            'commands' =>
//              Worked For Creating tasks and Projects by this script
            '[{"type": "share_project", "temp_id": "' . $temp_id . '",'
            . ' "uuid": "' . $uuid . '", '
            . '"args": {"project_id": "' . $todoist_project_id . '", "email": "' . $collebrator_email . '"}}]'];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        curl_close($ch);
        echo "Project Shared<br>";
    }

    public function createTask($project_id, $task_name, $due_date_utc, $assigned_by_uid, $responsible_uid) {
        $uuid = $this->key();
        $temp_id = $this->key();

        $post_data = [
            'token' => $this->token,
            'commands' =>
//              Worked For Creating tasks and Projects by this script
            '[{"type": "item_add", "temp_id": "' . $temp_id . '",'
            . ' "uuid": "' . $uuid . '",'
            . ' "args": {"content": "' . $task_name . '", "project_id": ' . $project_id . ', '
            . '"due_date_utc": ' . $due_date_utc . ', "assigned_by_uid": ' . $assigned_by_uid . ','
            . '"responsible_uid": ' . $responsible_uid . ','
            . ' "auto_reminder": enable }}]'];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        curl_close($ch);

        $todoist_task_id = $this->getTaskId($output, $uuid, $temp_id);
        echo "Task Created<br>";
        return $todoist_task_id;
    }

    public function findProject($podio_project_id) {
        foreach ($this->projects as $value) {
            if ($value->podio_project_id == $podio_project_id) {
                echo "Project Found<br>";
                return $value->todoist_project_id;
            }
        }
        echo "Project Not Found<br>";
        return NULL;
    }

    public function findTask($podio_task_id) {
        foreach ($this->tasks as $value) {
            if ($value->podio_task_id == $podio_task_id) {
                echo "Task Found<br>";
                return $value->todoist_task_id;
            }
        }
        echo "Task Not Found<br>";
        return NULL;
    }

    public function updateTask($todoist_task_id, $podio_task_id) {
        $uuid = $this->key();
        //$temp_id = $this->key();

        $post_data = [
            'token' => $this->token,
            'commands' =>
//              Worked For Creating tasks and Projects by this script
            '[{"type": "item_update", "uuid": "' . $uuid . '",'
            . ' "args": {"id": ' . $todoist_task_id . ', "content": ' . $podio_task_id . '}}]'];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        curl_close($ch);

        $todoist_task_id = $this->getTaskId($output, $uuid, $temp_id);
        echo "Task Updated<br>";
    }

    public function getTaskId($json_obj, $uuid, $temp_id) {
        $json = json_decode($json_obj);

        foreach ($json->sync_status as $uuid_key => $mydata) {
// var_dump($mydata);
            if ($uuid_key == $uuid) {
                if ($mydata == 'ok') {
                    foreach ($json->temp_id_mapping as $temp_id_key => $val) {
                        if ($temp_id_key == $temp_id) {
                            echo "Task_Key : " . $val . '<br>';
                            return $val;
                        }
                    }
                }
            } else {

                foreach ($mydata as $key => $val) {
                    if ($key == "error_code") {
                        echo "Project Error Code : " . $val . '<br>';
                        return $val;
                    }
                }
            }
        }
    }

    public function getProjectId($json_obj, $uuid, $temp_id) {
        $json = json_decode($json_obj);

        foreach ($json->sync_status as $uuid_key => $mydata) {
// var_dump($mydata);
            if ($uuid_key == $uuid) {
                if ($mydata == 'ok') {
                    foreach ($json->temp_id_mapping as $temp_id_key => $val) {
                        if ($temp_id_key == $temp_id) {
                            echo "Project_Key : " . $val . '<br>';
                            return $val;
                        }
                    }
                }
            } else {

                foreach ($mydata as $key => $val) {
                    if ($key == "error_code") {
                        echo "Project Error Code : " . $val . '<br>';
                        return $val;
                    }
                }
            }
        }
    }

    public function makeProjectArrayFromFile() {
        try {
            if ($file = fopen("makeProjectsArray.txt", "r")) {
                while (!feof($file)) {
                    $line = fgets($file);
                    $pieces = explode(",", $line);
                    $obj = new Projects($pieces[0], $pieces[1]);
                    $this->projects[] = $obj;
                }
                fclose($file);
            }
        } catch (Exception $ex) {
            print '<pre>';
            print_r($ex);
            print '</pre>';
        }
        echo "Project array created<br>";
    }

    public function writeNewProjectIdsInFile($podio_project_id, $todoist_project_id) {
        try {
            $data = "\r\n" . $podio_project_id . ',' . $todoist_project_id;
            file_put_contents('makeProjectsArray.txt', $data, FILE_APPEND);
        } catch (Exception $ex) {
            print '<pre>';
            print_r($ex);
            print '</pre>';
        }
        echo "Project written new project in file created<br>";
    }

    public function makeTaskArrayFromFile() {
        try {
            if ($file = fopen("makeTasksArray.txt", "r")) {
                while (!feof($file)) {
                    $line = fgets($file);
                    $pieces = explode(",", $line);
                    $obj = new Tasks($pieces[0], $pieces[1]);
                    $this->tasks[] = $obj;
                }
                fclose($file);
            }
        } catch (Exception $ex) {
            print '<pre>';
            print_r($ex);
            print '</pre>';
        }
        echo "Task array created<br>";
    }

    public function writeNewTaskIdsInFile($podio_task_id, $todoist_task_id) {
        $data = "\r\n" . $podio_task_id . ',' . $todoist_task_id;
        file_put_contents('makeTasksArray.txt', $data, FILE_APPEND);

        echo "Task written new task in file created<br>";
    }

}

class Projects {

    public $podio_project_id;
    public $todoist_project_id;

    public function __construct($podio_project_id, $todoist_project_id) {
// Define your constructor here
        $this->podio_project_id = $podio_project_id;
        $this->todoist_project_id = $todoist_project_id;
    }

}

class Tasks {

    public $podio_task_id;
    public $todoist_task_id;

    public function __construct($podio_task_id, $todoist_task_id) {
// Define your constructor here
        $this->podio_task_id = $podio_task_id;
        $this->todoist_task_id = $todoist_task_id;
    }

}
