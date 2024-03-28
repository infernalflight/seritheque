<?php

namespace App\Command;

use App\Repository\SerieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'serie:update',
    description: 'Commande pour mettre à jour ma base de séries',
)]
class SerieUpdateCommand extends Command
{

    protected SerieRepository $serieRepository;

    public function __construct(SerieRepository $serieRepository)
    {
        $this->serieRepository = $serieRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('status', InputArgument::OPTIONAL, 'Statut des séries à update');
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $status = $input->getArgument('status');

        if ($status) {
            $io->note(sprintf('You passed an argument: %s', $status));
        }

        $q = $this->serieRepository->createQueryBuilder('s')
            ->andWhere('s.firstAirDate < :date')
            ->setParameter(':date', (new \DateTime("- 20 year"))->format('y-m-d'));

        if ($status) {
            $q->andWhere('s.status = :status')
                ->setParameter(':status', $status);
        }

        $series = $q->getQuery()->getResult();

        $nb = count($series);

        if ($nb === 0) {
            $io->warning("Rien à supprimer aujourd'hui, désolé");

            return Command::INVALID;
        }

        $confirm = $io->confirm('Etes vous sûr de supprimer ' . $nb . ' séries ?', false);

        if (!$confirm) {
            $io->warning('Pas d\'opération aujourd\'hui, salut');
            return Command::SUCCESS;
        }

        $em = $this->serieRepository->getEntityManager();

        foreach ($series as $serie) {
            $em->remove($serie);
        }

        $em->flush();

        $io->success('Bravo, Vous avez supprimé ' . $nb . ' series');

        return Command::SUCCESS;
    }
}
