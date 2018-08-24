<?php
header("Content-Type: application/octet-stream;");
header("Content-Disposition: attachment; filename=".urlencode($_GET['name']).".jpg;");
header("Content-Length: 600;");
echo $_GET['name'];
