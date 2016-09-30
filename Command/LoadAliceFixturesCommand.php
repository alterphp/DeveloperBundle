<?php

namespace AlterPHP\DeveloperBundle\Command;

use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

class LoadAliceFixturesCommand extends LoadDataFixturesDoctrineCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('alice:fixtures:load');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $doctrine \Doctrine\Common\Persistence\ManagerRegistry */
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager($input->getOption('em'));

        if ($input->isInteractive() && !$input->getOption('append')) {
            $questionHelper = $this->getHelperSet()->get('question');
            $question = new ConfirmationQuestion(
                'Careful, database will be purged. Do you want to continue Y/N ?',
                false
            );

            if (!$questionHelper->ask($input, $output, $question)) {
                return;
            }
        }

        $files = $input->getOption('fixtures');
        $fixtures = array();

        if ($files) {
            $fixtures = is_array($files) ? $files : array($files);
        } else {
            $paths = array();
            foreach ($this->getApplication()->getKernel()->getBundles() as $bundle) {
                $paths[] = $bundle->getPath().'/DataFixtures/ORM';
            }

            $finder = new Finder();
            foreach ($paths as $path) {
                if (is_dir($path)) {
                    foreach ($finder->in($path)->name('*.yml') as $file) {
                        $fixtures[] = $file->getRealpath();
                    }
                }
            }
        }

        $fixtures = array_unique($fixtures);

        if (empty($fixtures)) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }

        if (!$input->getOption('append')) {
            $purger = new ORMPurger($em);
            $purger->setPurgeMode($input->getOption('purge-with-truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
            $purger->purge();
        }

        sort($fixtures);

        \Nelmio\Alice\Fixtures::load($fixtures, $em, array(
            'logger' => function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            },
        ));
    }
}
