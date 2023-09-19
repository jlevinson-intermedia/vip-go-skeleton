<?php

namespace Helpers;

class HubspotClientHelper
{
    public static function makeRequest($method, $url, $token, $operation = '', $params = array())
    {
        try {
            if(empty($url)) {
                throw new Exception('Please supply request url');
            }

            if(empty($token)) {
                throw new Exception('Please supply request token');
            }

            if(empty($method) || !in_array(strtolower($method), array('post', 'get'))) {
                throw new Exception('Please supply request method which must be POST OR GET');
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);

            if(!empty($operation) && in_array($operation, array('PUT', 'DELETE'))) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $operation);
            }

            if(strtolower($method) == 'post') {
                curl_setopt($ch, CURLOPT_POST, 1);
                if(!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $token
            ));

            $result = curl_exec($ch);

            return json_decode($result);

            curl_close($ch);
        }catch(Exception $e) {
            echo 'Exception: ' . $e->getMessage();
        }
    }

    public static function get($url, $token) 
    {
        if(empty($url) || empty($token)) {
            return '';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Bearer " . $token
        )
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return '';
        } else {
            return json_decode($response);
        }
    }
    
    public static function get_ailec_event_start_date($event_id, $format)
    {
        global $wpdb; 

        $sql = "select p.post_title, p.post_content, 
                        e.timezone_name, e.venue, e.address, e.start, e.end, e.contact_url, e.post_id 
                        from ".$wpdb->prefix."ai1ec_events as e inner join ".$wpdb->prefix."posts as p on 
                        (e.post_id=p.id and p.post_type='ai1ec_event' and p.post_status='publish') where e.post_id = $event_id";

        $r = $wpdb->get_row($sql);

        if(!empty($r)) {
            if(!empty($r->timezone_name)) { 
                date_default_timezone_set($r->timezone_name);
            }
            return date($format, $r->start);
        }

        return 'n/a';
    }
    
    public static function get_excerpt_from_post($post_id) 
    {
        global $wpdb; 

        $sql = 'select post_excerpt from '.$wpdb->prefix.'posts where ID="'.$post_id.'" and post_status="publish" limit 1';

        $result = $wpdb->get_row($sql);

        return $result->post_excerpt; 
    }
    
}
