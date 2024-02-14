<?php
    namespace App\Controllers;

    use \Firebase\JWT\JWT;
    use App\Models\Users;

    class AuthController
    {
        private $requestMethod;
        private $users;

        public function __construct($requestMethod)
        {
            $this->requestMethod = $requestMethod;
            $this->users = Users::getInstance();
        }

        public function loginFromRequest()
        {
            ob_start();
            $input = (array) json_decode(file_get_contents("php://input"), true);

            $usuario = $input["usuario"];
            $dataUser = $this->users->login($usuario, $input["password"]);

            if ($dataUser) {
                $issuerClaim = "localhost"; // this can be the servername
                $audienceClaim = "localhost";
                $isSuedAtClaim = time(); // issued at
                $notBeforeClaim = time(); //not before in seconds
                $expireClaim = $isSuedAtClaim + 3600; // expire time in seconds
                $token = [
                    "iss" => $issuerClaim,
                    "aud" => $audienceClaim,
                    "iat" => $isSuedAtClaim,
                    "nbf" => $notBeforeClaim,
                    "exp" => $expireClaim,
                    "data" => [
                        "usuario" => $usuario,
                    ]
                ];

                $jwt = JWT::encode($token, KEY, 'HS256');
                $res = json_encode([
                    "message" => "Successful login.",
                    "jwt" => $jwt,
                    "user" => $usuario,
                    "expireAt" => $expireClaim
                ]);

                $response["status_code_header"] = "HTTP/1.1 201 OK";
                $response["body"] = $res;
            } else {
                $response["status_code_header"] = "HTTP/1.1 401 Unauthorized";
                $response["body"] = json_encode([
                    "message" => "Access denied.",
                    "error" => "Invalid user or password."
                ]);
            }
            header($response["status_code_header"]);
            if ($response["body"]) {
                echo $response["body"];
            }
        }
    }
