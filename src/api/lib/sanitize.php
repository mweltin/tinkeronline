<?php
/**
 * Simple class used to sanitize user inputs and form validation.
 * There is no internal state so we use a singleton to save memory
 * and provide a namespace for these functions.
 */
class sanitize {

    public function __construct()
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
        if( !$this->mustMatch( $output['email'], $userInput['emailConfirm']) ) {
            throw new Exception('Confirmation email does not match');
        }

        if( !$this->mustMatch( $output['password'], $userInput['passwordConfirm']) ) {
          throw new Exception('Confirmation password does not match');
        }

        // simple email validation, we really rely sending an email for account validation
        if( filter_var($output['email'] , FILTER_VALIDATE_EMAIL) != $output['email'] ){
            throw new Exception('Invlaid email address.'. $output['email']);
        }

        return $output;
    }

    function mustMatch($a, $b) {
      return $a === $b;
    }
}
