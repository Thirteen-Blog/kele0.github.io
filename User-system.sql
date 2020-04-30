-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-03-13 00:33:28
-- 服务器版本： 5.6.19
-- PHP Version: 5.5.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `User-system`
--

-- --------------------------------------------------------

--
-- 表的结构 `kami`
--

CREATE TABLE `kami` (
  `id` int(10) NOT NULL,
  `kami` varchar(30) NOT NULL,
  `type` varchar(50) NOT NULL,
  `value` int(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `kami`
--

INSERT INTO `kami` (`id`, `kami`, `type`, `value`) VALUES
(26, 'N4Q10Z6GX7JZ', '至尊VIP', 3),
(25, 'Z6IT6VNMJ5RY', '至尊VIP', 3),
(20, 'GHIBUSBOFY2N', '金额', 1),
(27, 'IBDU8YLAUHLA3', '至尊VIP', 3);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL COMMENT '编号',
  `user` varchar(20) NOT NULL COMMENT '账号',
  `pass` varchar(20) NOT NULL COMMENT '密码',
  `email` varchar(25) NOT NULL,
  `state` varchar(50) NOT NULL COMMENT '状态',
  `grade` varchar(30) NOT NULL COMMENT '等级',
  `expiry` varchar(30) NOT NULL COMMENT '到期',
  `sum` varchar(30) NOT NULL COMMENT '金额',
  `ip` varchar(35) NOT NULL COMMENT 'IP',
  `date` datetime NOT NULL COMMENT '注册'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `user`, `pass`, `email`, `state`, `grade`, `expiry`, `sum`, `ip`, `date`) VALUES
(2, '浮沉', '123456', '2722053504@qq.com', 'true', '超级管理员', '2018-03-13', '55', '127.0.0.1', '2018-03-10 12:15:46'),
(3, '老王', '6655', '2722053503@qq.com', 'true', '普通用户', '长期', '10', '127.0.0.1', '2018-03-10 12:22:45'),
(4, '测试用户', '123456', '272205350@qq.com', '拉黑:你丑', '普通用户', '长期', '10', '127.0.0.1', '2018-03-12 21:25:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kami`
--
ALTER TABLE `kami`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kami` (`kami`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `kami`
--
ALTER TABLE `kami`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '编号', AUTO_INCREMENT=5;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
