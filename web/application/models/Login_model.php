<?php

namespace Model;

use App;
use Cassandra\Exception\UnauthorizedException;
use Exception;
use System\Core\CI_Model;

class Login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();

    }

    public static function logout()
    {
        App::get_ci()->session->unset_userdata('id');
    }

    /**
     * @param array $data
     *
     * @return User_model
     *
     * @throws Exception
     */
    public static function login(array $data): User_model
    {
        $user = User_model::find_user_by_email($data['email']);
        //Так как в тестовом пароли никак не хэшируются - используем простое сравнение (но password_verify() найден :) )
        //Ну и следую кодстайлу описанному в доках
        if ( ! $user OR $user->get_password() !== $data['password']) {
            throw new Exception('Email or password are not match');
        }
        self::start_session($user->get_id());

        return $user;
    }

    public static function start_session(int $user_id)
    {
        // если перенедан пользователь
        if (empty($user_id))
        {
            throw new Exception('No id provided!');
        }

        App::get_ci()->session->set_userdata('id', $user_id);
    }
}
