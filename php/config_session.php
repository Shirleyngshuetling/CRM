<?php

    // to change a couple of ionize settings 
    // set the use only cookies and use strict mode to true
    // to make it a lot more secure when handling sessions
    ini_set('session.use_only_cookies',1);
    ini_set('session.use_strict_mode',1);
    

    session_set_cookie_params([
        'lifetime' => 1800, //lifetime of cookie
        'domain' => 'localhost', //the domain the cookie will work on
        'path' => '/', 
        'Secure' => true, //to allow us to only use this cookie inside a secure connection (https conncetion)
        'httpOnly' => true, //to avoid user being able to chnage anything about this cookie using a script like Js inside our website
    ]);

    session_start(); // start the session

    //if condition run an update every 30 mins which will go in and take our cookie
    //and regenerate the Id for that cookie
    //to prevent attackers from gaining access to the cookie and then using the cookie for more than 30 minutes
    if (!isset($_SESSION["last_regeneration"])){
        regenerate_session_id();
    }
    else{
        $interval = 60 * 30; //30 minutes in seconds
        if (time() - $_SESSION["last_regeneration"] >= $interval){
            regenerate_session_id();
        }

    }
    function regenerate_session_id(){
        session_regenerate_id();

        //time() gets the current time of the server
        $_SESSION["last_regeneration"]= time(); //check the last time we went in and updated our session id
    }