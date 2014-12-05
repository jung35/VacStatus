<?php

$str = "http://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/e1/e14cf3c3a571bf1266726bbe76a01276ce01bfd1.jpg";

preg_match('/^(.*)?\/avatars\/(.*)$/i', $str, $matches);

print_r($matches);
