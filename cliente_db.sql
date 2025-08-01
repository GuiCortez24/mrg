-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 179.188.16.204
-- Generation Time: 30-Abr-2025 às 22:09
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
  `seguradora` varchar(100) NOT NULL,
  `tipo_seguro` varchar(50) NOT NULL,
  `pdfAntigo` varchar(255) DEFAULT NULL,
  `observacoes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `inicio_vigencia`, `final_vigencia`, `apolice`, `nome`, `cpf`, `numero`, `email`, `pdf_path`, `premio_liquido`, `comissao`, `status`, `seguradora`, `tipo_seguro`, `pdfAntigo`, `observacoes`) VALUES
(61, '2024-08-06', NULL, '1911015707', 'OGMAR CASTELI PANZERA', '00816566615', '35991975000', 'MOINHOSGERAIS@HOTMAIL.COM', NULL, 770.65, 20.00, 'Emitida', 'Porto Seguro', 'Seguro Empresarial', NULL, 'SEGURO RESIDENCIAL..'),
(62, '2024-08-06', NULL, '1911014011', 'OGMAR CASTELI PANZERA', '00816566615', '(35) 99197-5000', 'MOINHOSGERAIS@HOTMAIL.COM', '../uploads/OGMAR CASTELI PANZERA.pdf.crdownload', 328.79, 25.00, 'Emitida', 'Porto Seguro', 'Seguro Empresarial', NULL, 'SEGURO RESIDENCIAL'),
(63, '2024-08-02', NULL, '28250316731491911', 'SILVIO MIRANDA SIGNORETTI', '46797297672', '(99) 84170-63', 'JCARVALHOSIGNORETTI@GMAIL.COM', '../uploads/SILVIO MIRANDA SIGNORETTI - ONIX.pdf', 1923.67, 15.00, 'Emitida', 'Porto Seguro', 'Seguro Auto', NULL, ''),
(1400, '2025-04-24', NULL, '4465485548', 'MARIA APARECIDA GUIMARAES ANDRADE', '929.196.346', '(35) 99979-0085', 'NIRLEIVILELA@HOTMAIL.COM', '../uploads/MARIA APARECIDA GUIMARAES ANDRADE_250423_101013.pdf', 1565.80, 10.00, 'Aguardando EmissÃ£o', 'Porto Seguro', 'Seguro Auto', NULL, NULL),
(1401, '2025-04-30', NULL, '132251062', 'ALCEBIADES OTAVIO RESENDE MARQUES', '085.374.996', '(35) 99869-0124', 'alcebiadesresende@gmail.com', '../uploads/ALCEBIADES OTAVIO RESENDE MARQUES_250423_105616.pdf', 1971.65, 15.00, 'Aguardando EmissÃ£o', 'Allianz Seguros', 'Seguro Auto', NULL, NULL),

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
(2, 'Allianz Seguros', 'ba080910', 'Minasrg2025.', '08000130700'),
(3, 'Azul Seguros', '030371', 'Mrg@1976', '08007030203'),
(4, 'HDI Seguros', '05825531645', 'tmrg1976', '08004344340'),
(5, 'Liberty Seguros', '55206280682', 'Minasriogrande@1976', '08007014120'),
(6, 'MAPFRE', '55206280682', 'Minas@2024', '08007754545'),
(7, 'Porto Seguro', '55206280682 - 28250J (P)', 'Mrg2024!', '08007270800'),
(8, 'Sompo Auto', '050232500000', 'Mrg2024!', '08004344340'),
(9, 'Tokio Marine Seguros', '10245393692', 'Mrg2025!', '08003186546'),
(10, 'Zurich Brasil Seguros', '270044', 'Mrg@1976', '08007077883'),
(11, 'Sancor Seguros', '222139653', 'Mrgdiamond53', '08002000392'),
(12, 'Suhai', '01147632000145', 'Mrg1976!', '08003278424'),
(13, 'Mitsui', '0085740', 'Mrg@1976!', '08007077883'),
(14, 'Sura Seguros', '10245393692', 'Minasrio@1976', '08007770989'),
(15, 'Ezze', '222139653', 'Wmrgezze1976!', '08007029985'),
(17, 'Unimed Seguros', '102.453.936-92', 'Mrgunimed1976@', '08000166633'),
(18, 'Bradesco', '01147632000145', 'Mrg1976!', '08007012757'),
(19, 'Zurich LIFE', '620140', 'Mrg1952!', '1'),
(20, 'Porto LIFE', '102.453.936-92', 'Mrg2024!', '1'),
(21, 'SulAmerica', '40183', '22725', '1'),
(22, 'bradessco life', '10245393692', 'Mrg1890;', '08007012757'),
(23, 'BICI SURA', 'mrg@mrgseguros.com.br', 'Mrg1976', '08007049399'),
(24, 'SUHAI MRG', '55206280682', 'Rgminas2024!', '08003278424'),
(25, 'Sura Canal corretor', '18280', 'Corretora@25', '0800');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1443;

--
-- AUTO_INCREMENT for table `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1999;

--
-- AUTO_INCREMENT for table `seguradoras`
--
ALTER TABLE `seguradoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
