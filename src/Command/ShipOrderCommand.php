<?php
declare(strict_types=1);

namespace App\Command;

use App\Data\Order;
use App\Repository\BuyerRepositoryInterface;
use App\ShippingServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:ship_order', description: 'Ship an order and get a tracking number')]
class ShipOrderCommand extends Command
{
    public function __construct(
        private readonly ShippingServiceInterface $shippingService,
        private readonly BuyerRepositoryInterface $buyerRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('orderID', InputArgument::REQUIRED, 'ID of the order to ship')
            ->addArgument('buyerID', InputArgument::REQUIRED, 'ID of the buyer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $orderID = (int)$input->getArgument('orderID');
            $buyerID = (int)$input->getArgument('buyerID');

            $order = new Order($orderID);
            $buyer = $this->buyerRepository->findBuyerById($buyerID);

            $trackingNumber = $this->shippingService->ship($order, $buyer);

            $output->writeln(sprintf('<info>Order shipped! Tracking Number: %s</info>', $trackingNumber));
            return Command::SUCCESS;

        } catch (\RuntimeException $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $output->writeln('<error>Unexpected error occurred.</error>');
            return Command::FAILURE;
        }
    }
}