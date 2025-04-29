<?php

/**
 * Клас Router відповідає за обробку HTTP-запитів,
 * визначаючи відповідний контролер та метод (дію) для їх виконання
 * на основі URI запиту.
 */
class Router
{
    /**
     * Головний метод роутера, який обробляє вхідний HTTP-запит.
     * Аналізує URI, визначає контролер, дію та параметри,
     * а потім викликає відповідний метод контролера або обробляє помилку 404.
     * Більш гнучко визначає дію та параметри, дозволяючи маршрути без явної дії
     * (використовуючи дію за замовчуванням 'index').
     *
     * @return void
     */
    public function route(): void
    {
        try {
            $requestUri = $this->getRequestUri();
            $parts = $this->splitUriIntoParts($requestUri); // Отримуємо всі частини URI

            // Визначаємо ім'я контролера (перша частина або 'default')
            $controllerName = ucfirst(strtolower($parts[0] ?? 'default'));
            $remainingParts = array_slice($parts, 1); // Частини після контролера

            $controllerFile = $this->getControllerFilePath($controllerName);
            $this->loadControllerFile($controllerFile); // Викине виняток, якщо файл не знайдено

            $controllerClass = $this->getControllerClassName($controllerName);
            // На цьому етапі ми вже знаємо, що файл і клас контролера існують,
            // завдяки попереднім крокам та обробці винятків.

            // Визначаємо потенційну назву дії (друга частина URI, якщо є)
            $potentialActionName = strtolower($remainingParts[0] ?? '');
            $defaultActionName = 'index'; // Дія за замовчуванням

            $actionName = $defaultActionName; // Спочатку припускаємо дію за замовчуванням
            $params = $remainingParts; // Спочатку припускаємо, що всі частини після контролера - це параметри

            // Перевіряємо, чи існує потенційна дія як метод контролера
            // (додаючи суфікс 'Action')
            $potentialMethodName = $potentialActionName . 'Action';
            if ($potentialActionName !== '' && method_exists($controllerClass, $potentialMethodName)) {
                // Якщо потенційна дія існує як метод, використовуємо її
                $actionName = $potentialActionName;
                // Параметри починаються з третьої частини URI
                $params = array_slice($remainingParts, 1);
            }
            // Якщо потенційна дія не існує або відсутня, використовуємо дію за замовчуванням
            // та всі частини після контролера як параметри (це вже встановлено вище)

            $controllerInstance = $this->createControllerInstance($controllerClass); // Викине виняток, якщо клас не знайдено (малоймовірно тут, але залишаємо)

            // Викликаємо дію контролера з параметрами
            $this->callControllerAction($controllerInstance, $actionName, $params);

        } catch (\Exception $e) {
            // Обробка винятків для відображення 404 помилки
            $this->error404($e->getMessage());
        }
    }

	/**
     * Розбиває очищений URI запиту на масив сегментів (частин) за символом слешу.
     *
     * @param string $requestUri Очищений URI запиту.
     * @return array Масив сегментів URI.
     */
    private function splitUriIntoParts(string $requestUri): array
    {
        // Розділяємо маршрут на частини: контролер, дія та параметри.
        // Якщо URI порожній, повертаємо порожній масив, який потім буде оброблено
        // для встановлення значень за замовчуванням.
        return $requestUri === '' ? [] : explode('/', $requestUri);
    }

	/**
     * Отримує та очищає URI запиту.
     * Видаляє початковий слеш та query string.
     *
     * @return string Очищений URI запиту.
     */
    private function getRequestUri(): string
    {
        // Отримуємо URI запиту та видаляємо початковий слеш, якщо він є.
        // Також видаляємо query string, якщо він присутній.
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

	/**
     * Розбирає очищений URI запиту на складові: контролер, дія та параметри.
     * Використовує маршрут за замовчуванням ('default/index'), якщо URI порожній.
     *
     * @param string $requestUri Очищений URI запиту.
     * @return array Масив з ключами 'controller', 'action', 'params'.
     */
    private function parseRouteParts(string $requestUri): array
    {
        // Визначаємо маршрут за замовчуванням, якщо URI порожній.
        $route = $requestUri === '' ? 'default/index' : $requestUri;

        // Розділяємо маршрут на частини: контролер, дія та параметри.
        $parts = explode('/', $route);

        // Визначаємо ім'я контролера. Перша частина маршруту з великої літери, суфікс 'Controller'.
        // Якщо частина відсутня, використовуємо 'Default'.
        $controllerName = ucfirst(strtolower($parts[0] ?? 'default'));

        // Визначаємо ім'я дії. Друга частина маршруту з маленької літери, суфікс 'Action'.
        // Якщо частина відсутня, використовуємо 'index'.
        $actionName = strtolower($parts[1] ?? 'index');

        // Отримуємо додаткові частини маршруту як параметри.
        $params = array_slice($parts, 2);

        return [
            'controller' => $controllerName,
            'action' => $actionName,
            'params' => $params,
        ];
    }

	/**
     * Визначає повний шлях до файла контролера на основі його імені.
     *
     * @param string $controllerName Ім'я контролера (наприклад, 'Default').
     * @return string Повний шлях до файла контролера.
     */
    private function getControllerFilePath(string $controllerName): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/app/controllers/' . $controllerName . 'Controller.php';
    }

	/**
     * Завантажує файл контролера.
     * Викидає виняток, якщо файл не існує.
     *
     * @param string $controllerFile Повний шлях до файла контролера.
     * @throws \Exception Якщо файл контролера не знайдено.
     * @return void
     */
    private function loadControllerFile(string $controllerFile): void
    {
        if (!file_exists($controllerFile)) {
            throw new \Exception("Controller file '$controllerFile' not found.");
        }
        require_once $controllerFile;
    }

    /**
     * Визначає повне ім'я класу контролера з простором імен.
     *
     * @param string $controllerName Ім'я контролера (наприклад, 'Default').
     * @return string Повне ім'я класу контролера (наприклад, 'app\controllers\DefaultController').
     */
    private function getControllerClassName(string $controllerName): string
    {
        return 'app\\controllers\\' . $controllerName . 'Controller';
    }

    /**
     * Створює екземпляр класу контролера.
     * Викидає виняток, якщо клас не існує.
     *
     * @param string $controllerClass Повне ім'я класу контролера.
     * @throws \Exception Якщо клас контролера не знайдено.
     * @return object Екземпляр контролера.
     */
    private function createControllerInstance(string $controllerClass): object
    {
        if (!class_exists($controllerClass)) {
             throw new \Exception("Controller class '$controllerClass' not found.");
        }
        return new $controllerClass();
    }

    /**
     * Викликає вказаний метод (дію) на екземплярі контролера з передачею параметрів.
     * Викидає виняток, якщо метод не існує.
     *
     * @param object $controllerInstance Екземпляр контролера.
     * @param string $actionName Ім'я методу дії (наприклад, 'index').
     * @param array $params Масив параметрів для передачі дії.
     * @throws \Exception Якщо метод дії не знайдено.
     * @return void
     */
    private function callControllerAction(object $controllerInstance, string $actionName, array $params): void
    {
        // Додаємо суфікс 'Action' до імені методу
        $methodName = $actionName . 'Action';

        if (!method_exists($controllerInstance, $methodName)) {
            throw new \Exception("Action '$methodName' not found in controller '" . get_class($controllerInstance) . "'.");
        }
        call_user_func_array([$controllerInstance, $methodName], $params);
    }

    /**
     * Обробляє помилку 404 (сторінка не знайдена), встановлюючи відповідний HTTP-заголовок
     * та виводячи повідомлення про помилку.
     *
     * @param string $message Повідомлення про помилку для відображення користувачу.
     * @return void
     */
    private function error404(string $message): void
    {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
        exit; // Зупиняємо подальше виконання скрипта після відображення помилки 404.
    }
}
