<?php

declare(strict_types=1);

namespace Forms;

use Interfaces\Form_interface;
use MY_Controller;

class Login_form extends MY_Controller implements Form_interface
{
    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form'));
        $this->load->library(['form_validation']);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function fill(array $data): void
    {
        if (array_key_exists('email', $data)) {
            $this->email = $data['email'];
        }
        if (array_key_exists('password', $data)) {
            $this->password = $data['password'];
        }
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function validate(): bool
    {
        $this->form_validation->set_data([
            'email' => $this->email,
            'password' => $this->password
        ]);
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            throw new \Exception($this->form_validation->error_string());
        }
        return true;
    }
}
