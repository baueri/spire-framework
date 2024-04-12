<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Bootstrappers;

use Baueri\Spire\Framework\Bootstrapper;
use Baueri\Spire\Framework\Http\View\Directives\EchoDirective;
use Baueri\Spire\Framework\Http\View\Directives\ExtendsDirective;
use Baueri\Spire\Framework\Http\View\Directives\IfDirective;
use Baueri\Spire\Framework\Http\View\Directives\IncludeDirective;
use Baueri\Spire\Framework\Http\View\Directives\LangDirective;
use Baueri\Spire\Framework\Http\View\Directives\RouteDirective;
use Baueri\Spire\Framework\Http\View\Directives\SectionDirective;
use Baueri\Spire\Framework\Http\View\Directives\YieldDirective;
use Baueri\Spire\Framework\Http\View\ViewParser;
use Baueri\Spire\Framework\Http\View\Directives\ForeachDirective;

class BootDirectives implements Bootstrapper
{
    public function boot(): void
    {
        ViewParser::registerDirective(new ExtendsDirective());
        ViewParser::registerDirective(new RouteDirective());
        ViewParser::registerDirective(new IncludeDirective());
        ViewParser::registerDirective(new EchoDirective());
        ViewParser::registerDirective(new LangDirective());
        ViewParser::registerDirective(new SectionDirective());
        ViewParser::registerDirective(new YieldDirective());
        ViewParser::registerDirective(new IfDirective());
        ViewParser::registerDirective(new ForeachDirective());
    }
}
