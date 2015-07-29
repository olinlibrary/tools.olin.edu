<?php

function show_page($f3, $page, $fluid=false){
    $f3->set('content', $page.'.html');
    $f3->set('fluid', $fluid);
    echo Template::instance()->render('page.html');
}

function logger($f3, $message){
    $logger = new Log('app/log.log');
    $message = 'user:'.$f3->get('SESSION.username').' | '.$message;
    $logger->write($message);
}

function get_log($f3){
    $web = \Web::instance();
    $web->send('app/log.log');
}

// Build-in PHP function exists for this in PHP >= 5.6
function ldap_escape($subject){
    $search = array_flip(array('\\', '*', '(', ')', "\x00"));
    $search = array_keys($search); 
    $replace = array();
    foreach ($search as $char) {
            $replace[] = sprintf('\\%02x', ord($char));
    }

    return str_replace($search, $replace, $subject);
}