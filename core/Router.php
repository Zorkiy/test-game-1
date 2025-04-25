<?php

/**
 * Клас Router відповідає за обробку запитів та визначення відповідного контролера та дії.
 */
class Router
{
    /**
     * Розбирає URL та викликає відповідний контролер.
     */
    public function route()
    {
        // Отримуємо значення параметра 'route' з GET-запиту.
        $route = $_GET['route'] ?? 'default/index'; // Default route if 'route' is not set.

        // Розділяємо маршрут на контролер та дію.
        $parts = explode('/', $route);
        $controllerName = ucfirst(strtolower($parts[0] ?? 'Default')) . 'Controller'; // Make the first letter uppercase.
		$controllerClass = 'app\\Controllers\\'. $controllerName;
        $actionName = strtolower($parts[1] ?? 'index') . 'Action'; // Add 'Action' suffix.
        $params = array_slice($parts, 2); // Any additional parts are parameters.
        $controllerFile = $_SERVER['DOCUMENT_ROOT'] .'/app/Controllers/' . $controllerName . '.php';

        // Перевіряємо, чи існує файл контролера.
        if (file_exists($controllerFile)) {
			// dd($controllerFile);
            require_once $controllerFile;

            // Перевіряємо, чи існує клас контролера.
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();

                // Перевіряємо, чи існує метод (дія) в контролері.
                if (method_exists($controller, $actionName)) {
                    // Викликаємо дію контролера з параметрами.
                    call_user_func_array([$controller, $actionName], $params);
                    return; // Stop further execution.
                } else {
                    // Action not found.
                    $this->error404("Action '$actionName' not found in controller '$controllerClass'.");
                    return;
                }
            } else {
                // Controller class not found.
                $this->error404("Controller class '$controllerClass' not found.");
                return;
            }
        } else {
            // Controller file not found.
            $this->error404("Controller file '$controllerFile' not found.");
            return;
        }
    }

    /**
     * Обробляє помилку 404 (сторінка не знайдена).
     *
     * @param string $message Повідомлення про помилку.
     */
    private function error404(string $message)
    {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
    }
}
