<?php
/**
 * Created by PhpStorm.
 * User: a.roslik
 * Date: 11/2/16 002
 * Time: 5:02 PM
 */

namespace Rikby\MergeXml\Command;

use Rikby\Console\Command\AbstractCommand;
use Rikby\MergeXml\Merger;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class console command
 *
 * @package Rikby\MergeXml\Command
 */
class MergeCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->writeXml(
            $this->mergeXmlFiles()
        );

        return 0;
    }

    /**
     * Configure command
     */
    protected function configureInput()
    {
        $this->addArgument(
            'source_xml',
            InputArgument::REQUIRED,
            'Path to source XML file.'
        );
        $this->addArgument(
            'update_xml',
            InputArgument::IS_ARRAY + InputArgument::REQUIRED,
            'Path to update XML file/s.'
        );
        $this->addOption(
            'collection-xpath',
            'c',
            InputOption::VALUE_IS_ARRAY + InputOption::VALUE_REQUIRED,
            'XML path to nodes which are collections.'
        );
    }

    /**
     * Configure command
     */
    protected function configureCommand()
    {
        $this->setName('merge');
        $this->setHelp(
            'Text for help.'
        );
        $this->setDescription(
            'Text for help.'
        );
    }

    /**
     * Merge XML files
     *
     * @return \SimpleXMLElement
     */
    protected function mergeXmlFiles()
    {
        $merger = new Merger();

        foreach ($this->input->getOption('collection-xpath') as $xpath) {
            $merger->addCollectionNode($xpath);
        }

        return $merger->merge(
            $this->input->getArgument('source_xml'), $this->input->getArgument('update_xml'), true
        );
    }

    /**
     * Write to output content of SimpleXML object
     *
     * @param \SimpleXMLElement $xml
     * @return string
     */
    protected function writeXml($xml)
    {
        return $this->output->writeln(
            $xml->asXML()
        );
    }
}
