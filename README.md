# console-command
This project attempts to extend the Symfony console-command with the specific goal of making it easier to interactively ask the user for inputs to command options. Functionality is rather basic, but helps avoid significant code repetition.

Still very much in dev/alpha state, but the project has been updated to work with Symfony 5. To use, fork the repo, and add the following to composer.json:
```
"require": {
  "ella7/console-command": "dev-dev"
},
"repositories": [
  {
    "type": "vcs",
    "url":  "https://github.com/ella7/console-command.git"
  }
]
```

run `composer update` to install. Here's an example of how to use:

```
<?php
namespace App\Command;

use Ella7\Console\Command\InteractiveOptionCommand;
use Symfony\Component\Console\Question\Question;

class TestCommand extends InteractiveOptionCommand
{
  protected static $defaultName = 'gpstools:test';

  protected function configure()
  {
    $this
      ->setDescription('Test command')
      ->addOption(
        'intopt',
        null,
        InputOption::VALUE_OPTIONAL,
        'just testing here',
        false
      )
    ;
    $question = new Question('This is the interactive question: ' . "\n> ");
    $this->addInteractivityForOption('intopt', self::INTERACTION_ALWAYS, $question);
  }
}
```
