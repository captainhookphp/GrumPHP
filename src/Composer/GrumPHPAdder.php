<?php

declare(strict_types=1);

/**
 * Copyright by the CaptainHook-GrumPHP Contributors
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\GrumPHP\Composer;

use function array_unshift;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use SplFileInfo;

class GrumPHPAdder
{
    const PRE_COMMIT_CONFIG = [
        'action' => 'DIFF=$(git -c diff.mnemonicprefix=false --no-pager diff -r -p -m -M --full-index --no-color --staged | cat); printf "%s\n" "${DIFF}" | exec ./vendor/bin/grumphp git:pre-commit --skip-success-output',
        'options' => [],
    ];

    const COMMIT_MSG_CONFIG = [
        'action' => 'GIT_USER=$(git config user.name);GIT_EMAIL=$(git config user.email);COMMIT_MSG_FILE={$FILE};DIFF=$(git -c diff.mnemonicprefix=false --no-pager diff -r -p -m -M --full-index --no-color --staged | cat);printf "%s\n" "${DIFF}" | exec ./vendor/bin/grumphp git:commit-msg "--git-user=$GIT_USER" "--git-email=$GIT_EMAIL" "$COMMIT_MSG_FILE"',
        'options' => [],
    ];

    private $configfile;

    public function __construct(SplFileInfo $configfile)
    {
        $this->configfile = $configfile;
    }

    public function __invoke()
    {
        $config = ['pre-commit' => ['actions' => []], 'commit-msg' => ['actions' => []]];
        if ($this->configfile->isFile()) {
            $config = json_decode(file_get_contents($this->configfile->getPathname()), true);
        }

        if (! in_array(self::PRE_COMMIT_CONFIG, $config['pre-commit']['actions'])) {
            array_unshift($config['pre-commit']['actions'], self::PRE_COMMIT_CONFIG);
            $config['pre-commit']['enabled'] = true;
        }

        if (! in_array(self::COMMIT_MSG_CONFIG, $config['commit-msg']['actions'])) {
            array_unshift($config['commit-msg']['actions'], self::COMMIT_MSG_CONFIG);
            $config['commit-msg']['enabled'] = true;
        }

        file_put_contents($this->configfile->getPathname(), json_encode($config,  JSON_PRETTY_PRINT, 255));
    }
}