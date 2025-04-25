<?php

/**
 * Виводить детальну інформацію про змінну (включно з методами об'єкта з параметрами та типами повернення),
 * файл та рядок виклику, стек викликів та завершує виконання скрипта.
 *
 * @param mixed $var Будь-яка змінна, яку потрібно дослідити.
 */
function dd(mixed $var, ): void
{
    // Отримуємо інформацію про місце виклику функції dd().
    $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file = $debug['file'] ?? 'unknown file';
    $line = $debug['line'] ?? 'unknown line';

    echo "<pre style='background:#1e1e1e;color:#dcdcdc;padding:1em;font-family:monospace;border-radius:5px;'>";
    echo "📍 Called from: <span style='color:#add8e6;'>$file</span> on line <span style='color:#add8e6;'>$line</span>\n";

    // Стек викликів
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    array_shift($backtrace); // Видаляємо перший елемент (виклик dd())

    echo "<details>";
    echo "<summary style='cursor:pointer;color:#ffd700;'>📜 Call Stack</summary>";
    echo "<ol style='padding-left:1.5em;margin-top:0.5em;'>";
    foreach ($backtrace as $index => $trace) {
        $file = $trace['file'] ?? 'unknown file';
        $line = $trace['line'] ?? 'unknown line';
        $function = $trace['function'] ?? 'unknown function';
        $class = isset($trace['class']) ? $trace['class'] . '::' : '';
        $type = $trace['type'] ?? '';
        echo "<li style='margin-bottom:0.3em;'>";
        echo "<span style='color:#a9a9a9;'>#" . ($index + 1) . "</span> ";
        echo "<span style='color:#add8e6;'>$file</span>:<span style='color:#add8e6;'>$line</span> - ";
        echo "<span style='color:#f0e68c;'>$class$function</span><span style='color:#a9a9a9;'>()</span>";
        echo "</li>";
    }
    echo "</ol>";
    echo "</details>\n\n";

    if (is_object($var)) {
        echo "🔍 Type: Object (<span style='color:#98fb98;'>" . get_class($var) . "</span>)\n\n";

        // Properties
        echo "<span style='color:#faebd7;'>📦 Properties:</span>\n";
        $reflection = new ReflectionObject($var);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $visibility = Reflection::getModifierNames($property->getModifiers());
            echo "  - (<span style='color:#87cefa;'>" . implode(' ', $visibility) . "</span>) <span style='color:#ffdab9;'>$" . $property->getName() . "</span> = ";
            try {
                print_r($property->getValue($var));
            } catch (Throwable $e) {
                echo "<span style='color:#ff4500;'>[unreadable property]</span>";
            }
            echo "\n";
        }

        // Public Methods with parameters and return types
        echo "\n<span style='color:#faebd7;'>🔧 Public Methods:</span>\n";
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class === get_class($var)) {
                $params = [];
                foreach ($method->getParameters() as $param) {
                    $type = $param->hasType() ? '<span style=\'color:#8fbc8f;\'>' . $param->getType() . ' </span>' : '';
                    $default = $param->isOptional() && $param->isDefaultValueAvailable()
                        ? ' <span style=\'color:#d8bfd8;\'>= ' . var_export($param->getDefaultValue(), true) . '</span>'
                        : '';
                    $params[] = $type . '<span style=\'color:#ffd700;\'>$' . $param->getName() . '</span>' . $default;
                }

                // Get return type
                $returnType = $method->hasReturnType() ? ': <span style=\'color:#8fbc8f;\'>' . $method->getReturnType() . '</span>' : '';

                echo "  - <span style='color:#66cdaa;'>" . $method->getName() . "</span>(<span style='color:#dcdcdc;'>" . implode(', ', $params) . "</span>)<span style='color:#dcdcdc;'>" . $returnType . "</span>\n";
            }
        }

    } elseif (is_array($var)) {
        echo "🔍 Type: Array (<span style='color:#98fb98;'>length = " . count($var) . "</span>)\n\n";
        print_r($var);

    } elseif (is_string($var)) {
        echo "🔍 Type: String (<span style='color:#98fb98;'>length = " . strlen($var) . "</span>)\n\n";
        echo "<span style='color:#eee8aa;'>" . htmlspecialchars($var) . "</span>";

    } elseif (is_int($var)) {
        echo "🔍 Type: Integer\n\n";
        echo "<span style='color:#f08080;'>" . $var . "</span>";

    } elseif (is_float($var)) {
        echo "🔍 Type: Float\n\n";
        echo "<span style='color:#dda0dd;'>" . $var . "</span>";

    } elseif (is_bool($var)) {
        echo "🔍 Type: Boolean\n\n";
        echo "<span style='color:#adff2f;'>" . ($var ? 'true' : 'false') . "</span>";

    } elseif (is_null($var)) {
        echo "🔍 Type: NULL\n\n";
        echo "<span style='color:#808080;'>null</span>";

    } else {
        echo "🔍 Type: Unknown\n\n";
        var_dump($var);
    }

    echo "</pre>";
    exit;
}
