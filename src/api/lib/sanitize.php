<?php
/**
 * Simple class used to sanitize user inputs and form validation.
 * There is no internal state so we use a singleton to save memory 
 * and provide a namespace for these functions.
 */ 
class sanitize extends Singleton {
    
    protected function __construct()
    {
    }

    function registration($userInput){
        $output = array();

        // test required fields are present
        if(
            empty($userInput['firstName'])
            ||
            empty($userInput['lastName'])
            ||
            empty($userInput['email'])
            ||
            empty($userInput['userName'])
            ||
            empty($userInput['password'])
        ){
            throw new Exception('One or more required fields are missing');
        } else {
            // escape string is not neccessary as variables are always 
            // used as part of a paramaterized query.
            $output['firstName'] = strval($userInput['lastName']);
            $output['laatName'] = strval($userInput['lastName']);
            $output['email'] = strval($userInput['email']);
            $output['userName'] = strval($userInput['userName']);
            $output['password'] = strval($userInput['password']);
        }

        // test confirmation fields match
        if(
            $output['email'] != $userInput['emailConfirm']
            ||
            $output['password'] != $userInput['passwordConfirm']
        ) {
            throw new Exception('One or more required fields are missing.');
        }

        // simple email validation, we really rely sending an email for account validation
        if( filter_var($output['email'] , FILTER_VALIDATE_EMAIL) ){
            throw new Exception('Invlaid email address.');
        }

        return $output;
    }
}