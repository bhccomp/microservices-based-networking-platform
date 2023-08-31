<?php 

namespace App\Message;

class EmailNotification {

    private $email;
    private $type;
    private $data;

    public function __construct(string $email, string $type, array $data) {

        $this->email = $email;
        $this->type = $type;
        $this->data = $data;

    }

}
