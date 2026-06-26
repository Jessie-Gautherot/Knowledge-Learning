<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-plantuml',
    description: 'Generate PlantUML class diagram from Doctrine entities'
)]
class GeneratePlantumlCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $metadataList = $this->em->getMetadataFactory()->getAllMetadata();

        $uml = ["@startuml", "skinparam classAttributeIconSize 0", ""];

        foreach ($metadataList as $metadata) {
            /** @var ClassMetadata $metadata */
            $className = $metadata->getReflectionClass()->getShortName();

            $uml[] = "class {$className} {";

            foreach ($metadata->fieldMappings as $field => $mapping) {
                $type = $mapping['type'] ?? 'mixed';
                $uml[] = "  +{$field} : {$type}";
            }

            $uml[] = "}";
            $uml[] = "";
        }

        foreach ($metadataList as $metadata) {
            $source = $metadata->getReflectionClass()->getShortName();

            foreach ($metadata->associationMappings as $field => $assoc) {
                $target = (new \ReflectionClass($assoc['targetEntity']))->getShortName();

                $left = match ($assoc['type']) {
                    ClassMetadata::ONE_TO_ONE => '"1"',
                    ClassMetadata::MANY_TO_ONE => '"*"',
                    ClassMetadata::ONE_TO_MANY => '"1"',
                    ClassMetadata::MANY_TO_MANY => '"*"',
                    default => '',
                };

                $right = match ($assoc['type']) {
                    ClassMetadata::ONE_TO_ONE => '"1"',
                    ClassMetadata::MANY_TO_ONE => '"1"',
                    ClassMetadata::ONE_TO_MANY => '"*"',
                    ClassMetadata::MANY_TO_MANY => '"*"',
                    default => '',
                };

                $uml[] = "{$source} {$left} -- {$right} {$target} : {$field}";
            }
        }

        $uml[] = "";
        $uml[] = "@enduml";

        file_put_contents('var/doctrine-class-diagram.puml', implode(PHP_EOL, $uml));

        $output->writeln('<info>Generated: var/doctrine-class-diagram.puml</info>');

        return Command::SUCCESS;
    }
}
