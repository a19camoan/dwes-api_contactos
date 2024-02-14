<?php
    namespace App\Models;

    #[\AllowDynamicProperties]
    class Contactos extends DBAbstractModel
    {
        # Contrucción del modelo singleton.
        private static $instance;

        public static function getInstance()
        {
            if (!isset(self::$instance)) {
                $miclase = __CLASS__;
                self::$instance = new $miclase;
            }
            return self::$instance;
        }
        public function __clone()
        {
            trigger_error("La clonación de este objeto no está permitida", E_USER_ERROR);
        }

        public function set($data = array())
        {
            foreach ($data as $campo=>$valor) {
                $this->params[$campo] = $valor;
            }

            $this->query = "INSERT INTO contactos(nombre, telefono, email)
                                    VALUES (:nombre, :telefono, :email)";
            $this->getResultsFromQuery();
            $this->mensaje = "Contacto added";
        }
    
        public function get($id = "")
        {
            if ($id != "") {
                $this->query = "SELECT * FROM contactos WHERE id = :id";
                $this->params["id"] = $id;
                $this->getResultsFromQuery();
            }
            if (count($this->rows) == 1) {
                foreach ($this->rows[0] as $propiedad => $valor) {
                    $this->$propiedad = $valor;
                }
                $this->mensaje = "Contact found";
            } else {
                $this->mensaje = "Contact not found";
            }
            return $this->rows[0]??null;
        }

        public function edit($id = "", $data = array())
        {
            if (!isset($data["nombre"]) || !isset($data["telefono"]) || !isset($data["email"])) {
                $this->mensaje = "Contacto no encontrado";
            } else {
                foreach ($data as $campo=>$valor) {
                    $this->params[$campo] = $valor;
                }
                $this->query = "UPDATE contactos
                                SET nombre=:nombre, telefono=:telefono, email=:email
                                WHERE id = :id";

                $this->getResultsFromQuery();
                $this->mensaje = "Contact modified";
            }
        }


        public function delete($id = "")
        {
            $this->query = "DELETE FROM contactos WHERE id = :id";
            $this->params["id"] = $id;
            $this->getResultsFromQuery();
            $this->mensaje = "Contact deleted";
        }

        public function getAll()
        {
            $this->query = "SELECT * FROM contactos";
            $this->getResultsFromQuery();
            return $this->rows;
        }
    }
