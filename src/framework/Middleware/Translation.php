<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Middleware;

use App\EventListeners\MissingTranslationListener;
use Baueri\Spire\Framework\Application;
use Baueri\Spire\Framework\Http\Route\RouterInterface;
use Baueri\Spire\Framework\Translation\TranslationMissing;

readonly class Translation implements Middleware
{
    public function __construct(
        private RouterInterface $router,
        private Application $app
    ) {
    }

    public function handle(): void
    {
        TranslationMissing::listen(MissingTranslationListener::class);

        $lang = request()->getUriValue('lang');

        if ($lang) {
            $this->app->setLocale($lang);
            $this->router->addGlobalArg('lang', $lang);
        }
    }
}
