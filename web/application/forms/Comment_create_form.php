<?php

declare(strict_types=1);

namespace Forms;

use Interfaces\Form_interface;
use MY_Controller;

class Comment_create_form extends MY_Controller implements Form_interface
{
    /**
     * @var int|null
     */
    private $post_id;

    /**
     * @var int|null
     */
    private $reply_id;

    /**
     * @var string|null
     */
    private $text;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form'));
        $this->load->library(['form_validation']);
    }

    public function fill(array $data): void
    {
        if (array_key_exists('post_id', $data)) {
            $this->post_id = $data['post_id'];
        }
        if (array_key_exists('reply_id', $data)) {
            $this->reply_id = $data['reply_id'];
        }
        if (array_key_exists('text', $data)) {
            $this->text = $data['text'];
        }
    }

    public function validate(): bool
    {
        $this->form_validation->set_rules('post_id', 'Post ID', 'required');
        $this->form_validation->set_rules('text', 'Comment', 'required');

        if ($this->form_validation->run() === FALSE) {
            throw new \Exception($this->form_validation->error_string());
        }
        return true;
    }

    /**
     * @return int|null
     */
    public function get_post_id(): ?int
    {
        return $this->post_id;
    }

    /**
     * @return int|null
     */
    public function get_reply_id(): ?int
    {
        return $this->reply_id;
    }

    /**
     * @return string|null
     */
    public function get_text(): ?string
    {
        return $this->text;
    }
}
