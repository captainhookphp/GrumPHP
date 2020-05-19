# Captainhook - GrumPHP

Integrate [GrumPHP](https://github.com/phpro/grumphp) into CaptainHook.

This package allows you to use GrumPHP as one of the hooks for CaptainHook.

## Installation:

```bash
composer require captainhook/grumphp
```

That should set up everything as you need it. You might want to tweak your 
`grumphp.yml`-File according to your needs. More details on that can be found in the
[GrumPHP-Documentation](https://github.com/phpro/grumphp#configuration)

Your `captainhook.json` should afterwards cnotain the following sections:
```json
{
    "pre-commit": {
        "enabled": true,
        "actions": [{
            "action" : "DIFF=$(git -c diff.mnemonicprefix=false --no-pager diff -r -p -m -M --full-index --no-color --staged | cat); printf \"%s\n\" \"${DIFF}\" | exec ./vendor/bin/grumphp git:pre-commit --skip-success-output",
            "options" : []
        }]
    },
    "commit-msg" : {
        "enabled" : true,
        "actions" : [{
            "action" : "GIT_USER=$(git config user.name);GIT_EMAIL=$(git config user.email);COMMIT_MSG_FILE={$FILE};DIFF=$(git -c diff.mnemonicprefix=false --no-pager diff -r -p -m -M --full-index --no-color --staged | cat);printf \"%s\n\" \"${DIFF}\" | exec ./vendor/bin/grumphp git:commit-msg \"--git-user=$GIT_USER\" \"--git-email=$GIT_EMAIL\" \"$COMMIT_MSG_FILE\"",
            "options" : []
        }]
    }
}
```
      
