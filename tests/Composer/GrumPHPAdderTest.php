<?php
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\GrumPHPTest\Composer;

use CaptainHook\GrumPHP\Composer\GrumPHPAdder;
use function copy;
use function file_get_contents;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class GrumPHPAdderTest extends TestCase
{
    /** @dataProvider invokationWitExistingFileProvider */
    public function testInvokationWithExistingFile($filename)
    {
        $file = __DIR__ . '/_assets/' . $filename;

        copy($file . '.pre', $file);


        $adder = new GrumPHPAdder(new SplFileInfo($file));

        $adder();

        $this->assertEquals(file_get_contents($file . '.base'), file_get_contents($file));
        unlink($file);
    }

    public function invokationWitExistingFileProvider() : array
    {
        return [
            ['test.json'],
            ['test3.json'],
        ];
    }

    /** @dataProvider invokationWithNonExistingFileProvider */
    public function testInvokationWithNonExistingFile($filename)
    {
        $file = __DIR__ . '/_assets/' . $filename;

        $adder = new GrumPHPAdder(new SplFileInfo($file));

        $adder();

        $this->assertEquals(file_get_contents($file . '.base'), file_get_contents($file));

        unlink($file);
    }

    public function invokationWithNonExistingFileProvider() : array
    {
        return [
            ['test2.json'],
        ];
    }
}
