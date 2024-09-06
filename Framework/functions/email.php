<?php

if (!function_exists('email')) {
    function email(string $email): void
    {
        $r = array();
        for($i=0; $i<strlen($email); $i++) {
            $r[$i] = $email[$i];
        }

        $hiddenEmail = implode('##', $r);

        echo '<a href="mailto:'.$hiddenEmail.'" class="email-link">'.$hiddenEmail.'</a>';
    }
}
?>
