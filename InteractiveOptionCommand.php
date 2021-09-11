<?php

namespace Ella7\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * Extension of Symfony Command making it easier to interactively request option values
 *
 * @author Ryan Packer <ryan.packer@gmail.com>
 */
class InteractiveOptionCommand extends Command
{

  /**
   * Do not use interaction to set option value. This is here only for completeness in the case
   * that someone wants to declare all options as "InteractiveOptions," but doesn't want the
   * functionality in all cases.
   */
  public const INTERACTION_NONE = 0;

  /**
   * Only ask for user input in the case where the option is not previously set on the commmand line
   */
  public const INTERACTION_UNSET_ONLY = 1;

  /**
   * Always ask for user input. If option has been previously set, use that value as the suggested
   * answer in the interaction.
   */
  public const INTERACTION_ALWAYS = 2;

  protected $interactive_options = [];

  /**
   * Adds interactivity for the specified option.
   *
   * @param string      $name               The name of the option for which we're adding interactivity
   * @param int         $mode               The interactivity mode: One of the InteractiveOptionCommand::INTERACTION_* constants
   * @param Question    $question           A question object which will be used to ask for input in setting the option interactively
   * @param string      $suggested_answer   An answer to suggest to the end user during interaction - a suggested prompt
   *
   * @throws InvalidArgumentException       If option $name doesn't exist
   *
   * @return $this
   */
  protected function addInteractivityForOption(string $name, int $mode, Question $question, string $suggested_answer = '')
  {
    if(!$this->getDefinition()->hasOption($name)) {
      throw new InvalidArgumentException(sprintf('Cannot add interactivity to an option that has not been created. The "--%s" option does not exist.', $name));
    }

    // TODO: at some point it may be useful to create an InteractiveOption class - will use associative array for now.
    $this->interactive_options[] = [
      'name'              => $name,
      'mode'              => $mode,
      'question'          => $question,
      'suggested_answer'  => $suggested_answer
    ];
  }

  /**
   * Interacts with the user. @see Command::interact()
   *
   * This method will request interactive setting of options. If additional interaction is desired
   * in classes which extend this class, be sure to call parent::interact() if overwriting this
   * method.
   */
  protected function interact(InputInterface $input, OutputInterface $output)
  {
    $helper = $this->getHelper('question');
    foreach ($this->interactive_options as $interactive_option) {
      if($this->optionRequiresInteraction($interactive_option, $input)){
        $input->setOption(
          $interactive_option['name'],
          $helper->ask($input, $output, $interactive_option['question'])
        );
      }
    }
  }

  // TODO: This should be a method of the InteractiveOption class if/when that is created
  protected function optionRequiresInteraction($interactive_option, InputInterface $input)
  {
    $name = $interactive_option['name'];
    $mode = $interactive_option['mode'];
    if(!$this->getDefinition()->hasOption($name)) {
      throw new InvalidArgumentException(sprintf('The "--%s" option does not exist.', $name));
    }
    return (
      (self::INTERACTION_ALWAYS === $mode) ||
      (self::INTERACTION_UNSET_ONLY === $mode && !$input->getOption($name))
    );
  }
}
