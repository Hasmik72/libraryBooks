<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        // var_dump($attribute);
        // var_dump($this->hasErrors());
        // var_dump($this->errors);
        
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */

    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
            return Yii::$app->user->login($user, $duration);
            
        } 
        return false;
        
    }



    public function _login() {
        if ($this->_user !== null) {
            $this->_user = new User();
            $this->_user->authenticate($this->username, $this->password);
            $userData = User::findOne(['username' => $this->username, 'password' => $this->password]);
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 3600 * 24 * 30;
            Yii::$app->user->login($userData, $duration);
            return true;

        }
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserIdentity::findByUsername($this->username);
        }

        return $this->_user;
    }
}


