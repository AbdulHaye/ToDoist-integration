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
    
    //put your code here
    public function dialUrl($username, $password, $url) {
        if (!is_null($username) && !is_null($password) && !is_null($url)) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                $result = curl_exec($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
                curl_close($ch);

                print "<pre>";
                print_r($status_code);
                print "</pre>";

                print "<pre>";
                print_r($result);
                print "</pre>";

                $response = array();
                $response['status_code'] = $status_code;
                $response['result'] = $result;
                return $response;
            } catch (Exception $ex) {
                print "<pre>";
                print_r($ex);
                print "</pre>";
            }
        } else {
            return "Missing arguements";
        }
    }

    public function index($data) {

        // checking url existance
        if (array_key_exists('url', $data)) {
            //$url = "https://todoist.com/API/v7/sync";
            $this->url = $url;
        } else {
            return;
        }

        // checking token existance
        if (array_key_exists('token', $data)) {
            $this->token = $data['token'];
        } else {
            return;
        }

        //cheking project name or id existance
        if (array_key_exists('project_id', $data)) {
            $project_id = $data['project_id'];
            if (array_key_exists('task_id', $data)) {
                try {
                    $task_id = $data['task_id'];
                    $response = $this->updateTask($project_id, $task_id);
                } catch (Exception $ex) {
                    print "<pre>";
                    print_r($ex);
                    print "</pre>";

                    return;
                }
            } elseif (array_key_exists('task_name', $data)) {
                try {
                    $task_name = $data['task_name'];
                    $task_id = $this->createTask($project_id, $task_name);
                } catch (Exception $ex) {
                    print "<pre>";
                    print_r($ex);
                    print "</pre>";

                    return;
                }
            } else {
                return;
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
            '[{"type": "project_add", "temp_id": "'.$temp_id.'",'
            . ' "uuid": "'.$uuid.'", "args": {"name": "'.$project_name.'"}}]'];

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
            . ' "args": {"content": "'.$task_name.'", "project_id": '.$project_id.'}}]'];

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

    public function updateTask($project_id, $task_id) {
        
    }

}
