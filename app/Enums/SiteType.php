<?php

namespace App\Enums;

enum SiteType: string
{
    case Wordpress = 'wordpress';
    case StaticHtml = 'static_html';
    case Php = 'php';
    case LandingPage = 'lp';
    case Other = 'other';
}
