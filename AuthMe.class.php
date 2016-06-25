<?php

class AuthMe
{

    /* TIPOS DE HASH DO AUTHME */
    const MD5 = "md5";
    const SHA256 = "sha256";
    const SHA1 = "sha1";
    const WHIRLPOOL = "whirlpool";
    const MD5VB = "md5vb";
    const PLAINTEXT = "plaintext";

    /* CONEXÃO DO BANCO DE DADOS. */
    private $conection;

    /* NOME DA TASBELA DO AUTHME */
    private $authme_table;

    /* HASH USADA */
    private $algorithm;

    /*
        PARAMETROS
        $db_host = Ip do seu banco de dados mysql;
        $db_user = Nome de usuario do seu banco de dados mysql.
        $db_pass = Senha do seu banco de dados mysql;
        $db_name = Nome do database do mysql;
        $authme_table = Nome da tabela do authme;
        $algo = Tipo de hash que seu authme está utilizando;
    */
    public function __construct($db_host, $db_user, $db_pass, $db_name, $authme_table, $algo) {
        $this->authme_table = $authme_table;
        $this->algorithm    = $algo;
        @$this->conection = mysqli_connect($db_host, $db_user, $db_pass) or die(mysqli_connect_error());
        @mysqli_select_db($this->conection, $db_name) or die(mysqli_error($this->conection));
    }

    /* METODO DESTRUTOR, O CONTRARIO DO CONSTRUTOR '-' */
    public function __destruct() {
        if (is_object($this->conection)) {
            $this->conection->close();
            unset($this->algorithm);
            unset($this->authme_table);
        }
    }

    /*
        METODO USADO PARA AUTENTICAR UM USUARIO, RETORNA true CASO OS
        DADOS ESTEJAM CORRETOS, CASO CONTRARIO RETORNA false.

        PARAMETROS
        $user = Nome de usuario.
        $pass = Senha do usuario.
    */
    public function authenticate($user, $pass) {
        $user  = addslashes($user);
        $query = mysqli_query($this->conection, "SELECT password FROM {$this->authme_table} WHERE username='{$user}'");

        if (mysqli_num_rows($query) == 1) {
            $ret       = mysqli_fetch_array($query);
            $hash_pass = $ret[0];
            return self::compare($pass, $hash_pass);
        } else {
            return false;
        }
    }

    /*
        METODO USADO PARA REGISTRAR UM USUARIO

        PARAMETROS
        $user = Nome de usuario.
        $pass = Senha do usuario.
        $ip = Ip do usuario.
    */
    public function register($user, $pass, $ip = "0.0.0.0") {
        $user = addslashes($user);
        $pass = addslashes(self::AMHash($pass));

        if (self::isUsernameRegistered($user)) {
            return false;
        }

        return mysqli_query($this->conection, "INSERT INTO {$this->authme_table} (`username`, `password`, `ip`, `lastlogin`, `x`, `y`, `z`) VALUES ('{$user}','{$pass}','{$ip}','0','0','0','0')");
    }

    /*
        METODO USADO PARA ALTERAR A SENHA DE UM USUARIO

        PARAMETROS
        $user = Nome de usuario.
        $newpass = Nova senha do usuario.
    */
    public function changePassword($username, $newpass) {
        if (!self::isUsernameRegistered($username)) {
            return false;
        }

        $username = addslashes($username);
        $newpass  = addslashes(self::AMHash($newpass));

        return mysqli_query($this->conection, "UPDATE {$this->authme_table} SET password='$newpass' WHERE username='$username'");
    }

    /*
        METODO USADO PARA VERIFICAR SE UM DETERMINADO IP ESTA REGISTRADO.

        PARAMETROS
        $ip = Ip que deseja verificar.
    */
    public function isIpRegistered($ip) {
        $ip    = addslashes($ip);
        $query = mysqli_query($this->conection, "SELECT ip FROM {$this->authme_table} WHERE ip='{$ip}'");
        return mysqli_num_rows($query) >= 1;
    }

    /*
        METODO USADO PARA VERIFICAR SE UM DETERMINADO NOME DE USUARIO ESTA REGISTRADO.

        PARAMETROS
        $user = Nome de usuario que deseja verificar.
    */
    public function isUsernameRegistered($user) {
        $user  = addslashes($user);
        $query = mysqli_query($this->conection, "SELECT username FROM {$this->authme_table} WHERE username='{$user}'");
        return mysqli_num_rows($query) >= 1;
    }

    /* METODOS PRIVADOS, USO SOMENTE DA CLASSE. */
    private function compare($pass, $hash_pass) {
        switch ($this->algorithm) {
            case self::SHA256:
                $shainfo = explode("$", $hash_pass);
                $pass    = hash("sha256", $pass) . $shainfo[2];
                return strcasecmp($shainfo[3], hash('sha256', $pass)) == 0;

            case self::SHA1:
                return strcasecmp($hash_pass, hash('sha1', $pass)) == 0;

            case self::MD5:
                return strcasecmp($hash_pass, hash('md5', $pass)) == 0;

            case self::WHIRLPOOL:
                return strcasecmp($hash_pass, hash('whirlpool', $pass)) == 0;

            case self::MD5VB:
                $shainfo = explode("$", $hash_pass);
                $pass    = hash("md5", $pass) . $shainfo[2];
                return strcasecmp($shainfo[3], hash('md5', $pass)) == 0;

            case self::PLAINTEXT:
                return $hash_pass == $pass;

            default:
                return false;
        }
    }

    private function AMHash($pass) {
        switch ($this->algorithm) {
            case self::SHA256:
                $salt = self::createSalt();
                return "\$SHA\$" . $salt . "\$" . hash("sha256", hash('sha256', $pass) . $salt);

            case self::SHA1:
                return hash("sha1", $pass);

            case self::MD5:
                return hash("md5", $pass);

            case self::WHIRLPOOL:
                return hash("whirlpool", $pass);

            case self::MD5VB:
                $salt = self::createSalt();
                return "\$MD5vb\$" . $salt . "\$" . hash("md5", hash('md5', $pass) . $salt);

            case self::PLAINTEXT:
                return $pass;

            default:
                return null;
        }
    }

    private function createSalt() {
        $salt = "";
        for ($i = 0; $i < 20; $i++) {
            $salt .= rand(0, 9);
        }
        return substr(hash("sha1", $salt), 0, 16);
    }
}
?>