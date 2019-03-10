<?php
/**
 * Copyright by the CaptainHook-GrumPHP Contributors
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\GrumPHPTest\Composer;

use CaptainHook\GrumPHP\Composer\EventDispatcherModifier;
use Closure;
use Composer\Composer;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Mockery as M;
use PHPUnit\Framework\TestCase;

class EventDispatcherModifierTest extends TestCase
{

    /** @dataProvider removeGrumPhpFromEventDispatcherProvider */
    public function testRemoveGrumPHPFromEventDispatcher(string $command)
    {
        /** @var Composer $composer */
        $composer = M::mock(Composer::class);
        /** @var IOInterface $ioInterface */
        $ioInterface = M::mock(IOInterface::class);
        $dispatcher = new EventDispatcher($composer, $ioInterface);

        $modifier = new EventDispatcherModifier();

        $dispatcher->addListener($command, 'Bar');
        $dispatcher->addListener($command, EventDispatcherModifier::GRUMPHP_COMMAND);

        self::assertContains(
            EventDispatcherModifier::GRUMPHP_COMMAND,
            $this->getListenersForCommand($command, $dispatcher)
        );

        $modifier($dispatcher);

        self::assertNotContains(
            EventDispatcherModifier::GRUMPHP_COMMAND,
            $this->getListenersForCommand($command, $dispatcher)
        );
    }

    public function removeGrumPhpFromEventDispatcherProvider() : array
    {
        return [
            ['post-install-cmd'],
            ['post-update-cmd'],
        ];
    }

    private function getListenersForCommand(string $command, EventDispatcher $dispatcher, int $priority = 0) : array
    {
        $thief = Closure::bind(function & (EventDispatcher $dispatcher) {
            return $dispatcher->listeners;
        }, null, $dispatcher);

        $listeners = $thief($dispatcher);

        return $listeners[$command][$priority];
    }
}
