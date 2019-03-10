<?php

declare(strict_types=1);

/**
 * Copyright by the CaptainHook-GrumPHP Contributors
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\GrumPHP\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use function getcwd;
use SplFileInfo;

class Installer implements PluginInterface, EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => [
                ['prepareForPackage', 99],
                ['installPackage'],
            ],
            PackageEvents::POST_PACKAGE_UPDATE => [
                ['prepareForPackage', 99,],
                ['installPackage'],
            ],
            PackageEvents::PRE_PACKAGE_UNINSTALL => [
                ['prepareForPackage', 99],
            ],
            ScriptEvents::POST_INSTALL_CMD => [
                ['prepareForScript', 99],
                ['installScript'],
            ],
            ScriptEvents::POST_UPDATE_CMD => [
                ['prepareForScript', 99],
                ['installScript'],
            ],
        ];

    }
    public function prepareForScript(Event $event) : void
    {
        $dispatcher = $event->getComposer()->getEventDispatcher();
        $modifier = new EventDispatcherModifier();
        $modifier($dispatcher);
    }

    public function prepareForPackage(PackageEvent $event) : void
    {
        $dispatcher = $event->getComposer()->getEventDispatcher();
        $modifier = new EventDispatcherModifier();
        $modifier($dispatcher);
    }

    public static function installScript(Event $event) : void
    {
        $adder = new GrumPHPAdder(new SplFileInfo(getcwd() . '/captainhook.json'));
        $adder();
    }

    public static function installPackage(PackageEvent $event) : void
    {
        $adder = new GrumPHPAdder(new SplFileInfo(getcwd() . '/captainhook.json'));
        $adder();
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        // Intentionally do nothing.
    }
}