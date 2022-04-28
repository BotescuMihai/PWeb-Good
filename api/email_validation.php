<?php
function profesor($email)
{
    for ($i = 0; $i < strlen($email); $i++) {
        if (ctype_digit($email[$i])) {
            return false;
        }
    }
    return true;
}

function student($email)
{
    if (profesor($email) == false)
        return true;
    return false;
}