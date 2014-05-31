<?php

namespace AlterPHP\DeveloperBundle\Command;

use AlterPHP\DeveloperBundle\Generator\DoctrineEntityGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineEntityCommand as BaseCommand;

/**
 * Initializes a Doctrine entity inside a bundle.
 */
class GenerateDoctrineEntityCommand extends BaseCommand
{
    protected function createGenerator()
    {
        return new DoctrineEntityGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('doctrine'));
    }
}
