DROP TABLE IF EXISTS task;
DROP TABLE IF EXISTS mao_obra_aplicada;
DROP TABLE IF EXISTS ordem_servico;

--- Tabela de Task
CREATE TABLE IF NOT EXISTS task(
    id_task SERIAL PRIMARY KEY NOT NULL,
    nm_titulo VARCHAR(150) NOT NULL,
    ds_task VARCHAR(300) NOT NULL,
    tp_prioridade int NOT NULL
);

--- Tabela Ordem Seriço
create table if not exists ordem_servico(
	id_ordemservico BIGSERIAL, 
	nu_ordemservico VARCHAR(100) UNIQUE,
	nm_patrimonio VARCHAR(100),
	dt_abertura date,
	dt_conclusao date,
	ds_defeito TEXT,
	tp_situacao INT,
	vl_custototal NUMERIC(18,2) NULL,
	primary key(id_ordemservico)
);


create table if not exists mao_obra_aplicada(
	id_maoobraaplicada BIGSERIAL, 
	nm_peca BIGINT, 
	vl_unitario NUMERIC(18,2),
	id_ordemservico BIGINT,
	qt_utilizada INTEGER, 
	vl_totalitem NUMERIC(18,2),
	primary key(id_maoobraaplicada),
	constraint fk_mao_obra_aplicada_ordem_servico foreign key(id_ordemservico) 
		references ordem_servico(id_ordemservico)
);

INSERT INTO ordem_servico (
    nu_ordemservico, 
    nm_patrimonio, 
    dt_abertura, 
    dt_conclusao, 
    ds_defeito, 
    tp_situacao, 
    vl_custototal
) VALUES 
(
    'OS-2026-001', 
    'PAT-NOTE-001',
    '2026-01-10', 
    '2026-01-12', 
    'Notebook não liga, possivelmente problema na fonte de alimentação.', 
    3,
    250.00
),
(
    'OS-2026-002', 
    'PAT-AR-552', 
    '2026-02-15', 
    NULL, 
    'Ar condicionado parou de refrigerar. Necessita de carga de gás.', 
    2,
    NULL
),
(
    'OS-2026-003', 
    '100890',
    '2026-03-01', 
    NULL, 
    'Troca de tela de smartphone corporativo trincada.', 
    1,
    NULL
),
(
    'OS-2026-004', 
    'PAT-IMP-99', 
    '2026-03-10', 
    '2026-03-11', 
    'Impressora com defeito na placa lógica, orçamento reprovado pelo cliente.', 
    4,
    0.00
);

create table if not exists peca(
	id_peca BIGSERIAL, 
	cd_peca VARCHAR(100) UNIQUE, 
	nm_peca VARCHAR(100), 
	vl_unitario NUMERIC(18,2),
	primary key(id_peca)
);

INSERT INTO peca (cd_peca, nm_peca, vl_unitario) VALUES
('PEC-2026-0001', 'Fonte de Alimentação ATX 650W',     320.00),
('PEC-2026-0002', 'Memória RAM DDR4 8GB',               180.00),
('PEC-2026-0003', 'SSD NVMe 480GB',                     350.00),
('PEC-2026-0004', 'Placa-mãe ATX',                      650.00),
('PEC-2026-0005', 'Bateria de No-break 12V 7Ah',        145.00),
('PEC-2026-0006', 'Toner Original HP 85A',              280.00),
('PEC-2026-0007', 'Cooler Fan 120mm',                    65.00),
('PEC-2026-0008', 'Cabo de Rede Cat6 (rolo 100m)',      210.00),
('PEC-2026-0009', 'Compressor de Ar Condicionado',     1850.00),
('PEC-2026-0010', 'Filtro de Ar Condicionado',           90.00),
('PEC-2026-0011', 'Rodízio para Cadeira (jogo)',          45.00),
('PEC-2026-0012', 'Gás Refrigerante R410A (kg)',        180.00),
('PEC-2026-0013', 'Disjuntor Termomagnético 20A',        75.00),
('PEC-2026-0014', 'Lâmpada de Projetor Epson',          420.00),
('PEC-2026-0015', 'Vela de Ignição para Gerador',        38.00);
