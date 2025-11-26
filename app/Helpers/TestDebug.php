<?php

if (! function_exists('tdump')) {
    /**
     * Gibt Daten IMMER im Terminal aus – unabhängig vom Test-Framework.
     */
    function tdump(mixed $var, ?string $title = null)
    {
        $output = "";

        if ($title) {
            $output .= "\n===== {$title} =====\n";
        }

        if (is_array($var) || is_object($var)) {
            $output .= print_r($var, true);
        } else {
            $output .= $var;
        }

        $output .= "\n===========================\n";

        // → Erzwinge Ausgabe in der Konsole
        fwrite(STDOUT, $output);

        return $var; // erlaubt chaining
    }
}
