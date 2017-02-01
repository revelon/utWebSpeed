dummy<hr>

<?php

var_dump($_REQUEST);

file_put_contents("./log.log", "\n". date(DATE_RFC2822) ." ". print_r($_REQUEST, 1) . print_r($_SERVER, 1), FILE_APPEND);

