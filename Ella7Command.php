<?php

namespace Ella7\Console\Command;

use Symfony\Component\Console\Command\Command;

class Ella7Command extends Command
{

  protected function getOptionalArgument($arg_key, $arg_desc, $arg_default, $input, $output)
  {
    $arg = $input->getArgument($arg_key);
    $arg_default = $arg ? $arg : $arg_default;
    $arg = $this->formattedAsk($output, $arg_desc, $arg_default);
    return $arg;
  }

  protected function formattedAsk($output, $ask, $default)
  {
    $dialog = $this->getHelperSet()->get('dialog');

    return $dialog->ask(
      $output,
      "<info>$ask</info> [<comment>$default</comment>]: ",
      $default
    );
  }
}
