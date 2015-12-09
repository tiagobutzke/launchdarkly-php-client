<?php

/** @var AppKernel $kernel */

/**
 * @param string $countryCode
 * @param string $rulesFolder
 */
function voloSeoRedirectIfRequired($countryCode, $rulesFolder)
{
    $rulesPath = $rulesFolder . "/$countryCode.php";
    if (!file_exists($rulesPath)) {
        return;
    }

    $rules = include_once($rulesPath);

    $urlParts = parse_url($_SERVER['REQUEST_URI']);
    $url = urldecode(trim($urlParts['path'], '/'));
    $url = trim($url, '/');

    if (array_key_exists($url, $rules)) {
        $newUrl = '/'.$rules[$url];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $newUrl);
        exit();
    }
}

voloSeoRedirectIfRequired(
    $kernel->lookupCountryCode(),
    $kernel->getContainer()->getParameter('redirect_rules_folder')
);
