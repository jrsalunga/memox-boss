<?php
   setcookie('X-CSRF-Token', sha1(rand()));
   echo file_get_contents( 'user.html' );
?>