<?php

require_once('core/config.php');

session_destroy();

Header("Location: /");
