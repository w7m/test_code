<?php
/**
 * Created by PhpStorm.
 * User: jghada
 * Date: 10/01/2019
 * Time: 08:10
 */

namespace App\Command;


use App\Repository\ExpertRepository;
use App\Service\BonRemboursementService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePaiementCommand extends Command
{
    private $bonRemboursement;
    private $expertRepository;
    private $manager;

    public function __construct(?string $name = null, BonRemboursementService $bonRemboursementService, ExpertRepository $expertRepository, ObjectManager $manager)
    {
        parent::__construct($name);
        $this->bonRemboursement = $bonRemboursementService;
        $this->expertRepository = $expertRepository;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName('app:creer-paiement')
            ->setDescription('Creation de bon de remboursement pour chaque expert')
            ->setHelp('Cette commande te permet de créer un bon de remboursement pour chaque expert...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $experts = $this->expertRepository->findAll();
        foreach ($experts as $expert) {

            $this->bonRemboursement->creerPaiement($expert);


        }
        $output->writeln([
            'Bon remboursement crée',
            '============',
            '',
        ]);

    }
}