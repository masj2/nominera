SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Tabellstruktur `nominerade`
--

CREATE TABLE IF NOT EXISTS `nominerade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `av` varchar(50) NOT NULL,
  `motivering` varchar(300) NOT NULL,
  `password` char(32) NOT NULL,
  `tel` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `beskrivning` varchar(300) NOT NULL,
  `accept` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  `placering` int(11) NOT NULL,
  `logintime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumpning av Data i tabell `nominerade`
--

INSERT INTO `nominerade` (`id`, `name`, `av`, `motivering`, `password`, `tel`, `email`, `beskrivning`, `accept`, `votes`, `placering`, `logintime`) VALUES
(1, 'admin', '', '', 'password', '', '', '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `voters`
--

CREATE TABLE IF NOT EXISTS `voters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `verify` int(11) NOT NULL,
  `voted` int(11) NOT NULL,
  `logintime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
