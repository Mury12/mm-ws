<?php

namespace MMWS\Entity;

use \PDO;

use MMWS\Model\User;

use PDOStatement;

class UserEntity
{

    public $u;

    function __construct(User $u)
    {
        $this->u = $u;
    }



    function save()
    {
        global $conn;


        // $from_ip = ORIGIN_HTTP_ADDR;

        $q = $conn->prepare('INSERT INTO `users` (username, email, `password`) VALUES (?, ?, ?)  ');
        $q->bindParam(1, $this->u->username);
        $q->bindParam(2, $this->u->email);
        $q->bindParam(3, $this->u->password);

        return perform_query_pdo($q);
    }

    /**
     * Binds password and username process to authentication service
     */
    function bindUserPassword()
    {
        global $conn;

        // $_from_ip = ORIGIN_HTTP_ADDR;
        $q = $conn->prepare('SELECT count(1) FROM users where ');
        $q->bindParam(1, $this->u->user);
        $q->bindParam(2, $this->u->password);

        if ($r = perform_query_pdo($q)) {
            $r = $r->fetch(PDO::FETCH_NUM);
            return $r[0];
        }
        return false;
    }

    function logout()
    {
        global $conn;

        $q = $conn->prepare('call sp_remove_token(?)');
        $q->bindParam(1, $this->u->session_token);

        return perform_query_pdo($q) ? true : false;
    }

    function verify()
    {
        global $conn;
        // $from_ip = ORIGIN_HTTP_ADDR;
        if (isset($this->u) && strlen($this->u->session_token) == 64) {
            $q = $conn->prepare('select fn_verifica_token(?, ?)');
            $q->bindParam(1, $this->u->session_token);
            $q->bindParam(2, $from_ip);

            $r = perform_query_pdo($q);

            if ($r) {
                $r= $r->fetch(PDO::FETCH_NUM)[0];
                return  $r ?? false;
            }
        }
        return false;
    }

    function passwordRecovery()
    {
        global $conn;

        $query = "";

        $_n = pop_password();
        $new_pass = hash('sha256',$_n);

        $q = $conn->prepare($query);
        $q->bindParam(1, $new_pass);
        $q->bindParam(2, $this->u->email);
        $q->bindParam(3, $this->u->user);

        return perform_query_pdo($q)->rowCount() > 0 ? $_n : false;
    }
}
