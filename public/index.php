<?php
    /**
     * API Rest CRUD contactos
     * end points:
     * GET /login :             Necesita body con "usuario" y "password". Devuelve token en "jwt".
     * GET /contactos :         Devuelve todos los contactos.
     * GET /contactos/{id} :    Devuelve el contacto con id = {id}.
     * POST /contactos :        Necesita body con "nombre", "telefono" y "email". Crea un nuevo contacto.
     * PUT /contactos/{id} :    Necesita body con "nombre", "telefono" y "email". Modifica el contacto con id = {id}.
     * DELETE /contactos/{id} : Borra el contacto con id = {id}.
     */
    use App\Core\Router;
    use App\Controllers\ContactosController;
    use App\Controllers\AuthController;
    use \Firebase\JWT\JWT;
    use \Firebase\JWT\Key;

    require_once "../bootstrap.php";

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, " .
        "Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $request = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $uri =  explode("/", $request);

    $id = null;
    if (isset($uri[2])) {
        $id = (int) $uri[2];
    }

    # Proceso de login
    if ($request == "/login") {
        $auth = new AuthController($requestMethod);
        if (!$auth -> loginFromRequest()) {
            exit(http_response_code(401));
        }
    }
    # Se ha creado correctamente el authcontroller. Autentificación OK.


    $input = (array) json_decode(file_get_contents("php://input"), true);

    # Recuperamos el token
    if (!isset($_SERVER["HTTP_AUTHORIZATION"])) {
        echo json_encode([
            "message" => "Access denied.",
            "error" => "Token not found"
        ]);
        exit(http_response_code(401));
    } else {
        $authHeader = $_SERVER["HTTP_AUTHORIZATION"];
        $arr = explode(" ", $authHeader);
        $jwt = $arr[1] ?? null;
    }
    
    if ($jwt) {
        try {
            $decoded = (JWT::decode($jwt, new Key(KEY, "HS256")));
        } catch (Exception $e) {
            echo json_encode([
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ]);
            exit(http_response_code(401));
        }
    }

    $router = new Router();
    $router->add(array(
        "name" => "home",
        "path" => "/^\/contactos\/([0-9]+)?$/",
        "action" => ContactosController::class)
    );
    $router->add(array(
        "name" => "create",
        "path" => "/^\/contactos$/",
        "action" => ContactosController::class)
    );

    $router = $router->match($request);
    if ($router) {
        $controllerName = $router["action"];
        $controller = new $controllerName($requestMethod, $id);
        $controller->processRequest();
    } else {
        $response["status_code_header"] = "HTTP/1.1 404 Not Found";
        $response["message"] = "Not Found";
        echo json_encode($response);
    }
