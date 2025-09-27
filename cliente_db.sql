-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 179.188.16.204
-- Generation Time: 26-Set-2025 às 14:16
-- Versão do servidor: 5.7.32-35-log
-- PHP Version: 5.6.40-0+deb8u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cliente_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `inicio_vigencia` date NOT NULL,
  `final_vigencia` date DEFAULT NULL,
  `apolice` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `numero` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `premio_liquido` decimal(10,2) NOT NULL DEFAULT '0.00',
  `comissao` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) NOT NULL DEFAULT 'Efetivado',
  `tipo_operacao` varchar(20) NOT NULL DEFAULT 'NOVO',
  `seguradora` varchar(100) NOT NULL,
  `tipo_seguro` varchar(50) NOT NULL,
  `item_segurado` varchar(255) DEFAULT NULL,
  `anotacoes` text,
  `item_identificacao` varchar(100) DEFAULT NULL COMMENT 'Armazena a placa do veículo ou um número de identificação do item segurado',
  `pdfAntigo` varchar(255) DEFAULT NULL,
  `observacoes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `inicio_vigencia`, `final_vigencia`, `apolice`, `nome`, `cpf`, `numero`, `email`, `pdf_path`, `premio_liquido`, `comissao`, `status`, `tipo_operacao`, `seguradora`, `tipo_seguro`, `item_segurado`, `anotacoes`, `item_identificacao`, `pdfAntigo`, `observacoes`) VALUES


-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensagem` varchar(255) NOT NULL,
  `data_hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `mensagem`, `data_hora`) VALUES
(3121, 12, 'Usuário Jessica adicionou (RENOVAÇÃO) proposta de JESUS AFONSO PADOVANI.', '2025-09-26 13:43:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `seguradoras`
--

CREATE TABLE `seguradoras` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `numero_0800` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `seguradoras`
--

INSERT INTO `seguradoras` (`id`, `nome`, `usuario`, `senha`, `numero_0800`) VALUES
(1, 'Aliro Seguro', '55206280682', 'Minasriogrande@1976', '08000130700'),
(2, 'Allianz Seguros', 'ba080910', 'Minasrg1976@', '08000130700'),
(3, 'Azul Seguros', '030371', 'Minas@1976', '08007030203'),
(4, 'HDI Seguros', '05825531645', 'Tmrg@1976', '08004344340'),
(5, 'Liberty Seguros', '55206280682', 'Minasriogrande@1976', '08007014120'),
(6, 'MAPFRE', '55206280682', 'Minas@2024', '08007754545'),
(7, 'Porto Seguro', '55206280682 - 28250J (P)', 'Mrg2025@', '08007270800'),
(8, 'Sompo Auto', '050232500000', 'Mrg2024!', '08004344340'),
(9, 'Tokio Marine Seguros', '10245393692', 'Mrg1976@', '08003186546'),
(10, 'Zurich Brasil Seguros', '270044', 'Mrg@1976', '08007077883'),
(11, 'Sancor Seguros', '222139653', 'Mrgdiamond53', '08002000392'),
(12, 'Suhai', '01147632000145', 'Mrg1976!', '08003278424'),
(13, 'Mitsui', '0085740', 'Mrg@1976!', '08007077883'),
(14, 'Sura Seguros', '10245393692', 'Minasrio1976@', '08007770989'),
(15, 'Ezze', '222139653', 'Wmrgezze1976!', '08007029985'),
(17, 'Unimed Seguros', '102.453.936-92', 'Mrgunimed1976@', '08000166633'),
(18, 'Bradesco', '01147632000145', 'Mrg1976!', '08007012757'),
(19, 'Zurich LIFE', '620140', 'Mrg1952!', '1'),
(20, 'Porto LIFE', '102.453.936-92', 'Wmrg1976#', '1'),
(21, 'SulAmerica', '40183', '22725', '1'),
(22, 'bradessco life', '10245393692', 'Mrg1890!', '08007012757'),
(23, 'BICI SURA', 'mrg@mrgseguros.com.br', 'Mrg1976', '08007049399'),
(24, 'SUHAI MRG', '55206280682', 'Rgminas2024!', '08003278424'),
(25, 'Sura Canal corretor', '18280', 'Corretora@25', '0800'),
(27, 'SUHAI DIAMOND LIFE', '10245393692', 'Wmrg1976!', '0800'),
(28, 'MAPFRE CONECT', '100282502 - 58271', 'Mrg1976!', '0');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`) VALUES
(11, 'Jose Abrahao Tonelli', 'ja@mrgseguros.com.br', '$2y$10$ZvxtgQjuAVG5isJlr6zjxuhy6GmJxLnyZhSCU0VUxRtU36f2QvGSK'),
(12, 'Jessica', 'atendimento@mrgseguros.com.br', '$2y$10$xTObCGWh06W6.Fj4TNoZR.DASl6Vs5L006ARBqbGkOBZyOYNRY7hu'),
(13, 'Thiago Monteiro', 'thiago@mrgseguros.com.br', '$2y$10$NsZzHJ6g8JvJGltCHYS46uc7fwLGZnoz2jkEHV20.HeOPSKqfvRM.'),
(14, 'Guilherme', 'comercial@mrgseguros.com.br', '$2y$10$lBaKLUbnHjMpVXJm6sQ3CORymd7wRgTmGbTvnx2gCBJlh/fuKLcq2'),
(15, 'william tonelli ', 'william@mrgseguros.com.br', '$2y$10$E4IMvARaqJPyonv2ZTCkw.gvBI2fhUWtK4HC5Ro8AXA2xPMPHvmtu'),
(16, 'sinistro', 'sinistro@mrgseguros.com.br', '$2y$10$ARs3yaOSS5lspyGOkGOJpeGcAsI6VTQ78/jIf/x14AqbkuRTp4Gbq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `seguradoras`
--
ALTER TABLE `seguradoras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2368;

--
-- AUTO_INCREMENT for table `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3122;

--
-- AUTO_INCREMENT for table `seguradoras`
--
ALTER TABLE `seguradoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
