<?php

namespace Ella7\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

/**
 * Extension of Symfony Command making it easier to interactively request option values
 *
 * @author Ryan Packer <ryan.packer@gmail.com>
 */
class Ella7Command extends Command
{

  const INTERACTION_NONE        = 0;
  const INTERACTION_UNSET_ONLY  = 1;
  const INTERACTION_ALL         = 2;

  protected $interactive_options = [];

  /**
   * Adds an option to the command to allow user to set level of option interaction.
   */
  protected function configure()
  {
    $this
      ->addOption(
        'option-interaction',
        'i',
        InputOption::VALUE_REQUIRED,
        'Interaction level for command options: 0=none, 1=unset options only, 2=all options',
        Ella7Command::INTERACTION_NONE
      )
    ;
  }


  protected function interact(InputInterface $input, OutputInterface $output)
  {
    $interactive_options = $this->getInteractiveOptions($input, $output);
    foreach ($interactive_options as $option_name) {
      $this->setOptionInteractively($option_name, $input, $output);
    }
  }

  protected function getInteractiveOptions(InputInterface $input, OutputInterface $output)
  {
    $interactive_options = [];
    $starting_interactive_options = $this->interactive_options ?
      $this->interactive_options : array_keys($input->getOptions());

    switch ($input->getOption('option-interaction')) {
      case Ella7Command::INTERACTION_NONE:
        break;

      case Ella7Command::INTERACTION_UNSET_ONLY:
        foreach ($starting_interactive_options as $option_name) {
          if(!$input->getOption($option_name)){
            $interactive_options[] = $option_name;
          }
        }
        break;

      case Ella7Command::INTERACTION_ALL:
        $interactive_options = $starting_interactive_options;
        break;
    }
    return $interactive_options;
  }

  protected function setInteractiveOptions(array $options)
  {
    // TODO: validate the provided options
    $this->interactive_options = $options;
  }

  protected function setOptionInteractively(string $option_name, $option_prompt, $option_default, InputInterface $input, OutputInterface $output)
  {
    $helper = $this->getHelper('question');
    $question = new Question($option_prompt.': ', $option_default);
    $input->setOption($option_name, $helper->ask($input, $output, $question));
  }

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
