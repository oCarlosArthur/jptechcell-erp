<?php

require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController
{
    public function index(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php');
            exit;
        }

        $dashboardModel = new Dashboard();

        $totalProdutos =
            $dashboardModel->totalProdutos();

        $estoqueBaixo =
            $dashboardModel->estoqueBaixo();

        $ordensEmAndamento =
            $dashboardModel->ordensEmAndamento();

        $ordensProntas =
            $dashboardModel->ordensProntas();

        $ordensRecebidasHoje =
            $dashboardModel->ordensRecebidasHoje();

        $aguardandoAprovacao =
            $dashboardModel->aguardandoAprovacao();

        $aguardandoPeca =
            $dashboardModel->aguardandoPeca();

        $emManutencao =
            $dashboardModel->emManutencao();

        $ultimasOrdens =
            $dashboardModel->ultimasOrdensServico();

        $ultimasVendas =
            $dashboardModel->ultimasVendas();

        require_once __DIR__
            . '/../views/dashboard.php';
    }
}