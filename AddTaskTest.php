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
            '[{"type": "item_add", "temp_id": "43f7fs85-a068-47j5-b2c9-4ab7c90977fa",'
            . ' "uuid": "998c4b83-5s51-48f9-3j67-de5685cfd79b",'
            . ' "args": {"content": "Task5", "project_id": 196213069}}]'
        ];

        $ch = curl_init();

        var_dump($ch);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        //var_dump($output);

        curl_close($ch);
        
        return $output;
    }

    /* Xml to PHP array
     * 
    
    function xmlstr_to_array($xmlstr) {
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        $root = $doc->documentElement;
        $output = domnode_to_array($root);
        $output['@root'] = $root->tagName;
        return $output;
    }

    function domnode_to_array($node) {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = domnode_to_array($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string) $v;
                    }
                }
                if ($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                    $output = array('@content' => $output); //Change output into an array.
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }
     */
}
