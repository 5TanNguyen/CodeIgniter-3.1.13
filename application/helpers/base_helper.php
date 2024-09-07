<?php

if (!function_exists('dd')) {
    /**
     * Dump and Die
     *
     * @param mixed $data
     */
    function dd($data)
    {
        if (is_array($data)) {
            print_r('
<pre>');
            print_r($data);
            die();
        } else {
            var_dump($data);
            die();
        }
    }
}
