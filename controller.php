<?
class Router {
    public function route($request) {

        if (!isset($request['controller'])) {
            $this->sendJsonError("Hata: Geçersiz istek. 'controller' zorunludur.");
            return;
        }

        if (!isset($request['action'])) {
            $this->sendJsonError("Hata: Geçersiz istek. 'action' parametresi zorunludur.");
            return;
        }

        $controllerName = $request['controller'] . 'Controller';
        $action = $request['action'];
        
        $controllerFile = 'controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            $this->sendJsonError("Hata: Controller '$controllerName' bulunamadı.");
            return;
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (method_exists($controller, $action)) {
            $result = $controller->$action();
            $this->sendJsonResponse($result);
        } else {
            $this->sendJsonError("Hata: '$action' işlemi '$controllerName' içinde bulunamadı.");
        }
    }

    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function sendJsonError($message) {
        $this->sendJsonResponse([
            "HATA" => true,
            "ACIKLAMA" => $message
        ]);
    }
}


