<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/Financeiro.php';

class Relatorio
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Database::conectar();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE VENDAS
    |--------------------------------------------------------------------------
    */

    public function gerarRelatorioVendas(
        string $dataInicio,
        string $dataFim
    ): array {
        $dataInicio = $this->validarData(
            $dataInicio,
            'Data inicial inválida.'
        );

        $dataFim = $this->validarData(
            $dataFim,
            'Data final inválida.'
        );

        if ($dataInicio > $dataFim) {
            throw new Exception(
                'A data inicial não pode ser maior que a data final.'
            );
        }

        $inicioPeriodo =
            $dataInicio . ' 00:00:00';

        $fimPeriodo = new DateTime(
            $dataFim . ' 00:00:00'
        );

        $fimPeriodo->modify('+1 day');

        $fimPeriodoFormatado =
            $fimPeriodo->format(
                'Y-m-d H:i:s'
            );

        $vendas = $this->buscarVendas(
            $inicioPeriodo,
            $fimPeriodoFormatado
        );

        $resumo = $this->calcularResumoVendas(
            $vendas
        );

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'vendas' => $vendas,
            'resumo' => $resumo
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCA DAS VENDAS
    |--------------------------------------------------------------------------
    */

    private function buscarVendas(
        string $dataInicio,
        string $dataFim
    ): array {
        $sql = "
            SELECT
                vendas.id,
                vendas.numero_venda,
                vendas.cliente_id,
                vendas.subtotal,
                vendas.desconto,
                vendas.total,
                vendas.forma_pagamento,
                vendas.status,
                vendas.criado_em,

                clientes.nome AS cliente_nome,

                ordens_servico.id
                    AS ordem_servico_id,

                ordens_servico.numero_os,

                ordens_servico.aparelho_marca,

                ordens_servico.aparelho_modelo,

                ordens_servico.valor_total
                    AS valor_ordem_servico

            FROM vendas

            LEFT JOIN clientes
                ON clientes.id =
                    vendas.cliente_id

            LEFT JOIN ordens_servico
                ON ordens_servico.venda_id =
                    vendas.id

            WHERE
                vendas.status = 'concluida'

                AND vendas.criado_em >=
                    :data_inicio

                AND vendas.criado_em <
                    :data_fim

            ORDER BY
                vendas.criado_em DESC,
                vendas.id DESC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);

        $vendas = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        foreach ($vendas as &$venda) {
            $venda['id'] =
                (int) $venda['id'];

            $venda['subtotal'] =
                round(
                    (float) $venda['subtotal'],
                    2
                );

            $venda['desconto'] =
                round(
                    (float) $venda['desconto'],
                    2
                );

            $venda['total'] =
                round(
                    (float) $venda['total'],
                    2
                );

            $venda['cliente_nome'] =
                $venda['cliente_nome']
                    ?: 'Cliente não informado';

            $venda['itens'] =
                $this->buscarItensVenda(
                    (int) $venda['id']
                );

            $venda['ordem_servico'] =
                $this->montarOrdemServico(
                    $venda
                );
        }

        unset($venda);

        return $vendas;
    }

    /*
    |--------------------------------------------------------------------------
    | ITENS DA VENDA
    |--------------------------------------------------------------------------
    */

    private function buscarItensVenda(
        int $vendaId
    ): array {
        $sql = "
            SELECT
                itens_venda.id,
                itens_venda.produto_id,
                itens_venda.quantidade,
                itens_venda.preco_unitario,
                itens_venda.custo_unitario,
                itens_venda.total,

                produtos.nome
                    AS produto_nome,

                produtos.marca
                    AS produto_marca,

                produtos.codigo_barras

            FROM itens_venda

            INNER JOIN produtos
                ON produtos.id =
                    itens_venda.produto_id

            WHERE
                itens_venda.venda_id =
                    :venda_id

            ORDER BY
                produtos.nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':venda_id' => $vendaId
        ]);

        $itens = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        foreach ($itens as &$item) {
            $quantidade =
                (int) $item['quantidade'];

            $precoUnitario =
                (float) $item[
                    'preco_unitario'
                ];

            $custoUnitario =
                (float) $item[
                    'custo_unitario'
                ];

            $total =
                (float) $item['total'];

            $item['id'] =
                (int) $item['id'];

            $item['produto_id'] =
                (int) $item['produto_id'];

            $item['quantidade'] =
                $quantidade;

            $item['preco_unitario'] =
                round(
                    $precoUnitario,
                    2
                );

            $item['custo_unitario'] =
                round(
                    $custoUnitario,
                    2
                );

            $item['total'] =
                round(
                    $total,
                    2
                );

            $item['custo_total'] =
                round(
                    $custoUnitario
                    * $quantidade,
                    2
                );

            $item['lucro'] =
                round(
                    $total
                    - (
                        $custoUnitario
                        * $quantidade
                    ),
                    2
                );
        }

        unset($item);

        return $itens;
    }

    /*
    |--------------------------------------------------------------------------
    | ORDEM DE SERVIÇO DA VENDA
    |--------------------------------------------------------------------------
    */

    private function montarOrdemServico(
        array $venda
    ): ?array {
        if (
            empty(
                $venda['ordem_servico_id']
            )
        ) {
            return null;
        }

        $marca = trim(
            (string) (
                $venda['aparelho_marca']
                ?? ''
            )
        );

        $modelo = trim(
            (string) (
                $venda['aparelho_modelo']
                ?? ''
            )
        );

        $aparelho = trim(
            $marca . ' ' . $modelo
        );

        return [
            'id' =>
                (int) $venda[
                    'ordem_servico_id'
                ],

            'numero_os' =>
                $venda['numero_os'],

            'aparelho' =>
                $aparelho !== ''
                    ? $aparelho
                    : 'Não informado',

            'valor' =>
                round(
                    (float) $venda[
                        'valor_ordem_servico'
                    ],
                    2
                )
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RESUMO DO RELATÓRIO
    |--------------------------------------------------------------------------
    */

    private function calcularResumoVendas(
        array $vendas
    ): array {
        $quantidadeVendas = 0;
        $quantidadeProdutos = 0;
        $quantidadeOrdensServico = 0;

        $subtotal = 0;
        $descontos = 0;
        $totalRecebido = 0;
        $totalProdutos = 0;
        $totalOrdensServico = 0;

        $formasPagamento = [];

        foreach ($vendas as $venda) {
            $quantidadeVendas++;

            $subtotal +=
                (float) $venda['subtotal'];

            $descontos +=
                (float) $venda['desconto'];

            $totalRecebido +=
                (float) $venda['total'];

            foreach (
                $venda['itens']
                as $item
            ) {
                $quantidadeProdutos +=
                    (int) $item['quantidade'];

                $totalProdutos +=
                    (float) $item['total'];
            }

            if (
                !empty(
                    $venda['ordem_servico']
                )
            ) {
                $quantidadeOrdensServico++;

                $totalOrdensServico +=
                    (float) $venda[
                        'ordem_servico'
                    ]['valor'];
            }

            $formaPagamento = trim(
                (string) $venda[
                    'forma_pagamento'
                ]
            );

            if ($formaPagamento === '') {
                $formaPagamento =
                    'Não informado';
            }

            if (
                !isset(
                    $formasPagamento[
                        $formaPagamento
                    ]
                )
            ) {
                $formasPagamento[
                    $formaPagamento
                ] = [
                    'quantidade' => 0,
                    'total' => 0
                ];
            }

            $formasPagamento[
                $formaPagamento
            ]['quantidade']++;

            $formasPagamento[
                $formaPagamento
            ]['total'] +=
                (float) $venda['total'];
        }

        foreach (
            $formasPagamento
            as &$forma
        ) {
            $forma['total'] =
                round(
                    (float) $forma['total'],
                    2
                );
        }

        unset($forma);

        ksort($formasPagamento);

        return [
            'quantidade_vendas' =>
                $quantidadeVendas,

            'quantidade_produtos' =>
                $quantidadeProdutos,

            'quantidade_ordens_servico' =>
                $quantidadeOrdensServico,

            'subtotal' =>
                round($subtotal, 2),

            'descontos' =>
                round($descontos, 2),

            'total_recebido' =>
                round($totalRecebido, 2),

            'total_produtos' =>
                round($totalProdutos, 2),

            'total_ordens_servico' =>
                round(
                    $totalOrdensServico,
                    2
                ),

            'formas_pagamento' =>
                $formasPagamento
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE PRODUTOS
    |--------------------------------------------------------------------------
    */

    public function gerarRelatorioProdutos(
        string $dataInicio,
        string $dataFim
    ): array {
        $dataInicio = $this->validarData(
            $dataInicio,
            'Data inicial inválida.'
        );

        $dataFim = $this->validarData(
            $dataFim,
            'Data final inválida.'
        );

        if ($dataInicio > $dataFim) {
            throw new Exception(
                'A data inicial não pode ser maior que a data final.'
            );
        }

        $inicioPeriodo =
            $dataInicio . ' 00:00:00';

        $fimPeriodo = new DateTime(
            $dataFim . ' 00:00:00'
        );

        $fimPeriodo->modify('+1 day');

        $fimPeriodoFormatado =
            $fimPeriodo->format(
                'Y-m-d H:i:s'
            );

        $produtos = $this->buscarRelatorioProdutos(
            $inicioPeriodo,
            $fimPeriodoFormatado
        );

        $resumo = $this->calcularResumoProdutos(
            $produtos
        );

        $topMaisVendidos = $this->ordenarProdutos(
            $produtos,
            'quantidade_vendida',
            10
        );

        $topMaisLucrativos = $this->ordenarProdutos(
            $produtos,
            'lucro',
            10
        );

        $produtosSemVenda = array_values(
            array_filter(
                $produtos,
                static function (array $produto): bool {
                    return (
                        (int) $produto[
                            'quantidade_vendida'
                        ]
                    ) === 0;
                }
            )
        );

        $produtosEstoqueBaixo = array_values(
            array_filter(
                $produtos,
                static function (array $produto): bool {
                    return in_array(
                        $produto['situacao_estoque'],
                        [
                            'estoque_baixo',
                            'sem_estoque'
                        ],
                        true
                    );
                }
            )
        );

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'produtos' => $produtos,
            'resumo' => $resumo,
            'top_mais_vendidos' =>
                $topMaisVendidos,
            'top_mais_lucrativos' =>
                $topMaisLucrativos,
            'produtos_sem_venda' =>
                $produtosSemVenda,
            'produtos_estoque_baixo' =>
                $produtosEstoqueBaixo
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCA DOS PRODUTOS E RESULTADOS DO PERÍODO
    |--------------------------------------------------------------------------
    */

    private function buscarRelatorioProdutos(
        string $dataInicio,
        string $dataFim
    ): array {
        $sql = "
            SELECT
                produtos.id,
                produtos.codigo_barras,
                produtos.nome,
                produtos.marca,
                produtos.quantidade_estoque
                    AS estoque_atual,
                produtos.estoque_minimo,
                produtos.preco_custo,
                produtos.preco_venda,

                COALESCE(
                    SUM(
                        CASE
                            WHEN vendas.id IS NOT NULL
                            THEN itens_venda.quantidade
                            ELSE 0
                        END
                    ),
                    0
                ) AS quantidade_vendida,

                COALESCE(
                    SUM(
                        CASE
                            WHEN vendas.id IS NOT NULL
                            THEN itens_venda.total
                            ELSE 0
                        END
                    ),
                    0
                ) AS faturamento,

                COALESCE(
                    SUM(
                        CASE
                            WHEN vendas.id IS NOT NULL
                            THEN (
                                itens_venda.custo_unitario
                                * itens_venda.quantidade
                            )
                            ELSE 0
                        END
                    ),
                    0
                ) AS custo_total

            FROM produtos

            LEFT JOIN itens_venda
                ON itens_venda.produto_id =
                    produtos.id

            LEFT JOIN vendas
                ON vendas.id =
                    itens_venda.venda_id

                AND vendas.status =
                    'concluida'

                AND vendas.criado_em >=
                    :data_inicio

                AND vendas.criado_em <
                    :data_fim

            WHERE
                produtos.ativo = 1

            GROUP BY
                produtos.id,
                produtos.codigo_barras,
                produtos.nome,
                produtos.marca,
                produtos.quantidade_estoque,
                produtos.estoque_minimo,
                produtos.preco_custo,
                produtos.preco_venda

            ORDER BY
                quantidade_vendida DESC,
                produtos.nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);

        $produtos = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        foreach ($produtos as &$produto) {
            $produto['id'] =
                (int) $produto['id'];

            $produto['estoque_atual'] =
                (int) $produto['estoque_atual'];

            $produto['estoque_minimo'] =
                (int) $produto['estoque_minimo'];

            $produto['quantidade_vendida'] =
                (int) $produto[
                    'quantidade_vendida'
                ];

            $produto['preco_custo'] =
                round(
                    (float) $produto[
                        'preco_custo'
                    ],
                    2
                );

            $produto['preco_venda'] =
                round(
                    (float) $produto[
                        'preco_venda'
                    ],
                    2
                );

            $produto['faturamento'] =
                round(
                    (float) $produto[
                        'faturamento'
                    ],
                    2
                );

            $produto['custo_total'] =
                round(
                    (float) $produto[
                        'custo_total'
                    ],
                    2
                );

            $produto['lucro'] =
                round(
                    $produto['faturamento']
                    - $produto['custo_total'],
                    2
                );

            $produto['margem_lucro'] =
                $produto['faturamento'] > 0
                    ? round(
                        (
                            $produto['lucro']
                            / $produto['faturamento']
                        ) * 100,
                        2
                    )
                    : 0.0;

            $produto['situacao_estoque'] =
                $this->definirSituacaoEstoque(
                    $produto['estoque_atual'],
                    $produto['estoque_minimo']
                );

            $produto['situacao_estoque_nome'] =
                $this->formatarSituacaoEstoque(
                    $produto['situacao_estoque']
                );

            $produto['marca'] =
                trim(
                    (string) (
                        $produto['marca']
                        ?? ''
                    )
                );

            if ($produto['marca'] === '') {
                $produto['marca'] =
                    'Não informada';
            }
        }

        unset($produto);

        return $produtos;
    }

    /*
    |--------------------------------------------------------------------------
    | RESUMO DO RELATÓRIO DE PRODUTOS
    |--------------------------------------------------------------------------
    */

    private function calcularResumoProdutos(
        array $produtos
    ): array {
        $totalCadastrados = count($produtos);
        $produtosVendidos = 0;
        $quantidadeVendida = 0;
        $produtosSemVenda = 0;
        $produtosEstoqueBaixo = 0;
        $produtosSemEstoque = 0;

        $faturamento = 0;
        $custoTotal = 0;
        $lucro = 0;
        $valorEstoqueCusto = 0;
        $valorEstoqueVenda = 0;

        foreach ($produtos as $produto) {
            $quantidadeProduto =
                (int) $produto[
                    'quantidade_vendida'
                ];

            if ($quantidadeProduto > 0) {
                $produtosVendidos++;
            } else {
                $produtosSemVenda++;
            }

            $quantidadeVendida +=
                $quantidadeProduto;

            $faturamento +=
                (float) $produto[
                    'faturamento'
                ];

            $custoTotal +=
                (float) $produto[
                    'custo_total'
                ];

            $lucro +=
                (float) $produto['lucro'];

            $estoqueAtual =
                (int) $produto[
                    'estoque_atual'
                ];

            $valorEstoqueCusto +=
                $estoqueAtual
                * (float) $produto[
                    'preco_custo'
                ];

            $valorEstoqueVenda +=
                $estoqueAtual
                * (float) $produto[
                    'preco_venda'
                ];

            if (
                $produto['situacao_estoque']
                === 'sem_estoque'
            ) {
                $produtosSemEstoque++;
                $produtosEstoqueBaixo++;
            } elseif (
                $produto['situacao_estoque']
                === 'estoque_baixo'
            ) {
                $produtosEstoqueBaixo++;
            }
        }

        $margemLucro =
            $faturamento > 0
                ? (
                    $lucro
                    / $faturamento
                ) * 100
                : 0;

        return [
            'total_cadastrados' =>
                $totalCadastrados,

            'produtos_vendidos' =>
                $produtosVendidos,

            'quantidade_vendida' =>
                $quantidadeVendida,

            'produtos_sem_venda' =>
                $produtosSemVenda,

            'produtos_estoque_baixo' =>
                $produtosEstoqueBaixo,

            'produtos_sem_estoque' =>
                $produtosSemEstoque,

            'faturamento' =>
                round($faturamento, 2),

            'custo_total' =>
                round($custoTotal, 2),

            'lucro' =>
                round($lucro, 2),

            'margem_lucro' =>
                round($margemLucro, 2),

            'valor_estoque_custo' =>
                round(
                    $valorEstoqueCusto,
                    2
                ),

            'valor_estoque_venda' =>
                round(
                    $valorEstoqueVenda,
                    2
                )
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ORDENAÇÃO DOS RANKINGS
    |--------------------------------------------------------------------------
    */

    private function ordenarProdutos(
        array $produtos,
        string $campo,
        int $limite
    ): array {
        $produtosComResultado =
            array_values(
                array_filter(
                    $produtos,
                    static function (
                        array $produto
                    ) use ($campo): bool {
                        return (
                            (float) (
                                $produto[$campo]
                                ?? 0
                            )
                        ) > 0;
                    }
                )
            );

        usort(
            $produtosComResultado,
            static function (
                array $produtoA,
                array $produtoB
            ) use ($campo): int {
                $valorA =
                    (float) (
                        $produtoA[$campo]
                        ?? 0
                    );

                $valorB =
                    (float) (
                        $produtoB[$campo]
                        ?? 0
                    );

                if ($valorA === $valorB) {
                    return strcmp(
                        (string) $produtoA['nome'],
                        (string) $produtoB['nome']
                    );
                }

                return $valorB <=> $valorA;
            }
        );

        return array_slice(
            $produtosComResultado,
            0,
            $limite
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SITUAÇÃO DO ESTOQUE
    |--------------------------------------------------------------------------
    */

    private function definirSituacaoEstoque(
        int $estoqueAtual,
        int $estoqueMinimo
    ): string {
        if ($estoqueAtual <= 0) {
            return 'sem_estoque';
        }

        if ($estoqueAtual <= $estoqueMinimo) {
            return 'estoque_baixo';
        }

        return 'normal';
    }

    private function formatarSituacaoEstoque(
        string $situacao
    ): string {
        $situacoes = [
            'normal' => 'Normal',
            'estoque_baixo' =>
                'Estoque baixo',
            'sem_estoque' =>
                'Sem estoque'
        ];

        return $situacoes[$situacao]
            ?? 'Não informada';
    }


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE ESTOQUE
    |--------------------------------------------------------------------------
    */

    public function gerarRelatorioEstoque(): array
    {
        $produtos = $this->buscarRelatorioEstoque();

        $resumo = $this->calcularResumoEstoque(
            $produtos
        );

        $estoqueBaixo = array_values(
            array_filter(
                $produtos,
                static function (array $produto): bool {
                    return in_array(
                        $produto['situacao_estoque'],
                        [
                            'estoque_baixo',
                            'sem_estoque'
                        ],
                        true
                    );
                }
            )
        );

        $semEstoque = array_values(
            array_filter(
                $produtos,
                static function (array $produto): bool {
                    return $produto['situacao_estoque']
                        === 'sem_estoque';
                }
            )
        );

        $maiorValorEstoque = $produtos;

        usort(
            $maiorValorEstoque,
            static function (
                array $produtoA,
                array $produtoB
            ): int {
                return $produtoB['valor_estoque_custo']
                    <=> $produtoA['valor_estoque_custo'];
            }
        );

        $maiorValorEstoque = array_slice(
            $maiorValorEstoque,
            0,
            10
        );

        return [
            'produtos' => $produtos,
            'resumo' => $resumo,
            'estoque_baixo' => $estoqueBaixo,
            'sem_estoque' => $semEstoque,
            'maior_valor_estoque' =>
                $maiorValorEstoque
        ];
    }

    private function buscarRelatorioEstoque(): array
    {
        $sql = "
            SELECT
                produtos.id,
                produtos.codigo_etiqueta,
                produtos.codigo_barras,
                produtos.nome,
                produtos.marca,
                produtos.quantidade_estoque,
                produtos.estoque_minimo,
                produtos.preco_custo,
                produtos.preco_venda

            FROM produtos

            WHERE
                produtos.ativo = 1

            ORDER BY
                produtos.nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute();

        $produtos = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        foreach ($produtos as &$produto) {
            $produto['id'] =
                (int) $produto['id'];

            $produto['quantidade_estoque'] =
                (int) $produto['quantidade_estoque'];

            $produto['estoque_minimo'] =
                (int) $produto['estoque_minimo'];

            $produto['preco_custo'] =
                round(
                    (float) $produto['preco_custo'],
                    2
                );

            $produto['preco_venda'] =
                round(
                    (float) $produto['preco_venda'],
                    2
                );

            $produto['valor_estoque_custo'] =
                round(
                    $produto['quantidade_estoque']
                    * $produto['preco_custo'],
                    2
                );

            $produto['valor_estoque_venda'] =
                round(
                    $produto['quantidade_estoque']
                    * $produto['preco_venda'],
                    2
                );

            $produto['lucro_potencial'] =
                round(
                    $produto['valor_estoque_venda']
                    - $produto['valor_estoque_custo'],
                    2
                );

            $produto['situacao_estoque'] =
                $this->definirSituacaoEstoque(
                    $produto['quantidade_estoque'],
                    $produto['estoque_minimo']
                );

            $produto['situacao_estoque_nome'] =
                $this->formatarSituacaoEstoque(
                    $produto['situacao_estoque']
                );

            $produto['marca'] = trim(
                (string) ($produto['marca'] ?? '')
            );

            if ($produto['marca'] === '') {
                $produto['marca'] = 'Não informada';
            }
        }

        unset($produto);

        return $produtos;
    }

    private function calcularResumoEstoque(
        array $produtos
    ): array {
        $totalProdutos = count($produtos);
        $totalUnidades = 0;
        $produtosEstoqueBaixo = 0;
        $produtosSemEstoque = 0;
        $valorEstoqueCusto = 0;
        $valorEstoqueVenda = 0;
        $lucroPotencial = 0;

        foreach ($produtos as $produto) {
            $totalUnidades +=
                (int) $produto['quantidade_estoque'];

            $valorEstoqueCusto +=
                (float) $produto['valor_estoque_custo'];

            $valorEstoqueVenda +=
                (float) $produto['valor_estoque_venda'];

            $lucroPotencial +=
                (float) $produto['lucro_potencial'];

            if (
                $produto['situacao_estoque']
                === 'sem_estoque'
            ) {
                $produtosSemEstoque++;
                $produtosEstoqueBaixo++;
            } elseif (
                $produto['situacao_estoque']
                === 'estoque_baixo'
            ) {
                $produtosEstoqueBaixo++;
            }
        }

        return [
            'total_produtos' => $totalProdutos,
            'total_unidades' => $totalUnidades,
            'produtos_estoque_baixo' =>
                $produtosEstoqueBaixo,
            'produtos_sem_estoque' =>
                $produtosSemEstoque,
            'valor_estoque_custo' =>
                round($valorEstoqueCusto, 2),
            'valor_estoque_venda' =>
                round($valorEstoqueVenda, 2),
            'lucro_potencial' =>
                round($lucroPotencial, 2)
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE CLIENTES
    |--------------------------------------------------------------------------
    */

    public function gerarRelatorioClientes(
        string $dataInicio,
        string $dataFim
    ): array {
        $dataInicio = $this->validarData(
            $dataInicio,
            'Data inicial inválida.'
        );

        $dataFim = $this->validarData(
            $dataFim,
            'Data final inválida.'
        );

        if ($dataInicio > $dataFim) {
            throw new Exception(
                'A data inicial não pode ser maior que a data final.'
            );
        }

        $inicio = $dataInicio . ' 00:00:00';

        $fim = new DateTime($dataFim . ' 00:00:00');
        $fim->modify('+1 day');

        $clientes = $this->buscarRelatorioClientes(
            $inicio,
            $fim->format('Y-m-d H:i:s')
        );

        $resumo = $this->calcularResumoClientes($clientes);

        $maisCompraram = $clientes;
        usort(
            $maisCompraram,
            static fn(array $a, array $b): int =>
                $b['total_gasto'] <=> $a['total_gasto']
        );

        $maisCompraram = array_slice(
            array_values(
                array_filter(
                    $maisCompraram,
                    static fn(array $cliente): bool =>
                        $cliente['total_gasto'] > 0
                )
            ),
            0,
            10
        );

        $maisFrequentes = $clientes;
        usort(
            $maisFrequentes,
            static fn(array $a, array $b): int =>
                $b['quantidade_compras']
                <=> $a['quantidade_compras']
        );

        $maisFrequentes = array_slice(
            array_values(
                array_filter(
                    $maisFrequentes,
                    static fn(array $cliente): bool =>
                        $cliente['quantidade_compras'] > 0
                )
            ),
            0,
            10
        );

        $semCompras = array_values(
            array_filter(
                $clientes,
                static fn(array $cliente): bool =>
                    $cliente['quantidade_compras'] === 0
            )
        );

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'clientes' => $clientes,
            'resumo' => $resumo,
            'mais_compraram' => $maisCompraram,
            'mais_frequentes' => $maisFrequentes,
            'sem_compras' => $semCompras
        ];
    }

    private function buscarRelatorioClientes(
        string $dataInicio,
        string $dataFim
    ): array {
        $sql = "
            SELECT
                clientes.id,
                clientes.tipo,
                clientes.nome,
                clientes.cpf_cnpj,
                clientes.telefone,
                clientes.whatsapp,
                clientes.email,
                clientes.cidade,
                clientes.estado,

                COUNT(vendas.id)
                    AS quantidade_compras,

                COALESCE(SUM(vendas.total), 0)
                    AS total_gasto,

                COALESCE(AVG(vendas.total), 0)
                    AS ticket_medio,

                MAX(vendas.criado_em)
                    AS ultima_compra

            FROM clientes

            LEFT JOIN vendas
                ON vendas.cliente_id = clientes.id
                AND vendas.status = 'concluida'
                AND vendas.criado_em >= :data_inicio
                AND vendas.criado_em < :data_fim

            WHERE clientes.ativo = 1

            GROUP BY
                clientes.id,
                clientes.tipo,
                clientes.nome,
                clientes.cpf_cnpj,
                clientes.telefone,
                clientes.whatsapp,
                clientes.email,
                clientes.cidade,
                clientes.estado

            ORDER BY
                total_gasto DESC,
                clientes.nome ASC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);

        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clientes as &$cliente) {
            $cliente['id'] = (int) $cliente['id'];
            $cliente['quantidade_compras'] =
                (int) $cliente['quantidade_compras'];
            $cliente['total_gasto'] =
                round((float) $cliente['total_gasto'], 2);
            $cliente['ticket_medio'] =
                round((float) $cliente['ticket_medio'], 2);

            $cidade = trim((string) ($cliente['cidade'] ?? ''));
            $estado = trim((string) ($cliente['estado'] ?? ''));

            $cliente['localidade'] = trim(
                $cidade
                . ($cidade !== '' && $estado !== '' ? ' - ' : '')
                . $estado
            );

            if ($cliente['localidade'] === '') {
                $cliente['localidade'] = 'Não informada';
            }
        }

        unset($cliente);

        return $clientes;
    }

    private function calcularResumoClientes(
        array $clientes
    ): array {
        $comCompra = 0;
        $semCompra = 0;
        $compras = 0;
        $total = 0;

        foreach ($clientes as $cliente) {
            $quantidade =
                (int) $cliente['quantidade_compras'];

            if ($quantidade > 0) {
                $comCompra++;
            } else {
                $semCompra++;
            }

            $compras += $quantidade;
            $total += (float) $cliente['total_gasto'];
        }

        return [
            'total_clientes' => count($clientes),
            'clientes_com_compra' => $comCompra,
            'clientes_sem_compra' => $semCompra,
            'quantidade_compras' => $compras,
            'total_movimentado' => round($total, 2),
            'ticket_medio_geral' => round(
                $compras > 0 ? $total / $compras : 0,
                2
            )
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO DE ORDENS DE SERVIÇO
    |--------------------------------------------------------------------------
    */

    public function gerarRelatorioOrdensServico(
        string $dataInicio,
        string $dataFim
    ): array {
        $dataInicio = $this->validarData(
            $dataInicio,
            'Data inicial inválida.'
        );

        $dataFim = $this->validarData(
            $dataFim,
            'Data final inválida.'
        );

        if ($dataInicio > $dataFim) {
            throw new Exception(
                'A data inicial não pode ser maior que a data final.'
            );
        }

        $inicio = $dataInicio . ' 00:00:00';

        $fim = new DateTime(
            $dataFim . ' 00:00:00'
        );

        $fim->modify('+1 day');

        $ordens = $this->buscarRelatorioOrdensServico(
            $inicio,
            $fim->format('Y-m-d H:i:s')
        );

        $resumo = $this->calcularResumoOrdensServico(
            $ordens
        );

        $clientesRanking = [];

        $aparelhosRanking = [];

        foreach ($ordens as $ordem) {
            $clienteId = (int) $ordem['cliente_id'];

            if (!isset($clientesRanking[$clienteId])) {
                $clientesRanking[$clienteId] = [
                    'cliente_nome' =>
                        $ordem['cliente_nome'],
                    'quantidade' => 0,
                    'valor_total' => 0
                ];
            }

            $clientesRanking[$clienteId]['quantidade']++;

            $clientesRanking[$clienteId]['valor_total'] +=
                (float) $ordem['valor_total'];

            $aparelho = trim(
                $ordem['aparelho_marca']
                . ' '
                . $ordem['aparelho_modelo']
            );

            if ($aparelho === '') {
                $aparelho = 'Não informado';
            }

            if (!isset($aparelhosRanking[$aparelho])) {
                $aparelhosRanking[$aparelho] = [
                    'aparelho' => $aparelho,
                    'quantidade' => 0,
                    'valor_total' => 0
                ];
            }

            $aparelhosRanking[$aparelho]['quantidade']++;

            $aparelhosRanking[$aparelho]['valor_total'] +=
                (float) $ordem['valor_total'];
        }

        $clientesRanking = array_values(
            $clientesRanking
        );

        usort(
            $clientesRanking,
            static function (
                array $clienteA,
                array $clienteB
            ): int {
                $comparacao =
                    $clienteB['quantidade']
                    <=> $clienteA['quantidade'];

                if ($comparacao !== 0) {
                    return $comparacao;
                }

                return $clienteB['valor_total']
                    <=> $clienteA['valor_total'];
            }
        );

        $clientesRanking = array_slice(
            $clientesRanking,
            0,
            10
        );

        $aparelhosRanking = array_values(
            $aparelhosRanking
        );

        usort(
            $aparelhosRanking,
            static function (
                array $aparelhoA,
                array $aparelhoB
            ): int {
                $comparacao =
                    $aparelhoB['quantidade']
                    <=> $aparelhoA['quantidade'];

                if ($comparacao !== 0) {
                    return $comparacao;
                }

                return $aparelhoB['valor_total']
                    <=> $aparelhoA['valor_total'];
            }
        );

        $aparelhosRanking = array_slice(
            $aparelhosRanking,
            0,
            10
        );

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'ordens' => $ordens,
            'resumo' => $resumo,
            'clientes_ranking' =>
                $clientesRanking,
            'aparelhos_ranking' =>
                $aparelhosRanking
        ];
    }

    private function buscarRelatorioOrdensServico(
        string $dataInicio,
        string $dataFim
    ): array {
        $sql = "
            SELECT
                os.id,
                os.numero_os,
                os.venda_id,
                os.cliente_id,
                os.aparelho_marca,
                os.aparelho_modelo,
                os.aparelho_cor,
                os.defeito_relatado,
                os.valor_os,
                os.desconto,
                os.valor_total,
                os.status,
                os.data_entrada,
                os.data_prevista,
                os.data_conclusao,
                os.data_entrega,

                clientes.nome
                    AS cliente_nome,

                clientes.telefone
                    AS cliente_telefone,

                clientes.whatsapp
                    AS cliente_whatsapp

            FROM ordens_servico AS os

            INNER JOIN clientes
                ON clientes.id = os.cliente_id

            WHERE
                os.data_entrada >= :data_inicio

                AND os.data_entrada < :data_fim

            ORDER BY
                os.data_entrada DESC,
                os.id DESC
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ]);

        $ordens = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        foreach ($ordens as &$ordem) {
            $ordem['id'] =
                (int) $ordem['id'];

            $ordem['cliente_id'] =
                (int) $ordem['cliente_id'];

            $ordem['venda_id'] =
                !empty($ordem['venda_id'])
                    ? (int) $ordem['venda_id']
                    : null;

            $ordem['valor_os'] =
                round(
                    (float) $ordem['valor_os'],
                    2
                );

            $ordem['desconto'] =
                round(
                    (float) $ordem['desconto'],
                    2
                );

            $ordem['valor_total'] =
                round(
                    (float) $ordem['valor_total'],
                    2
                );

            $ordem['status_nome'] =
                $this->formatarStatusOrdemServico(
                    (string) $ordem['status']
                );

            $ordem['aparelho_marca'] =
                trim(
                    (string) (
                        $ordem['aparelho_marca']
                        ?? ''
                    )
                );

            $ordem['aparelho_modelo'] =
                trim(
                    (string) (
                        $ordem['aparelho_modelo']
                        ?? ''
                    )
                );

            $ordem['aparelho'] = trim(
                $ordem['aparelho_marca']
                . ' '
                . $ordem['aparelho_modelo']
            );

            if ($ordem['aparelho'] === '') {
                $ordem['aparelho'] =
                    'Não informado';
            }
        }

        unset($ordem);

        return $ordens;
    }

    private function calcularResumoOrdensServico(
        array $ordens
    ): array {
        $status = [
            'recebida' => 0,
            'em_analise' => 0,
            'aguardando_aprovacao' => 0,
            'aguardando_peca' => 0,
            'em_manutencao' => 0,
            'pronta' => 0,
            'venda' => 0,
            'entregue' => 0,
            'cancelada' => 0
        ];

        $valorBruto = 0;
        $descontos = 0;
        $valorTotal = 0;
        $ordensFinalizadas = 0;
        $ordensEmAberto = 0;

        foreach ($ordens as $ordem) {
            $statusAtual =
                (string) $ordem['status'];

            if (!isset($status[$statusAtual])) {
                $status[$statusAtual] = 0;
            }

            $status[$statusAtual]++;

            $valorBruto +=
                (float) $ordem['valor_os'];

            $descontos +=
                (float) $ordem['desconto'];

            $valorTotal +=
                (float) $ordem['valor_total'];

            if (in_array(
                $statusAtual,
                ['entregue', 'cancelada'],
                true
            )) {
                $ordensFinalizadas++;
            } else {
                $ordensEmAberto++;
            }
        }

        $quantidade = count($ordens);

        return [
            'total_ordens' => $quantidade,
            'ordens_em_aberto' =>
                $ordensEmAberto,
            'ordens_finalizadas' =>
                $ordensFinalizadas,
            'valor_bruto' =>
                round($valorBruto, 2),
            'descontos' =>
                round($descontos, 2),
            'valor_total' =>
                round($valorTotal, 2),
            'ticket_medio' =>
                round(
                    $quantidade > 0
                        ? $valorTotal / $quantidade
                        : 0,
                    2
                ),
            'status' => $status
        ];
    }

    private function formatarStatusOrdemServico(
        string $status
    ): string {
        $nomes = [
            'recebida' => 'Recebida',
            'em_analise' => 'Em análise',
            'aguardando_aprovacao' =>
                'Aguardando aprovação',
            'aguardando_peca' =>
                'Aguardando peça',
            'em_manutencao' =>
                'Em manutenção',
            'pronta' => 'Pronta',
            'venda' => 'Adicionada à venda',
            'entregue' => 'Entregue',
            'cancelada' => 'Cancelada'
        ];

        return $nomes[$status]
            ?? ucfirst(
                str_replace('_', ' ', $status)
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RELATÓRIO FINANCEIRO
    |--------------------------------------------------------------------------
    */

    public function gerarRelatorioFinanceiro(
        int $mes,
        int $ano
    ): array {
        if ($mes < 1 || $mes > 12) {
            throw new Exception(
                'Selecione um mês válido.'
            );
        }

        if ($ano < 2000 || $ano > 2100) {
            throw new Exception(
                'Selecione um ano válido.'
            );
        }

        $financeiro = new Financeiro();

        $dados = $financeiro
            ->obterDadosFinanceiros(
                $mes,
                $ano
            );

        $resumo = $dados['resumo'] ?? [];

        $quantidadeVendas =
            (int) (
                $resumo['quantidade_vendas']
                ?? 0
            );

        $totalRecebido =
            (float) (
                $resumo['total_recebido']
                ?? 0
            );

        $dados['resumo']['ticket_medio'] =
            round(
                $quantidadeVendas > 0
                    ? $totalRecebido
                        / $quantidadeVendas
                    : 0,
                2
            );

        $categoriasGastos = [];

        foreach (
            ($dados['gastos'] ?? [])
            as $gasto
        ) {
            $categoria = trim(
                (string) (
                    $gasto['categoria']
                    ?? 'Sem categoria'
                )
            );

            if ($categoria === '') {
                $categoria = 'Sem categoria';
            }

            if (
                !isset(
                    $categoriasGastos[$categoria]
                )
            ) {
                $categoriasGastos[$categoria] = [
                    'categoria' => $categoria,
                    'quantidade' => 0,
                    'valor_total' => 0
                ];
            }

            $categoriasGastos[$categoria]
                ['quantidade']++;

            $categoriasGastos[$categoria]
                ['valor_total'] +=
                    (float) (
                        $gasto['valor']
                        ?? 0
                    );
        }

        $categoriasGastos = array_values(
            $categoriasGastos
        );

        usort(
            $categoriasGastos,
            static function (
                array $categoriaA,
                array $categoriaB
            ): int {
                return $categoriaB['valor_total']
                    <=> $categoriaA['valor_total'];
            }
        );

        foreach (
            $categoriasGastos
            as &$categoria
        ) {
            $categoria['valor_total'] =
                round(
                    (float) $categoria[
                        'valor_total'
                    ],
                    2
                );
        }

        unset($categoria);

        $produtosMaisLucrativos =
            $dados['produtos_vendidos']
            ?? [];

        usort(
            $produtosMaisLucrativos,
            static function (
                array $produtoA,
                array $produtoB
            ): int {
                return
                    (float) $produtoB['lucro']
                    <=>
                    (float) $produtoA['lucro'];
            }
        );

        $dados['categorias_gastos'] =
            $categoriasGastos;

        $dados['produtos_mais_lucrativos'] =
            array_slice(
                $produtosMaisLucrativos,
                0,
                10
            );

        return $dados;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DE DATA
    |--------------------------------------------------------------------------
    */

    private function validarData(
        string $data,
        string $mensagemErro
    ): string {
        $data = trim($data);

        $objetoData =
            DateTime::createFromFormat(
                'Y-m-d',
                $data
            );

        if (
            $objetoData === false
            || $objetoData->format(
                'Y-m-d'
            ) !== $data
        ) {
            throw new Exception(
                $mensagemErro
            );
        }

        return $data;
    }
}