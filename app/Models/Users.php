<?php
    namespace App\Models;

    #[\AllowDynamicProperties]
    class Users extends DBAbstractModel
    {
        private static $instancia;
        
        public static function getInstance()
        {
            if (!isset(self::$instancia)) {
                $miclase = __CLASS__;
                self::$instancia = new $miclase;
            }
            return self::$instancia;
        }

        public function __clone()
        {
            trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
        }

        public function login($usuario, $password)
        {
            $this->query = "SELECT * FROM usuarios
                            WHERE usuario = :usuario and password = :password";
            $this->params['usuario'] = $usuario;
            $this->params['password'] = $password;

            $this->getResultsFromQuery();
            if (count($this->rows) == 1) {
                foreach ($this->rows[0] as $propiedad=>$valor) {
                    $this->$propiedad = $valor;
                }
                $this->mensaje = "Contacto encontrado";
            } else {
                $this->mensaje = "Contacto no encontrado";
            }
            return $this->rows[0]??null;
        }

        public function get($id="")
        {
            # Not implemented
        }
        public function set()
        {
            # Not implemented
        }
        public function edit()
        {
            # Not implemented
        }
        public function delete()
        {
            # Not implemented
        }

    }
