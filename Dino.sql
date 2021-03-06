-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: julio 08, 2018 at 08:50 AM
-- PHP Version: 7.0.9
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
--
-- Database: `dino`
--
CREATE DATABASE IF NOT EXISTS `Dino` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `Dino`;

--
-- Table structure for table `Concurso`
--

CREATE TABLE IF NOT EXISTS `Concurso` (
  `CId` int(11) NOT NULL auto_increment COMMENT 'El identificador unico para cada concurso',
  `Titulo` tinytext character set latin1 collate latin1_spanish_ci NOT NULL COMMENT 'El titulo que aparecera en cada concurso',
  `Descripcion` text NOT NULL COMMENT 'Una breve descripcion de cada concurso.',
  `Inicio` timestamp NOT NULL DEFAULT '2000-01-01 06:00:00' COMMENT 'Hora de inicio de este concurso',
  `Final` timestamp NOT NULL DEFAULT '2000-01-01 06:00:00' COMMENT 'Hora de finalizacion de este concurso',
  `EsPrivado` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'False implica concurso cerrado, ver la tabla ConcursantesConcurso',
  `BloqueoTabla` timestamp NOT NULL DEFAULT '2000-01-01 06:00:00' COMMENT 'Entero del 0 al 100, indicando el porcentaje de tiempo que el scoreboard será visible',
  `report` mediumtext NOT NULL COMMENT 'Reporte del concurso.',
  `password` varchar(2048) NOT NULL COMMENT 'Contraseña para cada concurso.',
  `Owner` varchar(255) NOT NULL COMMENT 'El usuario que creó el objeto',
  PRIMARY KEY  (`CID`),
  KEY `Inicio` (`Inicio`),
  KEY `Final` (`Final`),
	KEY `EsPrivado` (`EsPrivado`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 COMMENT='Concursos que se llevan a cabo en el juez.';

-- --------------------------------------------------------

--
-- Table structure for table `Usuario`
--

CREATE TABLE IF NOT EXISTS `Usuario` (
  `UserID` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id del usuario',
  `UserName` varchar(255) NOT NULL  COMMENT 'Username o nickname del usuario',
 -- `Nickname` varchar(1024) DEFAULT NULL,--
  `Nombre` varchar(128) character set latin1 collate latin1_spanish_ci NOT NULL COMMENT 'nombre del usuario',
  `Apellidos` varchar(128) character set latin1 collate latin1_spanish_ci NOT NULL COMMENT 'apellidos del usuario',
  `Password` varchar(2048) NOT NULL COMMENT 'contraseña del usuario',
  `TotalAceptados` int(11) NOT NULL default '0' COMMENT 'problemas aceptados',
  `TotalEnvios` int(11) NOT NULL default '0' COMMENT 'cantidad de ejecuciones realizadas',
  `Ubicacion` varchar(64) NOT NULL COMMENT 'Ubicacion del usuario',
  `Institucion` text NOT NULL COMMENT 'Institucion donde estudia el usuario',
  `Email` varchar(64) NOT NULL COMMENT 'Email del usuario',
  `TiempoRegistro` datetime NOT NULL COMMENT 'Fecha en la q se registro el usuario',
  `Estado` int(1) NOT NULL COMMENT 'Estado activo o inactivo',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserName` (`UserName`),
  --KEY `Nickname` (`Nickname`(255)),--
  KEY `Password` (`Password`),
  KEY `Ubicacion` (`Ubicacion`),
  KEY `Email` (`Email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Lista de Usuarios';


-- --------------------------------------------------------

--
-- Table structure for table `Problema`
--
CREATE TABLE IF NOT EXISTS `Problema` (
  `ProbID` int(11) NOT NULL auto_increment COMMENT 'id del problema',
  `UserID` int(11) NOT NULL COMMENT 'id del usuario q subio el problema',
  `Publico` varchar(4) NOT NULL default 'NO' COMMENT 'mostrar en problemas',
  `Titulo` varchar(256) NOT NULL COMMENT 'titulo del problema',
  `descripcion` longtext NOT NULL COMMENT 'descripcion del problema',
  `Entrada` text NOT NULL COMMENT 'especificacion de las entradas del problema',
  `Salida` text NOT NULL COMMENT 'especificacion de las salidas del problema',
  `EjemploEntrada` text NOT NULL COMMENT 'ejemplos de entradas del problema',
  `EjemploSalida` text NOT NULL COMMENT 'ejemplos de  salidas del problema',
  `TiempoLimite` int(11) NOT NULL COMMENT 'tiempo limite en segundos',
  `MemoriaLimite` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'limite de memoria en MB',
  `Autor` text NOT NULL COMMENT 'autor del problema',
  `Source` varchar(255) DEFAULT NULL,
  `Vistas` int(11) NOT NULL default '0' COMMENT 'cantidad de vistos de cada problema',
  `Aceptados` int(11) NOT NULL default '0' COMMENT 'cantidad de aceptados de cada problema',
  `Intentos` int(11) NOT NULL default '0' COMMENT 'cantidad de vintentos de cada problema',
  PRIMARY KEY  (`ProbID`),
  KEY `UserID` (`UserID`),
  KEY `Titulo` (`Titulo`),
  KEY `Source` (`Source`(255)),
  CONSTRAINT `fk_p_ProbID` FOREIGN KEY (`UserID`) REFERENCES `Usuario` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Problemas para el Juez Dino' AUTO_INCREMENT=10 ;


-- --------------------------------------------------------

--
-- Table structure for table `ConcursoProblema`
--
CREATE TABLE IF NOT EXISTS `ConcursoProblema` (
  `cpid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL,
  `pid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`cpid`),
  KEY `cid` (`cid`),
  KEY `pid` (`pid`),
  CONSTRAINT `fk_cp_cid` FOREIGN KEY (`cid`) REFERENCES `Concurso` (`CId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cp_pid` FOREIGN KEY (`pid`) REFERENCES `Problema` (`ProbID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='Contest, its problems and their status';



-- --------------------------------------------------------

--
-- Table structure for table `ejecucion`
--


CREATE TABLE IF NOT EXISTS `Ejecucion` (
  `EjecID` bigint(20) NOT NULL auto_increment COMMENT 'id de la ejecucion',
  `Lenguaje` varchar(8) NOT NULL COMMENT 'lenguaje de programacion',
  `UserName` varchar(255) NOT NULL NOT NULL COMMENT 'id del usuario q realizo la ejecucion',
  `ProbID` int(11) NOT NULL COMMENT 'id del problema q realizo la ejecucion',
  `Estado` varchar(64) NOT NULL default 'JUDGING',
  `Tiempo` int(11) NOT NULL DEFAULT '0' COMMENT 'tiempo usado en segundos',
  `Source` mediumtext COMMENT 'Codigo enviado',
  `Memoria` int(11) NOT NULL DEFAULT '0' COMMENT 'memoria usada en MB',
  `RemoteIP` varchar(16) NOT NULL COMMENT 'Obtencion de la IP de donde se hizo la ejecucion',
  `Fecha` timestamp NOT NULL default CURRENT_TIMESTAMP  COMMENT 'fecha en la q se hizo la ejecucion',
  `jnum` smallint(6) NOT NULL,
  `ConcursoID` int(11) NOT NULL default '-1',
  PRIMARY KEY  (`EjecID`),  
  KEY `ProbID` (`ProbID`),
  KEY `Estado` (`Estado`),
  KEY `Tiempo` (`Tiempo`),
  KEY `ConcursoID` (`ConcursoID`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `fk_ejec_ProbID` FOREIGN KEY (`ProbID`) REFERENCES `Problema` (`ProbID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ejec_ConcursoID` FOREIGN KEY (`ConcursoID`) REFERENCES `Concurso` (`CId`) ON DELETE NO ACTION ON UPDATE NO ACTION

) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1001;



-- --------------------------------------------------------

--
-- Table structure for table `LostPassword`
--

CREATE TABLE IF NOT EXISTS `LostPassword` (
  `ID` smallint(11) NOT NULL auto_increment,
  `Fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `IP` varchar(16) collate latin1_bin NOT NULL,
  `UserID` int(11)  NOT NULL,
  `Token` varchar(128) collate latin1_bin NOT NULL,
  `MailSent` tinyint(1) NOT NULL default '0' COMMENT 'Se ha enviado el correo a este usuario',
  PRIMARY KEY (`ID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `fk_LostPass_UserID` FOREIGN KEY (`UserID`) REFERENCES `Usuario` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_bin AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `Mensaje`
--

CREATE TABLE IF NOT EXISTS `Mensaje` (
  `Id` int(11) NOT NULL auto_increment COMMENT 'Identificador del mensaje',
  `Unread` int(1) NOT NULL default '1' COMMENT 'si esta leido 1 sino 0',
  `De_Id` int(11) NOT NULL COMMENT 'Id del usuario que envia',
  `Para_Id` int(11)  NOT NULL COMMENT 'Id del usuario que recibe',
  `Mensaje` text NOT NULL COMMENT 'Mensaje',
  `Fecha` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'time',
  PRIMARY KEY (`ID`),
  KEY `De_Id` (`De_Id`),
  KEY `Para_Id` (`Para_Id`),
  CONSTRAINT `fk_Menaje_De_Id` FOREIGN KEY (`De_Id`) REFERENCES `Usuario` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Menaje_Para_Id` FOREIGN KEY (`Para_Id`) REFERENCES `Usuario` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
-- --------------------------------------------------------

--
-- Table structure for table `ConcursoUsuario`
--

DROP TABLE IF EXISTS `ConcursoUsuario`;
CREATE TABLE `ConcursoUsuario` (
  `cuid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`cuid`),
  KEY `userid` (`userid`),
  KEY `cid` (`cid`),
  CONSTRAINT `fk_ConUsu_cid` FOREIGN KEY (`cid`) REFERENCES `Concurso` (`CId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ConUsu_userid` FOREIGN KEY (`userid`) REFERENCES `Usuario` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
