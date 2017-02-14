<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AddTaskTest
 *
 * @author Cresenttech
 */


class AddTaskTest {

    //put your code here
    public function index() {
        $token = 'cbb11f920c82b498ec295608285e82dc6a523969';
        $project_id = '2F196193946';
        $url = "https://todoist.com/API/v7/sync";
        $post_data = [
            'token' => $token,
            'commands' =>
//              Worked For Creating task and "Project4 is created by this
//            '[{"type": "project_add", "temp_id": "4ff1e388-5ca6-453a-b0e8-662ebf373b6b",'		8-4-4-4-12
//            . ' "uuid": "32774db9-a1da-4550-8d9d-910372124fa4", "args": {"name": "Project4"}}]'
            '[{"type": "item_add", "temp_id": "43f7cd23-a038-46b5-b2c9-4abda9097ffa",'
            . ' "uuid": "997c4b43-55f1-48a9-9e66-de5785dfd69b",'
            . ' "args": {"content": "Task2", "project_id": 196213069}}]'
        ];

        $ch = curl_init();
        
        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        
        var_dump($output);

        curl_close($ch);
    }

}
