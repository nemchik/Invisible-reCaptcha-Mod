<?php
/*
Plugin Name: Invisible reCaptcha Mod
Description: Modify Invisible reCaptcha to skip internal IP addresses.
Version: 1.0
Author: Mihai Chelaru
*/
add_action('wp_print_scripts', function () {
    $clientIp = \InvisibleReCaptcha\MchLib\Utils\MchHttpRequest::getClientIp();
    if (\InvisibleReCaptcha\MchLib\Utils\MchIPUtils::isPublicIpAddress($clientIp)) {
        return;
    }

    if (!function_exists('login_header')) { // not login page
        return;
    }

    if (!\InvisibleReCaptcha\MchLib\Utils\MchIPUtils::isIpInCIDRRange($clientIp, '172.16.16.*')) {
        return;
    }

    wp_dequeue_script('google-invisible-recaptcha');
});

add_filter('google_invre_is_valid_request_filter', function ($isValid) {
    if (empty($_POST)) {
        return $isValid;
    }
    $clientIp = \InvisibleReCaptcha\MchLib\Utils\MchHttpRequest::getClientIp();
    if (\InvisibleReCaptcha\MchLib\Utils\MchIPUtils::isPublicIpAddress($clientIp)) {
        return $isValid;
    }

    if (!\InvisibleReCaptcha\MchLib\Utils\MchIPUtils::isIpInCIDRRange($clientIp, '172.16.16.*')) {
        return $isValid;
    }

    return true;
}, 9999);
