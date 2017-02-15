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
    private $team_task;

    public function __construct() {
        // Define your constructor here
    }

    public function index() {

        if (!empty($_GET)) {
            $this->token = $_GET['token'];
            $this->url = $_GET['url'];
            $podio_project_id = $_GET['podio_project_id'];
            $podio_task_name = $_GET['podio_task_name'];
            $podio_task_id = $_GET['podio_task_id'];
        } elseif (!empty($_POST)) {
            $this->token = $_GET['token'];
            $this->url = $_GET['url'];
            $podio_project_id = $_GET['podio_project_id'];
            $podio_task_name = $_GET['podio_task_name'];
            $podio_task_id = $_GET['podio_task_id'];
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
                $task_id = $this->findTask($podio_task_id);
                if ($task_id != NULL) {
                    try {
                        $response = $this->updateTask($project_id, $task_id);
                    } catch (Exception $ex) {
                        print "<pre>";
                        print_r($ex);
                        print "</pre>";

                        return;
                    }
                } else {
                    $task_response = $this->createTask($project_id, $task_name);
                    if (!$task_response == '37') {
                        $this->tasks['task_name'] = $task_response;
                    }
                }
            } else if (array_key_exists('project_name', $data)) {
                $project_name = $data['project_name'];
                try {
                    $project_id = $this->createProject($project_name);
                } catch (Exception $ex) {
                    print "<pre>";
                    print_r($ex);
                    print "</pre>";

                    return;
                }
            }
        }elseif(!empty ($podio_task_id) || !empty ($podio_task_name)){
            $task_id = $this->createTask($this->team_task, $podio_task_name);
        }
    }

    public function timeZoneConversion() {
        $la_time = new DateTimeZone('America/Los_Angeles');
        $datetime->setTimezone($la_time);
        return $datetime->format('Y-m-d H:i:s');
    }

    public function randomKey($length) {
        $key = '';
        $pool = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        for ($i = 0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $key;
    }

    public function key() {
        $key = $this->randomKey(8) . "-"
                . $this->randomKey(4) . "-"
                . $this->randomKey(4) . "-"
                . $this->randomKey(4) . "-"
                . $this->randomKey(12);
        return $key;
    }

    public function createProject($project_name) {
        $uuid = $this->key();
        $temp_id = $this->key();

        $post_data = [
            'token' => $token,
            'commands' =>
//              Worked For Creating tasks and Projects by this script
            '[{"type": "project_add", "temp_id": "' . $temp_id . '",'
            . ' "uuid": "' . $uuid . '", "args": {"name": "' . $project_name . '"}}]'];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        // var_dump($output);
        print "<pre>";
        print_r($output);
        print "</pre>";

        curl_close($ch);
    }

    public function createTask($project_id, $task_name) {
        $uuid = $this->key();
        $temp_id = $this->key();

        $post_data = [
            'token' => $token,
            'commands' =>
//              Worked For Creating tasks and Projects by this script
            '[{"type": "item_add", "temp_id": "' . $temp_id . '",'
            . ' "uuid": "' . $uuid . '",'
            . ' "args": {"content": "' . $task_name . '", "project_id": ' . $project_id . '}}]'];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        curl_close($ch);

        $task_id = getTaskId($output, $uuid, $temp_id);
        return $task_id;
    }

    public function findProject($podio_project_id) {
        if (array_key_exists($podio_project_id, $this->projects)) {
            return $this->projects[$podio_project_id];
        } else {
            return NULL;
        }
    }

    public function findTask($podio_task_id) {
        if (array_key_exists($podio_task_id, $this->tasks)) {
            return $this->tasks[$podio_task_id];
        } else {
            return NULL;
        }
    }

    public function updateTask($task_name) {
        if (array_key_exists($task_name, $this->tasks)) {
            return $this->tasks[$project_name];
        } else {
            return NULL;
        }
    }

    public function getTaskId($json_obj, $uuid, $temp_id) {
        $json = json_decode($json_obj);

        foreach ($json->sync_status as $uuid_key => $mydata) {
            // var_dump($mydata);
            if ($uuid_key == $uuid) {
                if ($mydata == 'ok') {
                    foreach ($json->temp_id_mapping as $temp_id_key => $val) {
                        if ($temp_id_key == $temp_id) {
                            echo "App_Key : " . $val;
                            return $val;
                        }
                    }
                }
            } else {

                foreach ($mydata as $key => $val) {
                    if ($key == "error_code") {
                        echo "Error Code : " . $val;
                        return $val;
                    }
                }
            }
        }
    }

}
