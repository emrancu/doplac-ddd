<?php


use Doplac\Domain\Supports\DomainSupport;



//function extractLastWord($input) {
//    $words = preg_split('/(?=[A-Z])/', $input);
//    return end($words);
//}
//
//function removeLastWord($input): string
//{
//    $words = preg_split('/(?=[A-Z])/', $input);
//
//    if (count($words) > 1) {
//        array_pop($words);
//    }
//
//    return implode('', $words);
//}
//
//
//function resolveInertiaPagePath($name): string
//{
//    $support =  new DomainSupport();
//    $domains =  $support->getDomains();
//    unset($domains['app']);
//
//
//    foreach ($domains as $domain){
//           if(\Illuminate\Support\Str::contains($name, $domain['title'])){
//             return  "domains/{$domain['title']}/resources/js/Pages/{$name}.vue";
//           }
//    }
//
//    return "resources/js/app/Pages/{$name}.vue";
//}