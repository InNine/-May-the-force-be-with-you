<?php

declare(strict_types=1);

use Forms\Comment_create_form;
use Forms\Login_form;
use Model\Boosterpack_model;
use Model\Comment_model;
use Model\Enums\Category_type;
use Model\Item_model;
use Model\Login_model;
use Model\Post_model;
use Model\User_model;
use System\Libraries\Core;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (is_prod()) {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts = Post_model::preparation_many(Post_model::get_all(), 'default');

        return $this->response_success(['posts' => $posts]);
    }

    public function get_boosterpacks()
    {
        $posts = Boosterpack_model::preparation_many(Boosterpack_model::get_all(), 'default');

        return $this->response_success(['boosterpacks' => $posts]);
    }

    public function login()
    {
        $login_form = new Login_form();
        try {
            $login_form->fill($this->input->post());
            $login_form->validate();
            $user = Login_model::login([
                'email' => $login_form->getEmail(),
                'password' => $login_form->getPassword(),
            ]);
        } catch (Exception $e) {
            return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS, ['message' => $e->getMessage()]);
        }

        return $this->response_success(['name' => $user->get_personaname()]);
    }

    public function logout()
    {
        Login_model::logout();

        return $this->response_success(['message' => 'You successfully logged out!']);
    }

    public function comment()
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $comment_create_form = new Comment_create_form();
        try {
            $comment_create_form->fill($this->input->post());
            $comment_create_form->validate();
            Comment_model::create([
                'user_id' => User_model::get_user()->get_id(),
                'assign_id' => $comment_create_form->get_post_id(),
                'reply_id' => $comment_create_form->get_reply_id(),
                'text' => $comment_create_form->get_text(),
                'likes' => 0
            ]);
        } catch (Exception $e) {
            return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS, ['message' => $e->getMessage()]);
        }

        return $this->response_success(['message' => 'comment created successfully']);
    }

    public function like()
    {
        $category = $this->input->get('category');
        $id = (int)$this->input->get('id');
        if ( ! $category OR ! in_array($category, Category_type::get_list()) OR ! $id) {
            return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        switch ($category) {
            case Category_type::POST:
                return $this->like_post($id);
            case Category_type::COMMENT:
                return $this->like_comment($id);
            default:
                return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
    }

    public function like_comment(int $comment_id)
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        try {
            $comment = new Comment_model($comment_id);
        } catch (Exception $exception) {
            return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS, ['message' => 'Wrong comment id!']);
        }

        $user = User_model::get_user();
        if ( ! $comment->increment_likes($user)) {
            return $this->response_error(Core::RESPONSE_GENERIC_INTERNAL_ERROR, ['message' => 'something went wrong!']);
        }

        return $this->response_success(['message' => 'you successfully incremented likes on message!']);
    }

    public function like_post(int $post_id)
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        try {
            $post = new Post_model($post_id);
        } catch (Exception $exception) {
            return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS, ['message' => 'Wrong post id!']);
        }

        $user = User_model::get_user();
        if ( ! $post->increment_likes($user)) {
            return $this->response_error(Core::RESPONSE_GENERIC_INTERNAL_ERROR, ['message' => 'something went wrong!']);
        }

        return $this->response_success(['message' => 'you successfully incremented likes on post!']);
    }

    public function add_money()
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $sum = (float)App::get_ci()->input->post('sum');
        if ( ! $sum) {
            return $this->response_error(Core::RESPONSE_GENERIC_NO_DATA);
        }

        $user = User_model::get_user();
        try {
            $user->add_money($sum);
        } catch (Exception $e) {
            return $this->response_error(Core::RESPONSE_GENERIC_INTERNAL_ERROR);
        }

        return $this->response_success(['message' => 'Money now in your pocket!']);
    }

    public function get_post(int $post_id)
    {
        // TODO получения поста по id
    }

    public function buy_boosterpack()
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $booster_id = $this->input->get('boosterpack_id');
        if ( ! $booster_id) {
            return $this->response_error(Core::RESPONSE_GENERIC_NO_DATA);
        }
        try {
            $booster = new Boosterpack_model($booster_id);
        } catch (Exception $e) {
            return $this->response_error(Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        $user = User_model::get_user();
        $item = $user->buy_boosterpack($booster);
        if ( ! $item) {
            return $this->response_error(Core::RESPONSE_GENERIC_INTERNAL_ERROR);
        }
        return $this->response_success(['message' => 'you successfully got ' . $item->get_price() . ' likes!']);
    }

    /**
     * @return object|string|void
     */
    public function get_boosterpack_info(int $bootserpack_info)
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        //TODO получить содержимое бустерпака
    }
}
