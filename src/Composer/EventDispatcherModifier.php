<?php

declare(strict_types=1);

/**
 * Copyright by the CaptainHook-GrumPHP Contributors
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\GrumPHP\Composer;

use Closure;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\PackageEvents;
use Composer\Script\ScriptEvents;
use GrumPHP\Composer\GrumPHPPlugin;

class EventDispatcherModifier
{
    const GRUMPHP_COMMAND = 'GrumPHP\Composer\DevelopmentIntegrator::integrate';

    public function __invoke(EventDispatcher $dispatcher) : void
    {
        $listenerThief = Closure::bind(function & (EventDispatcher $dispatcher) {
            return $dispatcher->listeners;
        }, null, $dispatcher);

        $listeners = &$listenerThief($dispatcher);

        $this->removeCommandFromListener(ScriptEvents::POST_INSTALL_CMD, $listeners);
        $this->removeCommandFromListener(ScriptEvents::POST_UPDATE_CMD, $listeners);
        $this->removeCommandFromListener(PackageEvents::POST_PACKAGE_INSTALL, $listeners);
        $this->removeCommandFromListener(PackageEvents::POST_PACKAGE_UPDATE, $listeners);
    }

    private function removeCommandFromListener(string $listener, array & $listeners) : void
    {
        if (! isset($listeners[$listener])) {
            return;
        }

        foreach ($listeners[$listener] as $priority => $events) {
            foreach ($events as $key => $event) {
                if ($event === self::GRUMPHP_COMMAND) {
                    unset($listeners[$listener][$priority][$key]);
                }

                if (is_array($event) && $event[0] instanceof GrumPHPPlugin) {
                    unset($listeners[$listener][$priority][$key]);
                }
            }
        }
    }
}