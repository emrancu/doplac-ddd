<?php


use Doplac\Domain\Supports\DomainSupport;



//function extractLastWord($input) {
//    $words = preg_split('/(?=[A-Z])/', $input);
//    return end($words);
//}

function removeLastWord($input): string
{
    $words = preg_split('/(?=[A-Z])/', $input);

    if (count($words) > 1) {
        array_pop($words);
    }

    return implode('', $words);
}


function resolveInertiaPagePath($name): string
{
    $support =  new DomainSupport();
    $domains =  $support->getDomains();

    $domain =  removeLastWord($name);

    if(isset($domains[$domain]) ){
        return "domains/$domain/resources/js/Pages/{$name}.vue";
    }

    return "resources/js/app/Pages/{$name}.vue";
}