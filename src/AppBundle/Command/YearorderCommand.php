<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Commande;
use AppBundle\Entity\ProductPackage;
use AppBundle\Entity\UserOrderByCategory;


class YearorderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('yearorder')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $commandes =  $em->getRepository(Commande::class)->findAll();
        foreach ($commandes as $commande) {
            $commande->setYearPaquetage(2018);
            $em->persist($commande);
            $em->flush();
        }
        $productpackages = $em->getRepository(ProductPackage::class)->findAll();
        foreach($productpackages as $productpackage) {
            $productpackage->setYearPaquetage(2018);
            $em->persist($productpackage);
            $em->flush();
        }
        $userOrders = $em->getRepository(UserOrderByCategory::class)->findAll();
        foreach($userOrders as $userOrder) {
            $userOrder->setYearPaquetage(2018);
            $em->persist($userOrder);
            $em->flush();
        }

        $output->writeln('Command');
    }

}
