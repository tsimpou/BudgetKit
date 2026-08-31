<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

abstract class Controller
{
    /** @var array<string, bool> */
    private static array $mobileViewExists = [];

    /**
     * Return the mobile view if the request is from a mobile device
     * and the mobile view exists, otherwise return the desktop view.
     */
    protected function mobileView(string $view, array $data = []): View
    {
        $isMobile = request()->attributes->get('isMobile', false);

        if ($isMobile) {
            $mobileView = 'mobile.'.$view;

            if (! array_key_exists($mobileView, self::$mobileViewExists)) {
                self::$mobileViewExists[$mobileView] = view()->exists($mobileView);
            }

            if (self::$mobileViewExists[$mobileView]) {
                return view($mobileView, $data);
            }
        }

        return view($view, $data);
    }
}
